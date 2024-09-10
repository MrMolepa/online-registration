"use strict";

// page loader function
$(window).on("load", function () {
  setTimeout(function () {
    $("body").addClass("loaded");
  }, 1000);
}); //school user dashboard menu toggle

$(document).ready(function () {
  /********** Some Variable Initial Value **************/
  var current_fs, next_fs, previous_fs;
  /********** Some Variable Initial Value End **************/

  /********** Event Handler On Next **************/

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
        phone_No: {
          required: true,
          minlength: 6
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
        phone_No: {
          required: "*phone number is required"
        }
      }
    }); //load if form is valid

    if (form.valid() === true) {
      current_fs = $(this).parent().parent();
      next_fs = $(this).parent().parent().next(); //Add Class Active

      $("#progressbar_content li").eq($("fieldset").index(next_fs)).addClass("active"); // show progress line move as next is being pressed
      // var progressLine = document.getElementById("progress_line");
      // var currWidth = progressLine.clientWidth;
      // if (currWidth < 690) {
      //   progressLine.style.width = currWidth + 100 + "px";
      // } else {
      //   progressLine.style.width = "760px";
      // }
      //display next fieldset or school_route(school registration or private registration)

      next_fs.show(); //hide current fieldset

      current_fs.hide();
    }
  });
  /********** Event Handler On Next End **************/

  /********** Event Handler On Previus **************/

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
  }); // Get current path and find target link

  var path = window.location.pathname.split("/").pop(); // Account for home page with empty path

  if (path == "") {
    path = "index.php";
  }

  var target = $('nav a[href="' + path + '"]'); // Add active class to target link

  target.addClass("active-menu");
  $("#sideNav").click(function () {
    if ($(this).hasClass("closed")) {
      $(".navbar-side").animate({
        left: "0px"
      });
      $(this).removeClass("closed");
      $("#page-wrapper").animate({
        "margin-left": "260px"
      });
    } else {
      $(this).addClass("closed");
      $(".navbar-side").animate({
        left: "-260px"
      });
      $("#page-wrapper").animate({
        "margin-left": "0px"
      });
    }
  });
});
$(document).ready(function () {
  $().ready(function () {
    $sidebar = $(".sidebar");
    $sidebar_img_container = $sidebar.find(".sidebar-background");
    $full_page = $(".full-page");
    $sidebar_responsive = $("body > .navbar-collapse");
    window_width = $(window).width();
    fixed_plugin_open = $(".sidebar .sidebar-wrapper .nav li.active a p").html();

    if (window_width > 767 && fixed_plugin_open == "Dashboard") {
      if ($(".fixed-plugin .dropdown").hasClass("show-dropdown")) {
        $(".fixed-plugin .dropdown").addClass("open");
      }
    }

    $(".fixed-plugin a").click(function (event) {
      // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
      if ($(this).hasClass("switch-trigger")) {
        if (event.stopPropagation) {
          event.stopPropagation();
        } else if (window.event) {
          window.event.cancelBubble = true;
        }
      }
    });
    $(".fixed-plugin .active-color span").click(function () {
      $full_page_background = $(".full-page-background");
      $(this).siblings().removeClass("active");
      $(this).addClass("active");
      var new_color = $(this).data("color");

      if ($sidebar.length != 0) {
        $sidebar.attr("data-color", new_color);
      }

      if ($full_page.length != 0) {
        $full_page.attr("filter-color", new_color);
      }

      if ($sidebar_responsive.length != 0) {
        $sidebar_responsive.attr("data-color", new_color);
      }
    });
    $(".fixed-plugin .background-color .badge").click(function () {
      $(this).siblings().removeClass("active");
      $(this).addClass("active");
      var new_color = $(this).data("background-color");

      if ($sidebar.length != 0) {
        $sidebar.attr("data-background-color", new_color);
      }
    });
    $(".fixed-plugin .img-holder").click(function () {
      $full_page_background = $(".full-page-background");
      $(this).parent("li").siblings().removeClass("active");
      $(this).parent("li").addClass("active");
      var new_image = $(this).find("img").attr("src");

      if ($sidebar_img_container.length != 0 && $(".switch-sidebar-image input:checked").length != 0) {
        $sidebar_img_container.fadeOut("fast", function () {
          $sidebar_img_container.css("background-image", 'url("' + new_image + '")');
          $sidebar_img_container.fadeIn("fast");
        });
      }

      if ($full_page_background.length != 0 && $(".switch-sidebar-image input:checked").length != 0) {
        var new_image_full_page = $(".fixed-plugin li.active .img-holder").find("img").data("src");
        $full_page_background.fadeOut("fast", function () {
          $full_page_background.css("background-image", 'url("' + new_image_full_page + '")');
          $full_page_background.fadeIn("fast");
        });
      }

      if ($(".switch-sidebar-image input:checked").length == 0) {
        var new_image = $(".fixed-plugin li.active .img-holder").find("img").attr("src");
        var new_image_full_page = $(".fixed-plugin li.active .img-holder").find("img").data("src");
        $sidebar_img_container.css("background-image", 'url("' + new_image + '")');
        $full_page_background.css("background-image", 'url("' + new_image_full_page + '")');
      }

      if ($sidebar_responsive.length != 0) {
        $sidebar_responsive.css("background-image", 'url("' + new_image + '")');
      }
    });
    $(".switch-sidebar-image input").change(function () {
      $full_page_background = $(".full-page-background");
      $input = $(this);

      if ($input.is(":checked")) {
        if ($sidebar_img_container.length != 0) {
          $sidebar_img_container.fadeIn("fast");
          $sidebar.attr("data-image", "#");
        }

        if ($full_page_background.length != 0) {
          $full_page_background.fadeIn("fast");
          $full_page.attr("data-image", "#");
        }

        background_image = true;
      } else {
        if ($sidebar_img_container.length != 0) {
          $sidebar.removeAttr("data-image");
          $sidebar_img_container.fadeOut("fast");
        }

        if ($full_page_background.length != 0) {
          $full_page.removeAttr("data-image", "#");
          $full_page_background.fadeOut("fast");
        }

        background_image = false;
      }
    });
    $(".switch-sidebar-mini input").change(function () {
      $body = $("body");
      $input = $(this);

      if (md.misc.sidebar_mini_active == true) {
        $("body").removeClass("sidebar-mini");
        md.misc.sidebar_mini_active = false;
        $(".sidebar .sidebar-wrapper, .main-panel").perfectScrollbar();
      } else {
        $(".sidebar .sidebar-wrapper, .main-panel").perfectScrollbar("destroy");
        setTimeout(function () {
          $("body").addClass("sidebar-mini");
          md.misc.sidebar_mini_active = true;
        }, 300);
      } // we simulate the window Resize so the charts will get updated in realtime.


      var simulateWindowResize = setInterval(function () {
        window.dispatchEvent(new Event("resize"));
      }, 180); // we stop the simulation of Window Resize after the animations are completed

      setTimeout(function () {
        clearInterval(simulateWindowResize);
      }, 1000);
    });
  });
});
calculateSum();
$(document).ready(function () {
  /*-----------------------------------/
  /*	ADD NEW USER
  /*----------------------------------*/
  $(document).on("click", "#save-user", function () {
    var action = "addUser";
    var data = new FormData(); //Form data

    var form_data = $("#addUserForm").serializeArray();
    $.each(form_data, function (key, input) {
      data.append(input.name, input.value);
    }); //File data

    var file_data = $('#addUserForm input[name="profileImage"]')[0].files;

    for (var i = 0; i < file_data.length; i++) {
      data.append("profileImage[]", file_data[i]);
    } //Custom data


    data.append("action", action);
    $.ajax({
      url: "action.php",
      method: "POST",
      cache: false,
      contentType: false,
      processData: false,
      data: data,
      success: function success(data) {
        console.log(data);
        var data = $.parseJSON(data);
        console.log(data);

        if (data.status == 1) {
          $("#add-user").modal("hide");
          $("#addUserForm").trigger("reset");
          showPopAlert("fa-check", "Success", data.output, "Dismiss");
        } else {
          showPopAlert("fa-times", "Error", data.output, "Try again");
        } // $(".errors").html(response);
        // $("add-user").modal("show");


        displayRecord();
      }
    });
  }); //rest input when close Add user Modal

  $(document).on("click", ".resetform", function () {
    $("form").trigger("reset");
    $(".errors").html("");
  });
  /*-----------------------------------/
  /*	UPDATE NEW USER
  /*----------------------------------*/
  // get particular  Records for user

  $(document).on("click", "#btn-edit-user", function () {
    var id = $(this).attr("data-id");
    action = "get_particular_data";
    $.ajax({
      url: "action.php",
      method: "post",
      data: {
        id: id,
        action: action
      },
      success: function success(data) {
        var data = JSON.parse(data); // console.log( data);

        $('#editUserForm input[name="userid"]').val(data.userid);
        $('#editUserForm input[name="email"]').val(data.email);
        $('#editUserForm input[name="occupation"]').val(data.occupation);
        $('#editUserForm #updateRole option[value="' + data.user_type + '"]').attr("selected", "selected");
        $("#profileDisplay").attr("src", "assets/img/profile.png");

        if (data.profile_pic != "") {
          $("#profileDisplay").attr("src", "../Application/uploads/ProfilePic/" + data.profile_pic);
          $('#editUserForm input[name="imageName"]').val(data.data.profile_pic);
        }
      }
    });
  }); // update user

  $(document).on("click", "#update-user", function () {
    var action = "update";
    var data = new FormData(); //Form data

    var form_data = $("#editUserForm").serializeArray();
    $.each(form_data, function (key, input) {
      data.append(input.name, input.value);
    }); //File data

    var file_data = $('#editUserForm input[name="profileImage"]')[0].files;

    for (var i = 0; i < file_data.length; i++) {
      data.append("profileImage[]", file_data[i]);
    } //Custom data


    data.append("action", action);
    $.ajax({
      url: "action.php",
      method: "POST",
      cache: false,
      contentType: false,
      processData: false,
      data: data,
      success: function success(data) {
        var data = $.parseJSON(data);
        console.log(data);

        if (data.status == 1) {
          $("#edit-user").modal("hide");
          showPopAlert("fa-check", "Success", data.output, "Dismiss");
        } else {
          showPopAlert("fa-times", "Error", data.output, "Try again");
        }

        displayRecord();
      }
    });
  }); //update user status

  $(document).on("click", "#change-status", function () {
    var userid = $(this).data("id");
    var status = $(this).data("user_status");
    var action = "change_status";

    if (confirm("are  sure you want to change status of this user;")) {
      $.ajax({
        url: "action.php",
        method: "POST",
        data: {
          user_id: userid,
          user_status: status,
          action: action
        },
        success: function success(data) {
          var data = JSON.parse(data);
          $("#change-status").html(data.user_status);
          displayRecord();
        }
      });
    } else {
      return false;
    }
  });
  /*-----------------------------------/
  /*DISPLAY ALL USERS
  /*----------------------------------*/

  displayRecord(); // display  records

  function displayRecord() {
    var action = "view";
    $.ajax({
      url: "action.php",
      method: "post",
      data: {
        action: action
      },
      success: function success(data) {
        console.log(data);
        var data = $.parseJSON(data);

        if (data.status == "success") {
          $("#table-view").html(data.table);
        }
      }
    });
  }
  /*-----------------------------------/
  /*NOTIFICATIONS
  /*----------------------------------*/


  loadUnseenNotification();
  setInterval(function () {
    loadUnseenNotification();
  }, 2000); // load unseen notification

  function loadUnseenNotification() {
    var view = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
    var action = "get_notification";
    $.ajax({
      url: "action.php",
      method: "post",
      data: {
        action: action,
        view: view
      },
      success: function success(data) {
        var data = $.parseJSON(data);
        $(".notifications").html(data.notification);

        if (data.unseen_notification > 0) {
          $(".unseen").html(data.unseen_notification);
        }
      }
    });
  } //clear notification


  $(document).on("click", "#seen-notifications", function () {
    $(".unseen").html(" ");
    $(".unseen").hide();
    loadUnseenNotification("yes");
  });
  /*-----------------------------------/
  /*UPDATE PASSWORD
  /*----------------------------------*/
  // set user id

  $(document).on("click", "#btn-edit-user-password", function () {
    var id = $(this).attr("data-id");
    $('#changePasswordForm input[name="userid"]').val(id);
  }); // change Password

  $(document).on("click", "#btn_change_pasword", function () {
    var form = $("#changePasswordForm").serialize();
    var action = "change_password";
    $.ajax({
      url: "action.php",
      method: "post",
      data: form + "&action=" + action,
      success: function success(data) {
        $("#change-password .errors").html(data);
        $("#change-password").modal("show");
        displayRecord();
        $("#changePasswordForm").trigger("reset");
      }
    });
  });
  /*-----------------------------------/
  /*	PROFILE PICTURE
  /*----------------------------------*/
  // adding user trigger event

  $(document).on("click", "#AddprofileDisplay", function () {
    $('#addUserForm input[name="profileImage"]').trigger("click");
  });
  $(document).on("change", '#addUserForm  input[name="profileImage"]', function () {
    readURL(this, "#AddprofileDisplay");
  }); // updating user trigger event

  $(document).on("click", "#profileDisplay", function () {
    $('#editUserForm input[name="profileImage"]').trigger("click");
  });
  $(document).on("change", '#editUserForm input[name="profileImage"]', function () {
    readURL(this, "#profileDisplay");
  }); // updating login user trigger event

  $(document).on("click", "#LogInprofile", function () {
    $('#profileform input[name="profileImage"]').trigger("click");
  });
  $(document).on("change", '#profileform input[name="profileImage"]', function () {
    readURL(this, "#LogInprofile");
  }); //  read url image  triger event for edit image

  function readURL(input, display) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        $(display).attr("src", e.target.result);
      };

      reader.readAsDataURL(input.files[0]);
    }
  }
  /*-----------------------------------/
  /*Diplay candidates
  /*----------------------------------*/

  /********** Some Variable Initial Value **************/


  var candidates_filter = $("#candidates_filter").val();
  var candidates_sort = $("#candidates_sort").val();
  var page = 1;
  var search_txt = "";
  /**********  Candidates Sorting Start    **************/

  $("#candidates_sort").on("change", function () {
    var candidates_sort = $(this).val();
    load_candidates("displayAllCandidates", page, candidates_filter, candidates_sort, search_txt);
    load_candidates("displayAllCandidatesInformation", page, candidates_filter, candidates_sort, search_txt);
  });
  /**********  Candidates Sorting End    **************/

  /**********  Candidates Main Search Start    **************/

  $("#search_txt").keyup(function () {
    var search = $(this).val();
    load_candidates("displayAllCandidates", page, candidates_filter, candidates_sort, search);
    load_candidates("displayAllCandidatesInformation", page, candidates_filter, candidates_sort, search);
  });
  /**********  Candidates Main Search End   **************/

  /*****  Retrieve Value When Page First Load  *******/

  load_candidates("displayAllCandidates", page, candidates_filter, candidates_sort, search_txt);
  load_candidates("displayAllCandidatesInformation", page, candidates_filter, candidates_sort, search_txt);
  /****  AJAX Main Function Who Perform All Tasks Start *******/

  function load_candidates(action, page, candidates_filter, candidates_sort, search_txt) {
    $.ajax({
      url: "action.php",
      method: "POST",
      data: {
        action: action,
        page: page,
        candidates_filter: candidates_filter,
        candidates_sort: candidates_sort,
        search_txt: search_txt
      },
      success: function success(data) {
        var data = JSON.parse(data);

        if (data.status == 1) {
          $(".amendcandidates").html(data.table);
        } else {
          $(".candidateInfo").html(data.table);
        }
      }
    });
  }
  /****  AJAX Main Function Who Perform All Tasks End *******/
  // save updates row


  $("#save").click(function () {
    $.ajax({
      url: "edit.php",
      method: "POST",
      data: $("#edit-form").serialize() + "&" + this.name + "=" + this.value,
      success: function success(data) {
        calculateSum();
        load_candidates("displayAllCandidates", page, candidates_filter, candidates_sort, search_txt);
        showNotification("bottom", "left", "Candidate updated successfully", "success");
      }
    });
  });
}); // upload js

$("#fileup").change(function () {
  //here we take the file extension and set an array of valid extensions
  var res = $("#fileup").val();
  var arr = res.split("\\");
  var filename = arr.slice(-1)[0];
  filextension = filename.split(".");
  filext = "." + filextension.slice(-1)[0];
  valid = [".csv"]; //if file is not valid we show the error icon, the red alert, and hide the submit button

  if (valid.indexOf(filext.toLowerCase()) == -1) {
    $(".imgupload").hide("slow");
    $(".imgupload.ok").hide("slow");
    $(".imgupload.stop").show("slow");
    $("#namefile").css({
      color: "red",
      "font-weight": 700
    });
    $("#namefile").html("File " + filename + " is not  CSV!");
    $("#submitbtn").hide();
    $("#fakebtn").show();
  } else {
    //if file is valid we show the green alert and show the valid submit
    $(".imgupload").hide("slow");
    $(".imgupload.stop").hide("slow");
    $(".imgupload.ok").show("slow");
    $("#namefile").css({
      color: "green",
      "font-weight": 700
    });
    $("#namefile").html(filename);
    $("#submitbtn").show();
    $("#fakebtn").hide();
  }
}); // delete

function deleteAjax(id) {
  if (confirm("are You sure delete this candidate ?")) {
    $.ajax({
      type: "post",
      url: "delete.php",
      data: {
        delete_id: id
      },
      success: function success(data) {
        $("#delete" + id).hide();
        showNotification("bottom", "left", "Candidate deleted successfully", "danger");
        calculateSum();
      }
    });
  }
} // edit inline


function activate(element) {
  $(element).attr("class", "activete");
}

function updateValue(element, column, id) {
  var value = element.innerText;
  $.ajax({
    url: "edit.php",
    method: "post",
    data: {
      value: value,
      column: column,
      id: id
    },
    success: function success(php_result) {
      $(element).removeAttr("class");
    }
  });
} // Edit row


function update(element) {
  var id = $(element).attr("data-id");
  var update_btn = $(element).attr("update");
  $("#subjects").html(" ");
  $.ajax({
    url: "edit.php",
    cache: false,
    method: "post",
    data: {
      candidate_no: id,
      update_btn: update_btn
    },
    success: function success(response) {
      var data = JSON.parse(response);
      var all_subjects = data.all_subjects;
      var subjects = data.joined_result[0].subjects.split(",");
      var flag = 0;
      var maths_optionA = 0;
      var physics_optionA = 0;
      var maths_optionB = 0;
      var physics_optionB = 0; //  loop to all subjects

      $.each(all_subjects, function (index_all_sujects, value) {
        // loop to all students subject
        $.each(subjects, function (index, subject_value) {
          var code = subject_value.split(" "); //    loop and split array code and option

          if (code[0] == parseInt(value.subject_code)) {
            flag = 1;

            if (code[1] == "B" && value.subject_code == "0178") {
              maths_optionB = 1;
            } else if (code[1] == "B" && value.subject_code == "0181") {
              physics_optionB = 1;
            } else if (code[1] == "A" && value.subject_code == "0178") {
              maths_optionA = 1;
            } else if (code[1] == "A" && value.subject_code == "0181") {
              physics_optionA = 1;
            }
          }
        });

        if (flag == 1) {
          if (maths_optionB == 1) {
            addAllInputs("subjects", value.subject_name + " Core", value.subject_name, "radio", value.subject_code + " A", " ");
            addAllInputs("subjects", value.subject_name + " Extended", value.subject_name, "radio", value.subject_code + " B", "checked");
            maths_optionB = 0;
          } else if (physics_optionB == 1) {
            addAllInputs("subjects", value.subject_name + " Core", value.subject_name, "radio", value.subject_code + " A", " ");
            addAllInputs("subjects", value.subject_name + " Extended", value.subject_name, "radio", value.subject_code + " B", "checked");
            physics_optionB = 0;
          } else if (maths_optionA == 1) {
            addAllInputs("subjects", value.subject_name + " Extended", value.subject_name, "radio", value.subject_code + " B", " ");
            addAllInputs("subjects", value.subject_name + " Core", value.subject_name, "radio", value.subject_code + " A", "checked");
            maths_optionA = 0;
          } else if (physics_optionA == 1) {
            addAllInputs("subjects", value.subject_name + " Extended", value.subject_name, "radio", value.subject_code + " B", " ");
            addAllInputs("subjects", value.subject_name + " Core", value.subject_name, "radio", value.subject_code + " A", "checked");
            physics_optionA = 0;
          } else {
            addAllInputs("subjects", value.subject_name, "Subjects[]", "checkbox", value.subject_code + " A", "checked");
          }

          flag = 0;
        } else {
          if (value.subject_code == 178) {
            addAllInputs("subjects", value.subject_name + " Extended", value.subject_name, "radio", value.subject_code, " ");
            addAllInputs("subjects", value.subject_name + " Core", value.subject_name, "radio", value.subject_code + " A", " ");
          } else if (value.subject_code == 181) {
            addAllInputs("subjects", value.subject_name + " Extended", value.subject_name, "radio", value.subject_code + " B", " ");
            addAllInputs("subjects", value.subject_name + " Core", value.subject_name, "radio", value.subject_code + " A", " ");
          } else {
            addAllInputs("subjects", value.subject_name, "Subjects[]", "checkbox", value.subject_code + " A", " ");
          }
        }
      });
      $('input[name="candidate_number"]').val(id);
      $('input[name="surname"]').val(data.joined_result[0].candidate_surname);
      $('input[name="other_name"]').val(data.joined_result[0].candidate_other_name);
      $('input[name="gender"]').val(data.joined_result[0].gender);
      $('input[name="date_of_birth"]').val(data.joined_result[0].date_of_birth);
      $('input[name="type"]').val(data.joined_result[0].type);
      $('input[name="sponser"]').val(data.joined_result[0].sponser);
    }
  });
} // add input field


function addAllInputs(divName, label) {
  var name = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : "";
  var inputType = arguments.length > 3 ? arguments[3] : undefined;
  var value = arguments.length > 4 ? arguments[4] : undefined;
  var checked = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : null;
  var newdiv = document.createElement("div");
  newdiv.className += "col-md-4";

  switch (inputType) {
    case "text":
      newdiv.innerHTML = "\n\t\t\t\t\t\t\t\t<div class=\"form-group\">\n\t\t\t\t\t\t\t\t<input type=\"text\" name=\"".concat(name, "\" value=\"").concat(value, "\">\n\t\t\t\t\t\t\t\t<label for=\"\">").concat(label, " </label>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t");
      break;

    case "radio":
      newdiv.innerHTML = " \n\t\t\t\t\t\t\t\t<div class=\"form-group\">\n\t\t\t\t\t\t\t\t<input type=\"radio\" name=\"".concat(name, "\" ").concat(checked, " value=\"").concat(value, "\">\n\t\t\t\t\t\t\t\t<label>").concat(label, " </label>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t\n\t\t\t\t\t\t\t\t");
      break;

    case "checkbox":
      newdiv.innerHTML = "\n\t\t\t            \n\t\t\t\t\t\t\t<div class=\"form-group\">\n\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"".concat(name, "\" ").concat(checked, " value=\"").concat(value, "\">\n\t\t\t\t\t\t\t<label>").concat(label, " </label>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\n\t\t\t\t\t\t\t");
      break;

    case "textarea":
      newdiv.innerHTML = " \n\t\t\t\t\t\t\t<div class=\"form-group\">\n\t\t\t\t\t\t\t<input type=\"textarea\" name=\"".concat(name, "\"  value=\"").concat(value, "\">\n\t\t\t\t\t\t\t<label>").concat(label, " </label>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t");
      break;

    default:
      newdiv.innerHTML = " ";
      break;
  }

  document.getElementById(divName).appendChild(newdiv);
} // calculate and get total amount and total number of student


function calculateSum() {
  $.ajax({
    url: "count_amount.php",
    success: function success(data) {
      var data = JSON.parse(data);
      $(".total-amount").html("M " + parseFloat(data.total_amount).toFixed(2));
      $(".student-number").html(data.student_number + " <small>  Candidates</small>");
      $(".sponsor").html("  NMDS   <span>  M" + parseFloat(data.sponsor[0]).toFixed(2) + " </span>    MoET   <span>  M" + parseFloat(data.sponsor[1]).toFixed(2) + " </span>   OTHER  <span>  M" + parseFloat(data.sponsor[2]).toFixed(2) + " </span>");
    }
  });
}

function showNotification(from, align, message, color) {
  // type = ['', 'info', 'danger', 'success', 'warning', 'rose', 'primary'];
  $.notify({
    icon: "add_alert",
    message: message
  }, {
    type: color,
    timer: 1000,
    placement: {
      from: from,
      align: align
    }
  });
} //


function showPopAlert(icon, tittle, description, btn) {
  $(".popup").show();

  if (tittle == "Error") {
    $(".popup .popup-icon i").removeClass("fa-check");
    $(".popup .popup-icon i").addClass("fa-times").css("color", "#dc3545");
    $(".popup .popup-icon").css({
      border: "2px solid #dc3545"
    });
    $(".popup .popup-icon").css({
      color: "#dc3545"
    });
  } else {
    $(".popup .popup-icon").css({
      border: "2px solid #34f234"
    });
    $(".popup .popup-icon").css({
      color: "#34f234"
    });
  }

  $(".popup-title").html(tittle);
  $(".popup-description").html(description);
  $(".popup-dismiss-btn button").html(btn);
  $(".popup").css({
    opacity: "1",
    top: "50%",
    transform: "translate(-50%,-50%) scale(1)",
    transition: "transform 300ms cubic-bezier(0.18,0.89,0.43,1.19)"
  });
  $(".popup-icon").css({
    transition: "all 300ms ease-in-out 250ms"
  });
  $(".popup-title").css({
    transition: "all 300ms ease-in-out 300ms"
  });
  $(".popup-description").css({
    transition: "all 300ms ease-in-out 350ms"
  });
  $(".popup-dismiss-btn").css({
    transition: "all 300ms ease-in-out 400ms"
  });
  $(".popup-overlay").css({
    visibility: "visible",
    opacity: "1"
  });
} // close alert


$(document).on("click", "#dismiss-popup-btn", function () {
  $(".popup").hide();
  $(".popup-overlay").css({
    visibility: " hidden",
    opacity: "0"
  });
});
$(document).on("click", ".popup-overlay", function (ev) {
  $(".popup").hide();
  $(".popup-overlay").css({
    visibility: " hidden",
    opacity: "0"
  });
  ev.stopPropagation();
});
//# sourceMappingURL=main.dev.js.map
