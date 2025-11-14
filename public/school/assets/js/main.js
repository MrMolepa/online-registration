/*	REGISTRESTION MULTISTEP
  /*----------------------------------*/

/********** Page loader function**************/
$(window).on("load", function () {
    setTimeout(function () {
        $("body").addClass("loaded");
    }, 1000);
});

/********** Page loader function End**************/

$(document).ready(function () {
    /*-----------------------------------/
	/*	ACTIVE LINK SIDENAV
  /*----------------------------------*/
    /**********  Get current path and find target link **************/
    var path = window.location.pathname.split("/").pop();

    // Account for home page with empty path
    if (path == "") {
        path = "index.php";
    }
    var target = $('nav a[href="' + path + '.php"]');
    // Add active class to target link
    target.addClass("active-menu");
    /**********  Get current path and find target link end **************/

    $(".alert-danger").delay(1800000).fadeOut("slow");
    /*-----------------------------------/
	/*	REGISTRESTION MULTISTEP PAYMENT
  /*----------------------------------*/
    /********** Some Variable Initial Value **************/
    var current_fs, next_fs, previous_fs;
    /********** Some Variable Initial Value End **************/

    /********** Event Handler On Next **************/
    $(".next").click(function (e) {
        // test
        e.preventDefault();

        $.validator.addMethod("minStrict", function (value, el, param) {
            return value > param;
        });

        $.validator.addMethod("allRequired", function (value, elem,param) {
            // Use the name to get all the inputs and verify them
            var name = elem.name;
            return $('input[name="' + name + '"]').map(function (i, obj) {
                return $(obj).val();
            }).get().every(function (v) {
                return v;
            })==param ?true:false ;

        });




        //form validation
        var form = $("#msform");
        form.validate({
            errorElement: "span",
            errorClass: "help-block",
            highlight: function (element, errorClass, validClass) {
                $(element).closest(".form-group").addClass("has-error");
                $(element)
                    .closest(".form-group")
                    .children(".form-control")
                    .css("border-color", "red");
                $(element)
                    .closest(".form-group")
                    .children(".control-label")
                    .css("color", "red");
                $(element)
                    .closest(".form-group")
                    .children(".control-label")
                    .removeClass("label_hidden");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).closest(".form-group").removeClass("has-error");
                $(element)
                    .closest(".form-group")
                    .children(".form-control")
                    .css("border", "1px solid grey");
                $(element)
                    .closest(".form-group")
                    .children(".control-label")
                    .css("color", "grey");
            },
            ignore: [],
            ignore: ":hidden:not(.do-not-ignore)",
            submitHandler: function (form) {
                // $(form).find('.submitSpin').show();
                $(form).find(".action-button").hide();
                form.submit();
            },
            rules: {
                amount: {
                    required: true,
                    number: true,
                    minStrict: 100,
                },
                email_address: {
                    required: true,
                    email: true,
                },


                'candidate_no[]': {
                    required: function (element) {
                        return $("[name='all_candidates']").prop("checked")? false :true;
                    },
                    "allRequired": function (element) {
                        return $("[name='all_candidates']").prop("checked")? false:true ;
                    },
                }
            },
            messages: {
                amount: {
                    required: "*Amount is required",
                    number: "please enter valide amount",
                    minStrict: "please enter valide amount",
                },
                email_address: {
                    required: "*email address  is required",
                },
                'candidate_no[]': {
                    required: "Please select the candidate",
                    "allRequired": "*Please select a the candidate",
                },
            },
        });

        //load if form is valid
        if (form.valid() === true) {
            current_fs = $(this).parent().parent();
            next_fs = $(this).parent().parent().next();
            formUpdate();

            //Remove Class Active
            $("#progressbar_content li")
                .eq($("fieldset").index(next_fs) - 1)
                .removeClass("active");

            //Add Class Active
            $("#progressbar_content li")
                .eq($("fieldset").index(next_fs))
                .addClass("active");

            //display next fieldset or school_route(school registration or private registration)
            next_fs.show();

            //hide current fieldset
            current_fs.hide();
        }
    });
    /********** Event Handler On Next End **************/

    /********** Event Handler On Previus **************/
    $(".previous").click(function () {
        current_fs = $(this).parent().parent();
        previous_fs = $(this).parent().parent().prev();

        //Remove class active
        $("#progressbar_content li")
            .eq($("fieldset").index(current_fs))
            .removeClass("active");

        //Add Class Active
        $("#progressbar_content li")
            .eq($("fieldset").index(current_fs) - 1)
            .addClass("active");

        //show the previous fieldset
        previous_fs.show();

        //hide the current fieldset
        current_fs.hide();
    });

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

    function formUpdate() {
        var email_Address = $("input[name='email_address']").val();
        var totalAmount = $("input[name='amount']").val();
        var totalAmount1 = totalAmount * 100;

        $("#Ecom_BillTo_Online_Email").val(email_Address);
        $("#Transaction_Amount").val(totalAmount1);
        $("#Transaction_LineItems_Amount_1").val(totalAmount1);
        $("#Lite_Order_LineItems_Amount_1").val(totalAmount1);
        $("#Lite_Order_Amount").val(totalAmount1);
        $("#Lite_Order_LineItems_Amount_1").val(totalAmount1);
        $("#Lite_Order_LineItems_Product_1").val("Total Amount paid ");
    }


});

$(document).ready(function () {
    $().ready(function () {
        $sidebar = $(".sidebar");

        $sidebar_img_container = $sidebar.find(".sidebar-background");

        $full_page = $(".full-page");

        $sidebar_responsive = $("body > .navbar-collapse");

        window_width = $(window).width();

        fixed_plugin_open = $(
            ".sidebar .sidebar-wrapper .nav li.active a p"
        ).html();

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

            if (
                $sidebar_img_container.length != 0 &&
                $(".switch-sidebar-image input:checked").length != 0
            ) {
                $sidebar_img_container.fadeOut("fast", function () {
                    $sidebar_img_container.css(
                        "background-image",
                        'url("' + new_image + '")'
                    );
                    $sidebar_img_container.fadeIn("fast");
                });
            }

            if (
                $full_page_background.length != 0 &&
                $(".switch-sidebar-image input:checked").length != 0
            ) {
                var new_image_full_page = $(
                        ".fixed-plugin li.active .img-holder"
                    )
                    .find("img")
                    .data("src");

                $full_page_background.fadeOut("fast", function () {
                    $full_page_background.css(
                        "background-image",
                        'url("' + new_image_full_page + '")'
                    );
                    $full_page_background.fadeIn("fast");
                });
            }

            if ($(".switch-sidebar-image input:checked").length == 0) {
                var new_image = $(".fixed-plugin li.active .img-holder")
                    .find("img")
                    .attr("src");
                var new_image_full_page = $(
                        ".fixed-plugin li.active .img-holder"
                    )
                    .find("img")
                    .data("src");

                $sidebar_img_container.css(
                    "background-image",
                    'url("' + new_image + '")'
                );
                $full_page_background.css(
                    "background-image",
                    'url("' + new_image_full_page + '")'
                );
            }

            if ($sidebar_responsive.length != 0) {
                $sidebar_responsive.css(
                    "background-image",
                    'url("' + new_image + '")'
                );
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
                $(".sidebar .sidebar-wrapper, .main-panel").perfectScrollbar(
                    "destroy"
                );

                setTimeout(function () {
                    $("body").addClass("sidebar-mini");

                    md.misc.sidebar_mini_active = true;
                }, 300);
            }

            // we simulate the window Resize so the charts will get updated in realtime.
            var simulateWindowResize = setInterval(function () {
                window.dispatchEvent(new Event("resize"));
            }, 180);

            // we stop the simulation of Window Resize after the animations are completed
            setTimeout(function () {
                clearInterval(simulateWindowResize);
            }, 1000);
        });
    });
});

$(document).ready(function () {
    /*-----------------------------------/
	/*	PROFILE PICTURE
  /*----------------------------------*/
    // adding user trigger event
    $(document).on("click", "#AddprofileDisplay", function () {
        $('#addUserForm input[name="profileImage"]').trigger("click");
    });
    $(document).on(
        "change",
        '#addUserForm  input[name="profileImage"]',
        function () {
            readURL(this, "#AddprofileDisplay");
        }
    );

    // updating user trigger event
    $(document).on("click", "#profileDisplay", function () {
        $('#editUserForm input[name="profileImage"]').trigger("click");
    });
    $(document).on(
        "change",
        '#editUserForm input[name="profileImage"]',
        function () {
            readURL(this, "#profileDisplay");
        }
    );

    // updating login user trigger event
    $(document).on("click", "#LogInprofile", function () {
        $('#profileform input[name="profileImage"]').trigger("click");
    });
    $(document).on(
        "change",
        '#profileform input[name="profileImage"]',
        function () {
            readURL(this, "#LogInprofile");
        }
    );

    //  read url image  triger event for edit image
    function readURL(input, display) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(display).attr("src", e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // check all

    // ID selector on Master Checkbox
    var checkedAll = "#select-all",
        checkedItems = "[name='candidates[]']";

    $(document).on("change", checkedAll, function () {
        $(checkedItems).prop("checked", $(this).prop("checked"));
        console.log($(this).prop("checked"));
    });
    $(document).on("click", checkedItems, function () {
        let inputs = $(checkedItems).length;
        let inputs_checked = $(checkedItems + ":checked").length;
        console.log(inputs);
        console.log(inputs_checked);

        if (inputs_checked <= 0) {
            $(checkedAll).prop("checked", false);
            $(checkedAll).prop("indeterminate", null);
        } else if (inputs == inputs_checked) {
            $(checkedAll).prop("checked", true);
            $(checkedAll).prop("indeterminate", false);
        } else {
            $(checkedAll).prop("checked", true);
            $(checkedAll).prop("indeterminate", true);
        }
    });
});

/*****  End delete All Candidate *******/

/********** Select subjects ****************/

$(document).on("change", "#physical_science_core", function () {
    if ($(this).prop("checked")) {
        if ($("#physical_science_extended").prop("checked")) {
            $("#physical_science_extended").prop("checked", false);
            $(this).prop("checked", true);
        } else {
            $(this).prop("checked", true);
        }
    } else {
        $(this).prop("checked", false);
    }
});

$(document).on("change", "#physical_science_extended", function () {
    if ($(this).prop("checked")) {
        if ($("#physical_science_core").prop("checked")) {
            $("#physical_science_core").prop("checked", false);
            $(this).prop("checked", true);
        } else {
            $(this).prop("checked", true);
        }
    } else {
        $(this).prop("checked", false);
    }
});

$(document).on("change", "#maths_core", function () {
    if ($(this).prop("checked")) {
        if ($("#maths_extended").prop("checked")) {
            $("#maths_extended").prop("checked", false);
            $(this).prop("checked", true);
        } else {
            $(this).prop("checked", true);
        }
    } else {
        $(this).prop("checked", false);
    }
});

$(document).on("change", "#maths_extended", function () {
    if ($(this).prop("checked")) {
        if ($("#maths_core").prop("checked")) {
            $("#maths_core").prop("checked", false);

            $(this).prop("checked", true);
        } else {
            $(this).prop("checked", true);
        }
    } else {
        $(this).prop("checked", false);
    }
});
/********* end Select subjects ************/

// upload js
$("#fileup").change(function () {
    //here we take the file extension and set an array of valid extensions
    var res = $("#fileup").val();
    var arr = res.split("\\");
    var filename = arr.slice(-1)[0];
    filextension = filename.split(".");
    filext = "." + filextension.slice(-1)[0];
    valid = [".csv"];
    //if file is not valid we show the error icon, the red alert, and hide the submit button
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
});

// delete
function deleteAjax(id) {
    if (confirm("are You sure delete this candidate ?")) {
        $.ajax({
            type: "post",
            url: "delete.php",
            data: {
                delete_id: id
            },
            success: function (data) {
                $("#delete" + id).hide();
                showNotification(
                    "bottom",
                    "left",
                    "Candidate deleted successfully",
                    "danger"
                );
                calculateSum();
            },
        });
    }
}

function showNotification(from, align, message, color) {
    // type = ['', 'info', 'danger', 'success', 'warning', 'rose', 'primary'];
    $.notify({
        icon: "add_alert",
        message: message,
    }, {
        type: color,
        timer: 1000,
        placement: {
            from: from,
            align: align,
        },
    });
}
