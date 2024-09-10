"use strict";

$(document).ready(function () {
  // var opacity;
  var current_fs, next_fs, previous_fs; //fieldsets
  // the selector will attach a click event handler on the 'next' button class

  $(".next").click(function (e) {
    // test
    e.preventDefault();
    $.validator.addMethod("numberRegex", function (value, element) {
      return this.optional(element) || /^[0-9]*$/i.test(value);
    }, "candidate number must contain only numbers"); //form validation

    var form = $("#msform");
    form.validate({
      errorElement: "span",
      errorClass: "help-block",
      highlight: function highlight(element, errorClass, validClass) {
        $(element).closest(".form-group").addClass("has-error");
        $(element).closest(".form-group").children(".form-control").css("border-color", "red");
        $(element).closest(".form-group").children(".control-label").css("color", "red");
        $(element).closest(".form-group").children(".control-label").removeClass("label_hidden");
      },
      unhighlight: function unhighlight(element, errorClass, validClass) {
        $(element).closest(".form-group").removeClass("has-error");
        $(element).closest(".form-group").children(".form-control").css("border", "1px solid grey");
        $(element).closest(".form-group").children(".control-label").css("color", "grey");
      },
      ignore: ":hidden:not(.do-not-ignore)",
      submitHandler: function submitHandler(form) {
        // $(form).find('.submitSpin').show();
        $(form).find(".action-button").hide();
        form.submit();
      },
      rules: {
        registration: {
          required: true
        },
        school_centers: {
          required: true
        },
        candidate_No: {
          required: true,
          minlength: 9,
          numberRegex: true
        },
        confirm_candidate: {
          required: true,
          minlength: 9
        },
        phone_No: {
          required: true,
          minlength: 6
        },
        email_Address: {
          required: true,
          email: true
        },
        email_Address_code: {
          required: true
        },
        confirm_email_Address_otp: {
          required: true
        }
      },
      messages: {
        school_centers: {
          required: "*school centers choice is required"
        },
        registration: {
          required: "*registration choice is required"
        },
        candidate_No: {
          required: "*candidate number is required"
        },
        confirm_candidate: {
          required: "candidate number does not existed"
        },
        phone_No: {
          required: "*phone number is required"
        },
        email_Address: {
          required: "*email is required",
          email: "*wrong email format"
        },
        email_Address_code: {
          required: "*verification code is required"
        }
      }
    }); //load if form is valid

    if (form.valid() === true) {
      current_fs = $(this).parent().parent();
      next_fs = $(this).parent().parent().next(); //Add Class Active

      $("#progressbar_content li").eq($("fieldset").index(next_fs)).addClass("active"); // show progress line move as next is being pressed

      var progressLine = document.getElementById("progress_line");
      var currWidth = progressLine.clientWidth;

      if (currWidth < 690) {
        progressLine.style.width = currWidth + 100 + "px";
      } else {
        progressLine.style.width = "760px";
      } //display next fieldset or school_route(school registration or private registration)


      next_fs.show(); //hide current fieldset

      current_fs.hide();

      if ($(this).prop("name") == "next-registration") {
        var registration = $("input:radio[name=registration]:checked").val();
        $.ajax({
          type: "POST",
          url: "server.php",
          data: $(this).serialize(),
          success: function success(response) {
            var jsonData = response; // user is logged in successfully in the back-end
            // let's redirect

            if (!(jsonData.success == "1") && registration == 1) {
              location.href = "login.php";
            }
          }
        });
      }

      if ($(this).prop("name") == "next-billing") {
        $.ajax({
          url: "server.php",
          method: "POST",
          data: $("#msform").serialize() + "&" + this.name + "=" + this.value,
          success: function success(data) {
            $("#bill").html(data);
          },
          error: function error() {
            alert("error");
          }
        });
      }
    }
  }); // the selector will attach a click event handler on the 'previous' button class

  $(".previous").click(function () {
    current_fs = $(this).parent().parent();
    previous_fs = $(this).parent().parent().prev(); //Remove class active

    $("#progressbar_content li").eq($("fieldset").index(current_fs)).removeClass("active"); // show progress line move as next is being pressed

    var progressLine = document.getElementById("progress_line");
    var currWidth = progressLine.clientWidth;

    if (currWidth <= 760) {
      progressLine.style.width = currWidth - 100 + "px";
    } //show the previous fieldset


    previous_fs.show(); //hide the current fieldset

    current_fs.hide();
  });
  $("#school_centers").keyup(function (ev) {
    ev.preventDefault();
    var query = $("#school_centers").val();

    if (query.length > 0) {
      $.ajax({
        url: "server.php",
        method: "POST",
        data: {
          search: 1,
          q: query
        },
        success: function success(data) {
          $("#response").html(data);
        },
        dataType: "text"
      });
    }
  });
  $(document).on("click", "li", function () {
    var country = $(this).text();
    $("#school_centers").val(country);
    $("#response").html("");
  }); // reset password

  $("#reset-password").on("click", function () {
    var email = $("#email-forgot-password").val();

    if (email != "") {
      $("#email-forgot-password").css("border", "1px solid green");
      $.ajax({
        url: "forgotPassword.php",
        method: "POST",
        data: {
          email: email
        },
        success: function success(data) {
          var data = $.parseJSON(data);

          if (!data.status) {
            $("#massage-response").html(data.msg).css("color", "red");
          } else {
            $("#massage-response").html(data.msg).css("color", "green");
          }
        }
      });
    } else {
      $("#email-forgot-password").css("border", "1px solid red");
    }
  });
});
var school_login_form = $("#login_form");
school_login_form.validate({
  errorElement: "span",
  errorClass: "help-block",
  highlight: function highlight(element, errorClass, validClass) {
    $(element).closest(".form-group").addClass("has-error");
    $(element).closest(".form-group").children(".form-control").css("border-color", "red");
    $(element).closest(".form-group").children(".control-label").css("color", "red");
    $(element).closest(".form-group").children(".control-label").removeClass("label_hidden");
  },
  unhighlight: function unhighlight(element, errorClass, validClass) {
    $(element).closest(".form-group").removeClass("has-error");
    $(element).closest(".form-group").children(".form-control").css("border", "1px solid grey");
    $(element).closest(".form-group").children(".control-label").css("color", "grey");
  },
  rules: {
    centre_number: {
      required: true
    },
    password: {
      required: true
    }
  },
  messages: {
    centre_number: {
      required: "*Centre number is required"
    },
    password: {
      required: "*Password is required"
    }
  }
});
$(document).ready(function () {
  //remove placeholder on personal information textfield on click
  $(".input_wait,select").each(function () {
    $(this).attr("data-placeholder", this.placeholder);
    $(this).bind("focus", function () {
      if ($(this).val().length === 0) {
        if ($(this).parent().hasClass("has-error")) {
          this.placeholder = ""; // $(this).css("border","3px solid red");
        } else {
          this.placeholder = ""; // show label associated with the input in focus

          $("label[for='" + this.id + "']").removeClass("label_hidden");
        }
      } else {
        if ($(this).parent().hasClass("has-error")) {
          this.placeholder = ""; // $(this).css("border","3px solid red");
        } else {
          this.placeholder = ""; // show label associated with the input in focus

          $("label[for='" + this.id + "']").removeClass("label_hidden");
        }
      }
    });
    $(this).bind("blur", function () {
      if ($(this).val().length === 0) {
        if ($(this).parent().hasClass("has-error")) {
          this.placeholder = $(this).attr("data-placeholder");
        } else {
          this.placeholder = $(this).attr("data-placeholder"); // hide label associated with the input in focus

          $("label[for='" + this.id + "']").addClass("label_hidden");
        }
      } else {
        if (!$(this).parent().hasClass("has-error")) {
          // this.placeholder = $(this).attr("data-placeholder");
          $(this).css("border", "1px solid grey");
          $("label[for='" + this.id + "']").css("color", "grey");

          if ($("label[for='" + this.id + "']").hasClass("label_hidden")) {
            $("label[for='" + this.id + "']").removeClass("label_hidden");
          }
        } // else{
        // 	// this.placeholder = $(this).attr("data-placeholder");
        // 	// hide label associated with the input in focus
        // 	// $("label[for='" + this.id + "']").css("color","red");
        // 	// $("label[for='" + this.id + "']").addClass("label_hidden");
        // }

      }
    });
  });

  if (!$(this).val()) {
    $(this).removeClass("error");
  } else {
    $(this).addClass("error");
  } // subject selection


  var subject_number = 0;
  var increment = 475.0;
  $(".subject").on("change", function () {
    var subj_count = subject_number += this.checked ? 1 : -1;

    if (subj_count <= 0) {
      increment = 475.0;
      $(".subject_number").text(0);
      $(".total").text("M" + 0);
      $("#total-amount input").attr("value", 0);
    } else {
      $(".subject_number").text(subj_count);
      $(".total").text("M" + (increment += this.checked ? 195 : -195));
      $("#total-amount input").attr("value", increment);
    }
  });
  $("#physical_science_core").on("change", function () {
    if ($(this).prop("checked")) {
      if ($("#physical_science_extended").prop("checked")) {
        $("#physical_science_extended").prop("checked", false);
        $(this).prop("checked", true);
      } else {
        $(this).prop("checked", true);
        $(".subject_number").text(subject_number += 1);
        $(".total").text("M" + (increment += 195));
        $("#total-amount input").attr("value", increment);
      }
    } else {
      $(this).prop("checked", false);
      var subj_count = subject_number -= 1;

      if (subj_count <= 0) {
        increment = 475.0;
        $(".subject_number").text(0);
        $(".total").text("M" + 0);
        $("#total-amount input").attr("value", 0);
      } else {
        $(".subject_number").text(subj_count);
        $(".total").text("M" + (increment -= 195));
        $("#total-amount input").attr("value", increment);
      }
    }
  });
  $("#physical_science_extended").on("change", function () {
    if ($(this).prop("checked")) {
      if ($("#physical_science_core").prop("checked")) {
        $("#physical_science_core").prop("checked", false);
        $(this).prop("checked", true);
      } else {
        $(this).prop("checked", true);
        $(".subject_number").text(subject_number += 1);
        $(".total").text("M" + (increment += 195));
        $("#total-amount input").attr("value", increment);
      }
    } else {
      $(this).prop("checked", false);
      var subj_count = subject_number -= 1;

      if (subj_count <= 0) {
        increment = 475.0;
        $(".subject_number").text(0);
        $(".total").text("M" + 0);
        $("#total-amount input").attr("value", 0);
      } else {
        $(".subject_number").text(subj_count);
        $(".total").text("M" + (increment -= 195));
        $("#total-amount input").attr("value", increment);
      }
    }
  });
  $("#maths_core").on("change", function () {
    if ($(this).prop("checked")) {
      if ($("#maths_extended").prop("checked")) {
        $("#maths_extended").prop("checked", false);
        $(this).prop("checked", true);
      } else {
        $(this).prop("checked", true);
        $(".subject_number").text(subject_number += 1);
        $(".total").text("M" + (increment += 195));
        $("#total-amount input").attr("value", increment);
      }
    } else {
      $(this).prop("checked", false);
      var subj_count = subject_number -= 1;

      if (subj_count <= 0) {
        increment = 475.0;
        $(".subject_number").text(0);
        $(".total").text("M" + 0);
        $("#total-amount input").attr("value", 0);
      } else {
        $(".subject_number").text(subj_count);
        $(".total").text("M" + (increment -= 195));
        $("#total-amount input").attr("value", increment);
      }
    }
  });
  $("#maths_extended").on("change", function () {
    if ($(this).prop("checked")) {
      if ($("#maths_core").prop("checked")) {
        $("#maths_core").prop("checked", false);
        $(this).prop("checked", true);
      } else {
        $(this).prop("checked", true);
        $(".subject_number").text(subject_number += 1);
        $(".total").text("M" + (increment += 195));
        $("#total-amount input").attr("value", increment);
      }
    } else {
      $(this).prop("checked", false);
      var subj_count = subject_number -= 1;

      if (subj_count <= 0) {
        increment = 475.0;
        $(".subject_number").text(0);
        $(".total").text("M" + 0);
        $("#total-amount input").attr("value", 0);
      } else {
        $(".subject_number").text(subj_count);
        $(".total").text("M" + (increment -= 195));
        $("#total-amount input").attr("value", increment);
      }
    }
  });
  $(".submit").click(function () {
    return false;
  }); // change jc subjects

  $("#level").on("change", function () {
    if (this.value == "LGCSE") {
      $(".JC_Subjects").hide();
      $(".LGCSE_Subjects").show();
    } else {
      $(".JC_Subjects").show();
      $(".LGCSE_Subjects").hide();
    }
  });
  $("#session").on("change", function () {
    if (this.value == "June") {
      $(".LGCSE_Subjects .form-row .centre").css("display", "none");
      $(".LGCSE_Subjects .form-row").css("display", "block");
      $(".subjects_selection .form-check").hide();
      $(".subjects_selection .june").show();
    } else {
      $(".LGCSE_Subjects .form-row .centre").css("display", "block");
      $(".LGCSE_Subjects .form-row").css("display", "flex");
      $(".subjects_selection .form-check").show();
    }
  });
}); // search student from database and display student info

$(document).on("keyup", "#candidate_No", function () {
  var search = $(this).val();

  if (search.length >= 9) {
    $.ajax({
      url: "server.php",
      method: "POST",
      data: {
        query: search
      },
      success: function success(data) {
        if (data.trim() === "Sorry Candidate Number not found") {
          // validate candate number
          $('input[name="confirm_candidate"]').addClass("do-not-ignore"); // validate otp

          $('input[name="confirm_email_Address_otp"]').addClass("do-not-ignore");
          $(".result").html("<div class='alert alert-danger' role='alert'>" + data + "</div>");
        } else {
          $('input[name="confirm_candidate"]').val(search);
          $(".result").html(data);
        }
      }
    });
  }
}); // 	// confirm otp

$(document).on("click", "#varify_email_Address_code_btn", function () {
  var otp = $("input[name=email_Address_code]").val();
  $.ajax({
    url: "server.php",
    method: "POST",
    data: {
      otp: otp
    },
    success: function success(data) {// if (data.trim() == "wrong") {
      //   $('input[name="confirm_email_Address_otp"]').val("");
      // } else {
      //   $('input[name="confirm_email_Address_otp"]').val(data.trim());
      // }
    }
  });
});
$(document).on("click", ".varify-btn", function () {
  $(".loading_icon", this).removeClass("btn_loader_hide");
  $(this).attr("disabled", true);
  $(".btn_loader_text", this).hide();
  setTimeout(function () {
    $(".loading_icon").addClass("btn_loader_hide");
    $(this).attr("disabled", false);
    $(".btn_loader_text").show();
  }, 3500);
  var email = $("#email_Address").val();
  load_varifycation_data(email);
}); // disable enter key

$(document).on("keypress", "input", function (ev) {
  if (ev.which == 13) {
    ev.preventDefault();
  }
}); // verify email

load_varifycation_data();

function load_varifycation_data(email) {
  var student_No = $("input[name=candidate_No]").val();
  var student_sname = $("input[name=surname]").val();
  var student_fname = $("input[name=other_name]").val();
  $.ajax({
    url: "server.php",
    method: "POST",
    data: {
      email: email,
      candidate_No: student_No,
      student_sname: student_sname,
      student_fname: student_fname
    },
    success: function success(data) {
      console.log(data);
      $(".varification_email").html(data);
    }
  });
}
//# sourceMappingURL=main.dev.js.map
