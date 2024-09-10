"use strict";

// page loader function
$(window).on("load", function () {
  setTimeout(function () {
    $("body").addClass("loaded");
  }, 1000);
});
$(document).ready(function () {
  /*-----------------------------------/
  /*	TOP NAVIGATION AND LAYOUT
  /*----------------------------------*/
  // Get current path and find target link
  var path = window.location.pathname.split("/").pop(); // Account for home page with empty path

  if (path == "") {
    path = "index.php";
  }

  var target = $('nav a[href="' + path + '"]'); // Add active class to target link

  target.addClass("active");
  $(".btn-toggle-fullwidth").on("click", function () {
    if (!$("body").hasClass("layout-fullwidth")) {
      $("body").addClass("layout-fullwidth");
    } else {
      $("body").removeClass("layout-fullwidth");
      $("body").removeClass("layout-default"); // also remove default behaviour if set
    }

    $(this).find(".lnr").toggleClass("lnr-arrow-left-circle lnr-arrow-right-circle");

    if ($(window).innerWidth() < 1025) {
      if (!$("body").hasClass("offcanvas-active")) {
        $("body").addClass("offcanvas-active");
      } else {
        $("body").removeClass("offcanvas-active");
      }
    }
  });
  $(window).on("load", function () {
    if ($(window).innerWidth() < 1025) {
      $(".btn-toggle-fullwidth").find(".icon-arrows").removeClass("icon-arrows-move-left").addClass("icon-arrows-move-right");
    } // adjust right sidebar top position


    $(".right-sidebar").css("top", $(".navbar").innerHeight()); // if page has content-menu, set top padding of main-content

    if ($(".has-content-menu").length > 0) {
      $(".navbar + .main-content").css("padding-top", $(".navbar").innerHeight());
    } // for shorter main content


    if ($(".main").height() < $("#sidebar-nav").height()) {
      $(".main").css("min-height", $("#sidebar-nav").height());
    }
  });
  /*-----------------------------------/
  /*	SIDEBAR NAVIGATION
  /*----------------------------------*/

  $('.sidebar a[data-toggle="collapse"]').on("click", function () {
    if ($(this).hasClass("collapsed")) {
      $(this).addClass("active");
    } else {
      $(this).removeClass("active");
    }
  });

  if ($(".sidebar-scroll").length > 0) {
    $(".sidebar-scroll").slimScroll({
      height: "95%",
      wheelStep: 2
    });
  }
  /*-----------------------------------/
  /*	PANEL FUNCTIONS
  /*----------------------------------*/
  // panel remove


  $(".panel .btn-remove").click(function (e) {
    e.preventDefault();
    $(this).parents(".panel").fadeOut(300, function () {
      $(this).remove();
    });
  }); // panel collapse/expand

  var affectedElement = $(".panel-body");
  $(".panel .btn-toggle-collapse").clickToggle(function (e) {
    e.preventDefault(); // if has scroll

    if ($(this).parents(".panel").find(".slimScrollDiv").length > 0) {
      affectedElement = $(".slimScrollDiv");
    }

    $(this).parents(".panel").find(affectedElement).slideUp(300);
    $(this).find("i.lnr-chevron-up").toggleClass("lnr-chevron-down");
  }, function (e) {
    e.preventDefault(); // if has scroll

    if ($(this).parents(".panel").find(".slimScrollDiv").length > 0) {
      affectedElement = $(".slimScrollDiv");
    }

    $(this).parents(".panel").find(affectedElement).slideDown(300);
    $(this).find("i.lnr-chevron-up").toggleClass("lnr-chevron-down");
  });
  /*-----------------------------------/
  /*	PANEL SCROLLING
  /*----------------------------------*/

  if ($(".panel-scrolling").length > 0) {
    $(".panel-scrolling .panel-body").slimScroll({
      height: "430px",
      wheelStep: 2
    });
  }

  if ($("#panel-scrolling-demo").length > 0) {
    $("#panel-scrolling-demo .panel-body").slimScroll({
      height: "175px",
      wheelStep: 2
    });
  }
  /*-----------------------------------/
  /*	TODO LIST
  /*----------------------------------*/


  $(".todo-list input").change(function () {
    if ($(this).prop("checked")) {
      $(this).parents("li").addClass("completed");
    } else {
      $(this).parents("li").removeClass("completed");
    }
  });
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

    var file_data = $('input[name="profileImage"]')[0].files;

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
        $(".errors").html(data);
        $("add-user").modal("show");
        $("#addUserForm").trigger("reset");
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
        $('#editUserForm #updateRole option[value="Moderator"]').attr("selected", "selected");
        $("#profileDisplay").attr("src", "assets/img/profile.png");
        $('#editUserForm #updateRole option[value="' + data.user_type + '"]').attr("selected", "selected");

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
        $("#edit-user .errors").html(data);
        $("#editUserForm").trigger("reset");
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

  displayRecord(); // call display records method and   set interval for 2 seconds

  setInterval(function () {
    displayRecord();
  }, 2000); // display  records

  function displayRecord() {
    var action = "view";
    $.ajax({
      url: "action.php",
      method: "post",
      data: {
        action: action
      },
      success: function success(data) {
        var data = $.parseJSON(data);

        if (data.status == "success") {
          $("#ECoL_user").html(data.tableECoL);
          $("#LGCSE_user").html(data.tableLGCSE);
        }
      }
    });
  }
  /*-----------------------------------/
  /*DISPLAY  TIMETABLE
  /*----------------------------------*/


  displayTimetable();

  function displayTimetable() {
    var action = "view-timetable";
    $.ajax({
      url: "action.php",
      method: "post",
      data: {
        action: action
      },
      success: function success(data) {
        var data = $.parseJSON(data);
        $("#lgcse_timetable").html(data.table);
      }
    });
  } // edit timetable


  $(document).on("click", ".editBtn", function () {
    //hide edit span
    $(this).closest("tr").find(".editSpan").hide(); //show edit input

    $(this).closest("tr").find(".editInput").show(); //hide edit button

    $(this).closest("tr").find(".editBtn").hide(); //show edit button

    $(this).closest("tr").find(".saveBtn").show();
  }); // save changes timetable

  $(document).on("click", ".saveBtn", function () {
    var trObj = $(this).closest("tr");
    var ID = $(this).closest("tr").attr("id");
    var inputData = $(this).closest("tr").find(".editInput").serialize();
    $.ajax({
      type: "POST",
      url: "action.php",
      dataType: "json",
      data: "action=editTimetable&id=" + ID + "&" + inputData,
      success: function success(response) {
        console.log(response);
        trObj.find(".editInput").hide();
        trObj.find(".saveBtn").hide();
        trObj.find(".editSpan").show();
        trObj.find(".editBtn").show();
        displayTimetable();
      }
    });
  });
  /*-----------------------------------/
  /*DISPLAY  REGISTERED SUBJECTS
  /*----------------------------------*/

  registered_subjects(); // display registered subjects;

  function registered_subjects() {
    var action = "view_registered_subjects";
    $.ajax({
      url: "action.php",
      method: "post",
      data: {
        action: action
      },
      success: function success(response) {
        var response = $.parseJSON(response);
        var data = {
          labels: ["ENGLISH LANGUAGE", "SESOTHO", "MATHEMATICS(CORE)", "MATHEMATICS(EXTENDED)", "BIOLOGY", "PHYSICAL SCIENCE(CORE)", "PHYSICAL SCIENCE(EXTENDED)", "DEVELOPMENT STUDIES", "GEOGRAPHY", "HISTORY ", "LITERATURE IN ENGLISH ", "RELIGIOUS STUDIES ", "TRAVEL & TOURISM", "ECONOMICS", "AGRICULTURE ", "ACCOUNTING", "DESIGN AND TECHNOLOGY", "FISHION AND TEXTILE", "FOOD AND NUTRITION", "ICT", "BUSINESS STUDIES"],
          datasets: [{
            label: "# of Candidates",
            data: [response.ENG, response.SESOTHO, response.MATHS_CORE, response.MATHS_EXT, response.BIOL, response.PHYS_CORE, response.PHYS_EXT, response.DS, response.GEO, response.HIS, response.LIT, response.REL, response.TRAV, response.ECO, response.AGRIC, response.ACC, response.DT, response.FT, response.FN, response.ICT, response.BS],
            backgroundColor: ["rgba(255, 99, 132, 0.2)", "rgba(54, 162, 235, 0.2)", "rgba(255, 206, 86, 0.2)", "rgba(75, 192, 192, 0.2)", "rgba(153, 102, 255, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)", "rgba(255, 159, 64, 0.2)"],
            borderColor: ["rgba(255, 99, 132, 1)", "rgba(54, 162, 235, 1)", "rgba(255, 206, 86, 1)", "rgba(75, 192, 192, 1)", "rgba(153, 102, 255, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)", "rgba(255, 159, 64, 1)"],
            borderWidth: 2
          }]
        };
        var myChart = new Chart($("#registered_subjects"), {
          type: "bar",
          data: data,
          options: {
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true
                }
              }],
              xAxes: [{
                ticks: {
                  fontSize: 10
                }
              }]
            }
          }
        });
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
  /*LOGS SETTINGS
  /*----------------------------------*/
  // logs

  $(".switch3 input").on("change", function () {
    var dad = $(this).parent();

    if ($(this).is(":checked")) {
      dad.addClass("switch3-checked");
      activateLogs("OFF");
    } else {
      dad.removeClass("switch3-checked");
      activateLogs("ON");
    }
  }); // update log

  function activateLogs() {
    var option = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "";
    var action = "activate logs";

    if (confirm("are  sure you want to change status of logs ?")) {
      $.ajax({
        url: "action.php",
        method: "post",
        data: {
          action: action,
          option: option
        },
        success: function success(data) {// console.log(data);
          // // $(".switch3 input").prop("checked", false);
        }
      });
    } else {
      return false;
    }
  } // view  log status


  viewLogs();

  function viewLogs() {
    var action = "view log status";
    $.ajax({
      url: "action.php",
      method: "post",
      data: {
        action: action
      },
      success: function success(data) {
        var data = $.parseJSON(data);
        var dad = $(".switch3 input").parent();

        if (data.business_logs_status == "active") {
          dad.addClass("switch3-checked");
          $(".switch3 input").prop("checked", true);
        } else {
          dad.addClass("switch3-checked");
          $(".switch3 input").prop("checked", false);
        }
      }
    });
  } // display logs to datatable


  $("#logsTable").DataTable();
  /*-----------------------------------/
  /*	DISPLAY ALL CENTRES TO DATATABLE
  /*----------------------------------*/

  $("#centresTable").DataTable();
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
}); // toggle function

$.fn.clickToggle = function (f1, f2) {
  return this.each(function () {
    var clicked = false;
    $(this).bind("click", function () {
      if (clicked) {
        clicked = false;
        return f2.apply(this, arguments);
      }

      clicked = true;
      return f1.apply(this, arguments);
    });
  });
};
//# sourceMappingURL=main.dev.js.map
