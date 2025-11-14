// page loader function
$(window).on("load", function () {
    setTimeout(function () {
        $("body").addClass("loaded");
    }, 1000);
});

$(document).ready(function () {
    $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
        localStorage.setItem("lastTab", $(this).attr("href"));
    });
    var lastTab = localStorage.getItem("lastTab");

    if (lastTab) {
        $('[href="' + lastTab + '"]').tab("show");
    }
    /*-----------------------------------/
	/*	TOOLTIP SETUP
  /*----------------------------------*/

    $('[data-toggle="tooltip"]').tooltip();

    /*-----------------------------------/
	/*	TOOLTIP SETUP END
  /*----------------------------------*/

    /*-----------------------------------/
	/*	TOASTER AND NOTIFICATION SETUP
  /*----------------------------------*/
    toastr.options = {
        closeButton: true,
        newestOnTop: false,
        progressBar: true,
        positionClass: "toast-top-center",
        preventDuplicates: false,
        onclick: null,
        showDuration: "3000",
        hideDuration: "8000",
        timeOut: "10000",
        extendedTimeOut: "8000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
    };

    /*-----------------------------------/
	/*	TOP NAVIGATION AND LAYOUT
  /*----------------------------------*/

    // Get current path and find target link
    var path = window.location.pathname.split("/").pop();

    // Account for home page with empty path
    if (path == "") {
        path = "index.php";
    }

    var target = $('nav a[href="' + path + '.php"]');

    // Add active class to target link
    target.addClass("active");

    $(".btn-toggle-fullwidth").on("click", function () {
        if (!$("body").hasClass("layout-fullwidth")) {
            $("body").addClass("layout-fullwidth");
        } else {
            $("body").removeClass("layout-fullwidth");
            $("body").removeClass("layout-default"); // also remove default behaviour if set
        }

        $(this)
            .find(".lnr")
            .toggleClass("lnr-arrow-left-circle lnr-arrow-right-circle");

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
            $(".btn-toggle-fullwidth")
                .find(".icon-arrows")
                .removeClass("icon-arrows-move-left")
                .addClass("icon-arrows-move-right");
        }

        // adjust right sidebar top position
        $(".right-sidebar").css("top", $(".navbar").innerHeight());

        // if page has content-menu, set top padding of main-content
        if ($(".has-content-menu").length > 0) {
            $(".navbar + .main-content").css(
                "padding-top",
                $(".navbar").innerHeight()
            );
        }

        // for shorter main content
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
            height: "100%",
            wheelStep: 2,
        });
    }

    /*-----------------------------------/
	/*	PANEL FUNCTIONS
	/*----------------------------------*/

    // panel remove
    $(".panel .btn-remove").click(function (e) {
        e.preventDefault();
        $(this)
            .parents(".panel")
            .fadeOut(300, function () {
                $(this).remove();
            });
    });

    // panel collapse/expand
    var affectedElement = $(".panel-body");

    $(".panel .btn-toggle-collapse").clickToggle(
        function (e) {
            e.preventDefault();

            // if has scroll
            if ($(this).parents(".panel").find(".slimScrollDiv").length > 0) {
                affectedElement = $(".slimScrollDiv");
            }

            $(this).parents(".panel").find(affectedElement).slideUp(300);
            $(this).find("i.lnr-chevron-up").toggleClass("lnr-chevron-down");
        },
        function (e) {
            e.preventDefault();

            // if has scroll
            if ($(this).parents(".panel").find(".slimScrollDiv").length > 0) {
                affectedElement = $(".slimScrollDiv");
            }

            $(this).parents(".panel").find(affectedElement).slideDown(300);
            $(this).find("i.lnr-chevron-up").toggleClass("lnr-chevron-down");
        }
    );

    /*-----------------------------------/
	/*	PANEL SCROLLING
	/*----------------------------------*/

    if ($(".panel-scrolling").length > 0) {
        $(".panel-scrolling .panel-body").slimScroll({
            height: "430px",
            wheelStep: 2,
        });
    }

    if ($("#panel-scrolling-demo").length > 0) {
        $("#panel-scrolling-demo .panel-body").slimScroll({
            height: "175px",
            wheelStep: 2,
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
	/*Display LOGS
  /*----------------------------------*/

    /********** Some Variable Initial Value **************/

    var log_filter = $("#log_filter").val();
    var log_sort = $("#log_sort").val();

    // ==============================================================
    // Checkbox selection (Permissions)
    // ==============================================================
    const item = ".item";
    const list = ".list";
    // Tree Checkbox
    const check = function (el) {
        const $this = $(el);
        checkChildren($this);
        checkParent($this);
    };

    const checkChildren = (el) => {
        const $this = $(el);
        const $child = $this.closest(item).children(list).find("input");
        const isCurrentCheck = $this.is(":checked");
        if (isCurrentCheck) {
            $child.prop("checked", true);
        } else {
            $child.prop("checked", false);
        }
    };

    const checkParent = (el) => {
        const $this = $(el);

        const $parent = $this
            .closest(list)
            .parents(item)
            .children("label")
            .children("input");
        $.makeArray($parent).map((parent) => checkSelf(parent));
    };

    const checkSelf = (el) => {
        const $this = $(el);

        const $children = $this
            .closest(item)
            .children(list)
            .children(item)
            .children("label")
            .children("input"); // Direct children
        const isAllChildrenChecked = $.makeArray($children).every((child) =>
            $(child).is(":checked")
        );
        const isSomeChildrenChecked = $.makeArray($children).some(
            (child) => $(child).is(":checked") || $(child).is(":indeterminate")
        );

        if (isAllChildrenChecked) {
            $this.prop("checked", true);
        } else {
            $this.prop("checked", false);
        }

        if (isSomeChildrenChecked && !isAllChildrenChecked) {
            $this.prop("indeterminate", true);
        } else {
            $this.prop("indeterminate", false);
        }
    };

    $(document).ready(function () {
        const $checkbox = $("input[type=checkbox]");
        checkParent($checkbox);
        $checkbox.on("change", function () {
            check(this);
        });





        // All parent levels require .dropdown-toggle class
        $('.dropdown-menu').find('.dropdown-submenu').not('.disabled').find('.dropdown-toggle').on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            console.log('clicked');

            // Remove "show" class in all siblings
            $(this).parent().siblings().removeClass('show').find('.show').removeClass('show');

            // Toggle submenu
            $(this).parent().toggleClass('show').children('.dropdown-menu').toggleClass('show');

            // Hide all levels when parent dropdown is closed
            $(this).parents('.show').on('hidden.bs.dropdown', function(e) {
                $('.dropdown-submenu .show, .dropdown-submenu.show').removeClass('show');
            });
        });






    });

    /*-----------------------------------/
	/*	DISPLAY  DATATABLE
  /*----------------------------------*/
    $("#centresTable").DataTable();


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
});

// confirm
$(document).on("input", "select", function (e) {
    var msg = $(this).children("option:selected").data("confirm");
    if (msg != undefined && !confirm(msg)) {
        $(this)[0].selectedIndex = 0;
    }
});

// toggle function
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
