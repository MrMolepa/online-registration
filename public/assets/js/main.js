// page loader function
$(window).on("load", function () {
    setTimeout(function () {
        $("body").addClass("loaded");
    }, 3000);
});

/********** Selected Payment method**************/
$(document).ready(function () {
    $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
        $('input[type="radio"]').prop("checked", false);
        $(e.target).find('input[type="radio"]').prop("checked", true);
    });
});
/********** Selected Payment method end**************/

var administrative_fee = 0;
var subjectFee = {};


$(document).ready(function () {
    // loader home page
    $(".preloader").fadeOut();

    /*-----------------------------------/
	/*	REGISTRESTION MULTISTEP
  /*----------------------------------*/

    /********** Some Variable Initial Value **************/
    var current_fs, next_fs, previous_fs;
    /********** Some Variable Initial Value End **************/

    /********** Some Variable Initial Value **************/
    const tabItems = $('[role="personal-tab"]'),
        tabPanels = $('[role="personal-tabpanel"]');
    let currentStep = 0;

    tabItems.each(function (i, d) {
        var is_checked = $(this).prop("checked");
        if (is_checked) {
            $(".alternative-tab-pane").eq(i).addClass("on");
            alternative(this);
        }
    });

    /*****************************************************************************
     * Resets the state of all tabs and tab panels.
     */

    /********** Some Variable Initial Value End **************/

    formUpdate();

    /********** Event Handler On Next **************/
    $(".next").click(function (e) {
        // test
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $(".preloader").fadeIn();
        $.validator.addMethod(
            "numberRegex",
            function (value, element) {
                return this.optional(element) || /^[0-9]*$/i.test(value);
            },
            "<i class='fas fa-exclamation-circle'></i> candidate number must contain only numbers"
        );
        //form validation
        var form = $("#msform");
        form.validate({
            errorElement: "span",
            errorClass: "help-block",
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("invalid");
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
            ignore: ":hidden:not(.do-not-ignore)",
            submitHandler: function (form) {
                // $(form).find('.submitSpin').show();
                $(form).find(".action-button").hide();
                form.submit();
            },
            rules: {
                candidate_no: {
                    required: true,
                    minlength: 9,
                    numberRegex: true,
                },
                alternative: {
                    required: true,
                },
                national_id: {
                    required: true,
                    numberRegex: true,
                },
                candidate_surname: {
                    required: true,
                },
                candidate_other_name: {
                    required: true,
                },
                date_of_birth: {
                    required: true,
                },
                gender: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                },
                phone_number: {
                    required: true,
                    minlength: 8,
                },
                special_need: {
                    required: true,
                },
                session: {
                    required: true,
                },
                level: {
                    required: true,
                },
                center: {
                    required: true,
                },
                number_of_subjects: {
                    required: true,
                },
                guardian_type: {
                    required: true,
                },
                guardian_national_id: {
                    required: true,
                    numberRegex: true,
                },
                guardian_name: {
                    required: true,
                },
                guardian_surname: {
                    required: true,
                },
                guardian_email: {
                    required: true,
                    email: true,
                },
                guardian_phone: {
                    required: true,
                },
                guardian_postal_address: {
                    required: true,
                },
                guardian_physical_address: {
                    required: true,
                },
                guardian_village: {
                    required: true,
                },
                guardian_district: {
                    required: true,
                },
                disclaimer: {
                    required: true,
                },
            },
            messages: {
                candidate_no: {
                    required: "<i class='fas fa-exclamation-circle'></i> The candidate number is required",
                },
                alternative: {
                    required: "<i class='fas fa-exclamation-circle'></i> The alternative choice is required",
                },
                national_id: {
                    required: "<i class='fas fa-exclamation-circle'></i>The national ID is required",
                },
                candidate_surname: {
                    required: "<i class='fas fa-exclamation-circle'></i>The candidate surname required",
                },
                candidate_other_name: {
                    required: "<i class='fas fa-exclamation-circle'></i> The candidate othername required",
                },
                date_of_birth: {
                    required: "<i class='fas fa-exclamation-circle'></i> The date of birth is required",
                },
                gender: {
                    required: "<i class='fas fa-exclamation-circle'></i>The gender is required ",
                },
                email: {
                    required: "<i class='fas fa-exclamation-circle'></i> The email is required",
                },
                phone_number: {
                    required: "<i class='fas fa-exclamation-circle'></i>The phone number is required",
                },
                center: {
                    required: "<i class='fas fa-exclamation-circle'></i> The center  is required",
                },

                number_of_subjects: {
                    required: "<i class='fas fa-exclamation-circle'></i>please select subject to proceed to billing",
                },
                disclaimer: {
                    required: "<i class='fas fa-exclamation-circle'></i> Terms and condition  ",
                },
            },
        });
        //load if form is valid
        if (form.valid() === true) {
            var i = 0;
            formUpdate();
            var button = $(this).prop("name");
            current_fs = $(this).parent().parent();
            next_fs = $(this).parent().parent().next();
            switch (button) {
                case "next-registration":
                    $(".preloader").fadeOut();
                    //display next fieldset or school_route(school registration or private registration)
                    //Add Class Active
                    $("#progressbar_content li")
                        .eq($("fieldset").index(next_fs))
                        .addClass("active");
                    next_fs.show();
                    //hide current fieldset
                    current_fs.hide();
                    break;
                case "personlinfo":
                    $(".preloader").fadeOut();
                    //display next fieldset or school_route(school registration or private registration)
                    //Add Class Active
                    $("#progressbar_content li")
                        .eq($(".fieldset").index(next_fs))
                        .addClass("active");
                    next_fs.show();
                    //hide current fieldset
                    current_fs.hide();

                    break;
                case "guardian":
                    //Add Class Active
                    $("#progressbar_content li")
                        .eq($(".fieldset").index(next_fs))
                        .addClass("active");

                    $(".preloader").fadeOut();
                    //display next fieldset or school_route(school registration or private registration)
                    next_fs.show();
                    //hide current fieldset
                    current_fs.hide();
                    break;
                case "exam-group":
                    $('input[name="number_of_subjects"]').addClass(
                        "do-not-ignore"
                    );
                    //Add Class Active
                    $("#progressbar_content li")
                        .eq($("fieldset").index(next_fs))
                        .addClass("active");
                    $(".preloader").fadeOut();
                    //display next fieldset or school_route(school registration or private registration)
                    next_fs.show();
                    getFees();
                    //hide current fieldset
                    current_fs.hide();
                    break;
                case "next-billing":
                    //Add Class Active
                    $("#progressbar_content li")
                        .eq($(".fieldset").index(next_fs))
                        .addClass("active");
                    $(".preloader").fadeOut();
                    //display next fieldset or school_route(school registration or private registration)
                    next_fs.show();
                    current_fs.hide();
                    var session_id = $("#session")
                        .find("option:selected")
                        .data("session");
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                    });
                    $.ajax({
                        url: "/registeration",
                        method: "POST",
                        cache: false,
                        data: $("#msform").serialize() +
                            "&" +
                            this.name +
                            "=" +
                            this.value +
                            "&session_id=" +
                            session_id,
                        beforeSend: function () {
                            // setting a timeout
                            $(".preloader").fadeIn();
                            i++;
                        },
                        success: function (data) {
                            if ($.isEmptyObject(data.errors)) {
                                $("input[name='next-payment']").hide();
                                if ($("#addSubjects").prop("checked")) {
                                    $(".registration-fee").html("M0.00");
                                    $(".local-fee").html("M0.00");
                                }
                                $("#total_amount").val(data.total_amount);
                                $("#number_of_subjects").val(
                                    data.subject_number
                                );
                                $("#bill").html(data.html);
                            } else {
                                $.each(data.errors, function (key, errors) {
                                    for (const error in errors) {
                                        const value = errors[error];
                                        toastr.error(value);
                                    }
                                });
                            }
                        },
                        complete: function () {
                            i--;
                            if (i <= 0) {
                                $(".preloader").fadeOut();
                            }
                        },
                    });
                    break;
                case "next-payment":
                    //Add Class Active
                    $("#progressbar_content li")
                        .eq($(".fieldset").index(next_fs))
                        .addClass("active");
                    $(".preloader").fadeOut();
                    //Add Class Active

                    //display next fieldset or school_route(school registration or private registration)
                    next_fs.show();
                    current_fs.hide();
                    break;
                case "make_payment":
                    //Add Class Active
                    $("#progressbar_content li")
                        .eq($(".fieldset").index(next_fs))
                        .addClass("active");

                    $(".preloader").fadeOut();
                    //Add Class Active
                    //display next fieldset or school_route(school registration or private registration)
                    next_fs.show();
                    current_fs.hide();
                    break;
                default:
                    break;
            }
        }
        $(".preloader").fadeOut();
    });
    /********** Event Handler On Next End **************/

    $(document).on("click", "input[name='registration']", function () {
        var registration = $(this).val();
        switch (registration) {
            case "1":
                location.href = "/center/login";
                break;
            case "2":
                location.href = "/candidate/login";
                break;
            case "3":
                location.href = "/private-candidate";
                break;
            case "4":
                location.href = "/sponsor";
                break;
            default:
                break;
        }
    });

    $(document).on("input", "input,select", function () {
        $("form").each(function () {
            $(this).find(":input").removeClass("is-invalid");
            //<-- Should return all input elements in that specific form.
        });
        $(this).siblings(".help-block").remove();
    });
    //

    /********** Event Handler On Previus **************/
    $(".previous").click(function () {
        current_fs = $(this).parent().parent();
        previous_fs = $(this).parent().parent().prev();

        //Remove class active
        $("#progressbar_content li")
            .eq($(".fieldset").index(current_fs))
            .removeClass("active");
        $('input[name="number_of_subjects"]').removeClass(
            "do-not-ignore"
        );

        //show the previous fieldset
        previous_fs.show();

        //hide the current fieldset
        current_fs.hide();
    });
    /********** Event Handler On Previus End **************/

    $("[name=agree-bill]").on("change", function () {
        var is_checked = $(this).prop("checked");
        if (is_checked) {
            $("input[name='next-payment']").show();
        } else {
            $("input[name='next-payment']").hide();
        }
    });

    /********** Registration Alternative Tabs**************/
    $("[name='alternative']").each(function (i, d) {
        console.log(i);
        $("#existing-candidate-number").attr("checked", "checked");
        var is_checked = $(this).prop("checked");
        if (is_checked) {
            $(".alternative-tab-pane").eq(i).addClass("on");
            alternative(this);
        }
    });

    $("[name='alternative']").on("click", function (e) {
        $("[name='alternative']").removeAttr("checked");
        $(this).attr("checked", "checked");
        var is_checked = $(this).prop("checked");
        alternative(this);
        var i = $("[name='alternative']").index(this);
        $(".alternative-tab-pane").removeClass("on");
        $(".alternative-tab-pane").eq(i).addClass("on");
    });

    function alternative(element) {
        var alternative = $(element).val();
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/registeration",
            method: "POST",
            data: {
                is_candidate_new: alternative
            },
            success: function (data) {
                $("#new-candidate").html("").fadeOut(300);
                $("#existing-candidate").html("").fadeOut(300);
                if (data.is_candidate_new) {
                    $("#new-candidate").html(data.html).fadeIn(300);
                } else {
                    $("#existing-candidate").html(data.html).fadeIn(300);
                }
            },
        });
    }
    /********** Registration Alternative Tabs End**************/

    var nextKinli = {};
    var nextKinFieldset = {};
    var removeFieldset = true;
    $(document).on("change", ".register_sessions", function () {
        var candidate_no = $("#candidate_no").val();
        var national_id = $("#national_id").val();
        var session = $(this).find('option:selected').val();
        var alternative = $("[name='alternative']:checked").val();

        if (session != "") {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            $.ajax({
                url: "/register-candidate-subjects",
                method: "POST",
                data: {
                    candidate_no: candidate_no,
                    national_id: national_id,
                    session: session,
                    alternative: alternative,
                },
                success: function (data) {
                    console.log(data);
                    if (removeFieldset) {
                        removeFieldset = false;
                        nextKinli = $("#nextKin").detach();
                        nextKinFieldset = $(".personlinfo")
                            .parent()
                            .parent()
                            .next()
                            .detach();
                    }

                    $(".subjects_selection input:checkbox").prop(
                        "checked",
                        false
                    );

                    if ($.isEmptyObject(data.candidate)) {
                        // adding Subjects
                        $(".registed_subject").show().html(data.html);
                        $(".registed_subject").css({
                            fontSize: "13px"
                        });
                        // Show session for registed
                        $("#msform #session").attr("readonly", false);

                        $(
                            '#msform #session option[value="' + session + '"]'
                        ).prop("hidden", false);
                        // Select level
                        $(
                                '#msform #level option[value="' +
                                data.center.level +
                                '"]'
                            )
                            .attr("selected", "selected")
                            .change();
                        $("#level").attr("readonly", true);
                        $('#msform #session option[value="' + session + '"]')
                            .attr("selected", "selected")
                            .change();
                        $("#msform #session").attr("readonly", true);
                        // Center
                        initailizeSelect2();
                        $(".livesearch-centers").select2("trigger", "select", {
                            data: {
                                id: data.center.center_no,
                                text: data.center.center_name,
                            },
                        });
                        $(".livesearch-center")
                            .select2("destroy")
                            .attr("readonly", true);
                        $(".personlinfo").hide();
                    } else {

                        // New Session
                        $("#msform #session").attr("readonly", false);
                        $(".registed_subject").show().html(data.html);

                        $(".registed_subject").css({
                            fontSize: "13px"
                        });
                        $(".personlinfo").show();
                        // Hide session for registed
                        $(
                            '#msform #session option[value="' +
                            data.candidate.session +
                            '"]'
                        ).attr("hidden", true);
                        $("#msform #session").val("").trigger("change");
                        // Select level
                        $(
                                '#msform #level option[value="' +
                                data.candidate.level +
                                '"]'
                            )
                            .attr("selected", "selected")
                            .change();
                        $("#level").attr("readonly", true);



                        // subjects_selection
                        $(".subjects_selection").html("");
                    }
                },
            });
        } else {
            $(".registed_subject").html("");
            $(".personlinfo").hide();
        }
    });

    /********** Persoanl Validation**************/
    var current_section, next_section, previous_section; //fieldsets
    var opacity;
    var current = 1;
    var steps = $("section").length;

    setProgressBar(current);
    $(document).on("click", ".next_personal", function () {
        var next_value = $(this).val();
        var next_name = $(this).attr("name");
        var alternative = $("[name='alternative']:checked").val();
        //load if form is valid
        current_section = $(this).parent();
        next_section = $(this).parent().next();
        var parentIndex = $(this).parent().index();
        console.log(parentIndex);

        if (next_name == "next_guardian") {
            var inputData = $(this)
                .parent()
                .find("select, textarea, input")
                .serialize();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            $.ajax({
                url: "/private-multiform-personal",
                method: "POST",
                data: inputData +
                    "&" +
                    "current_section" +
                    "=" +
                    $(this).parent().index(),
                success: function (data) {
                    if ($.isEmptyObject(data.errors)) {
                        $(".personlinfo").trigger("click");
                    } else {
                        errorMessage(data);
                    }
                },
            });
        } else {
            var inputData = $(this)
                .parent()
                .find("select, textarea, input")
                .serialize();
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });
            $.ajax({
                url: "/private-multiform-personal",
                method: "POST",
                data: inputData +
                    "&current_section" +
                    "=" +
                    parentIndex +
                    "&alternative=" +
                    alternative,
                success: function (data) {
                    console.log(data);
                    if ($.isEmptyObject(data.errors)) {
                        if (data.status == 1 && data.registered == true) {
                            $(".registered-info").html(data.output);
                            $("#personal-section .form-group").hide();
                            $("#personal-section .form-row").hide();
                            $("#personal-section .varify-infomation").hide();
                        } else if (
                            data.status == 2 &&
                            data.registered == true
                        ) {
                            $(".registered-info").html(data.output);
                            $(".registered-info").html(data.output);
                            $("#personal-section .form-group").hide();
                            $("#personal-section .form-row").hide();
                            $("#personal-section .varify-infomation").hide();
                        } else {
                            next_section.show();
                            current_section.animate({
                                opacity: 0
                            }, {
                                step: function (now) {
                                    // for making fielset appear animation
                                    opacity = 1 - now;
                                    current_section.css({
                                        display: "none",
                                        position: "relative",
                                    });
                                    next_section.css({
                                        opacity: opacity
                                    });
                                },
                                duration: 500,
                            });
                        }
                    } else {
                        errorMessage(data);
                    }
                },
            });
        }
    });

    $(document).on("click", ".previous_personal", function () {
        current_section = $(this).parent();
        previous_section = $(this).parent().prev();

        //Remove class active

        //show the previous fieldset
        previous_section.show();

        //hide the current fieldset with style
        current_section.animate({
            opacity: 0
        }, {
            step: function (now) {
                // for making fielset appear animation
                opacity = 1 - now;

                current_section.css({
                    display: "none",
                    position: "relative",
                });
                previous_section.css({
                    opacity: opacity
                });
            },
            duration: 500,
        });
        setProgressBar(--current);
    });
    /********** Persoanl Validation End**************/

    /********** Subject Selection **************/

    /********** if increase number of Subjects**************/
    $(document).on("change", "#appending-subjects", function () {
        if (this.checked) {
            $(".personlinfo").show();
        } else {
            $(".personlinfo").hide();
        }
    });
    /********** if increase number of Subjects end**************/

    /*-----------------------------------/
	/* 	FEES
  /*----------------------------------*/
    function getFees() {
        var level = $("#level").val();
        var session = $("#session").val();
        var increase = $("#appending-subjects").val();
        var session_id = $("#session").find("option:selected").data("session");
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/registeration",
            method: "post",
            data: {
                fee_stracture: "setup",
                level: level,
                session: session,
                session_id: session_id,
                subject_addition: increase,
            },
            success: function (data) {
                $('#fee_group_id').val(data.fee_group_id);
                $('#fine').val(data.total_fine);
                administrative_fee = data.administrative_fee;
                subjectFee = data.subjects_fee;
            },
        });
    }
    /*-----------------------------------/
  /* End	FEES
/*----------------------------------*/

    /********** Select subjects ****************/
    $(document).on("change", ".subject", function () {
        checkedCheckBox();
    });
    $(document).on("click", ".subjects_selection  input", function () {
        if ($(this).prop("checked")) {
            var input_classes = $(this).attr("class").split(" ");
            var className;
            $.each(input_classes, function () {
                if (this.toLowerCase().indexOf("subj_") >= 0) className = this;
            });
            $("." + className).prop("checked", false);
            $(this).prop("checked", true);
        } else {
            var input_classes = $(this).attr("class").split(" ");
            var className;
            $.each(input_classes, function () {
                if (this.toLowerCase().indexOf("subj_") >= 0) className = this;
            });
            $("." + className).prop("checked", false);
            $(this).prop("checked", false);
        }
        checkedCheckBox();
    });

    /********** Checked Subjects **************/
    function checkedCheckBox() {
        var subjects = [];

        $(".subjects_selection input:checkbox:checked").each(function () {
            const subject = $(this).val().split(",")
            subjects.push(subject[0]);
        });
        var numberOfChecked = subjects.length;
        var subject_fee_total = parseFloat(administrative_fee);
        for (let index = 0; index < subjects.length; ++index) {
            const subject = subjects[index];
            subject_fee_total += parseFloat(subjectFee[subject]);
        }
        if (numberOfChecked <= 0) {
            $(".subject_number").text(0);
            $(".total").text("M" + 0);
            $("#total_amount input[name='total_amount']").attr("value", 0);
            $("#number_of_subjects").removeAttr("value");
        } else {
            $(".subject_number").text(numberOfChecked);
            $(".total").text("LSL" + subject_fee_total);
            $("#total_amount input[name='total_amount']").attr(
                "value",
                subject_fee_total
            );
            $("#number_of_subjects").val(numberOfChecked);
        }
    }
    /********** Checked Subjects End**************/

    /********** Subject Selection End**************/

    /********** Auto Search For School Centres **************/

    /**** Initailize Select2 functions *******/
    initailizeSelect2();

    function initailizeSelect2() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $(".livesearch-centers").select2({
            placeholder: "Select the Center",
            ajax: {
                url: "/registeration-autocomplete-search",
                method: "POST",
                dataType: "json",
                data: function (params) {
                    var level = $("#level")
                        .find("option:selected")
                        .data("level");
                    var session = $("#session").find("option:selected").val();
                    var query = {
                        search: params.term,
                        level: level,
                        session: session,
                    };
                    return query;
                },
                delay: 250,
                processResults: function (data) {
                    console.log(data);
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.center_name,
                                id: item.center_no,
                            };
                        }),
                    };
                },
                cache: true,
            },
        });
    }
    /**** Initailize Select2 functions *******/

    $(document).on("change", ".livesearch-centers", function () {
        var centre_no = $(this).val();
        var level = $("#level").find("option:selected").data("level");
        var session = $("#session").find("option:selected").data("session");
        var candidate_no = $("#candidate_no").val();
        var national_id = $("#national_id").val();
        var appending_subjects = $("#appending-subjects").prop("checked");
        var register_sessions = $(".register_sessions")
            .find("option:selected")
            .val();
        var alternative = $("[name='alternative']:checked").val();

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "/registeration-center-subjects",
            method: "POST",
            data: {
                centre_no: centre_no,
                level: level,
                session: session,
                candidate_no: candidate_no,
                national_id: national_id,
                appending_subjects: appending_subjects,
                register_sessions: register_sessions,
                alternative: alternative,
            },
            success: function (data) {
                $(".subjects_selection").html(data.subjectsHTML);
                console.log(data);
            },
        });
    });
    /********** Auto Search For School Centres End **************/

    function formUpdate() {
        var candidate_surname = $("input[name='candidate_surname']").val();
        var candidate_other_name = $(
            "input[name='candidate_other_name']"
        ).val();
        var national_id = $("input[name='national_id']").val();
        var email = $("input[name='email']").val();
        var number_of_subjects = $("input[name='number_of_subjects']").val();
        var totalAmount = $("input[name='total_amount']").val();
        var totalAmount1 = totalAmount * 100;

        $("#Ecom_BillTo_Postal_Name_First").val(candidate_other_name);
        $("#Ecom_BillTo_Postal_Name_Last").val(candidate_surname);

        $("#Ecom_BillTo_Postal_Name_Prefix").val(national_id);
        $("#Ecom_BillTo_Online_Email").val(email);
        $("#Transaction_Amount").val(totalAmount1);
        $("#Transaction_LineItems_Amount_1").val(totalAmount1);
        $("#Lite_Order_LineItems_Amount_1").val(totalAmount1);
        $("#Lite_Order_Amount").val(totalAmount1);
        $("#Lite_Order_LineItems_Amount_1").val(totalAmount1);
        // $("#Lite_Order_LineItems_Quantity_1").val(number_of_subjects);
        $("#Lite_Order_LineItems_Product_1").val(
            "Total Subjects (" + number_of_subjects + ")"
        );
    }

    function errorMessage(data) {
        $(".help-block").remove();
        $("form").each(function () {
            $(this).find(":input").removeClass("is-invalid"); //<-- Should return all input elements in that specific form.
        });
        for (const field in data.errors) {
            $("[name='" + field + "']").addClass("is-invalid");
            const value = data.errors[field];
            $(`<span class='help-block'>${value} </span>`).insertAfter(
                "[name='" + field + "']"
            );
        }
    }

    // disable enter key
    $(document).on("keypress", "input", function (ev) {
        if (ev.which == 13) {
            ev.preventDefault();
        }
    });
    $(document).on("keydown", "input", function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });

    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });

    //remove placeholder on personal information textfield on click
    /********** Remove Placehold**************/
    $(".input_wait,select").each(function () {
        $(this).attr("data-placeholder", this.placeholder);

        $(this).bind("focus", function () {
            if ($(this).val().length === 0) {
                if ($(this).parent().hasClass("has-error")) {
                    this.placeholder = "";
                    // $(this).css("border","3px solid red");
                } else {
                    this.placeholder = "";
                    // show label associated with the input in focus
                    $("label[for='" + this.id + "']").removeClass(
                        "label_hidden"
                    );
                }
            } else {
                if ($(this).parent().hasClass("has-error")) {
                    this.placeholder = "";
                    // $(this).css("border","3px solid red");
                } else {
                    this.placeholder = "";
                    // show label associated with the input in focus
                    $("label[for='" + this.id + "']").removeClass(
                        "label_hidden"
                    );
                }
            }
        });

        $(this).bind("blur", function () {
            if ($(this).val().length === 0) {
                if ($(this).parent().hasClass("has-error")) {
                    this.placeholder = $(this).attr("data-placeholder");
                } else {
                    this.placeholder = $(this).attr("data-placeholder");

                    $("label[for='" + this.id + "']").addClass("label_hidden");
                }
            } else {
                if (!$(this).parent().hasClass("has-error")) {
                    $(this).css("border", "1px solid grey");
                    $("label[for='" + this.id + "']").css("color", "grey");
                    if (
                        $("label[for='" + this.id + "']").hasClass(
                            "label_hidden"
                        )
                    ) {
                        $("label[for='" + this.id + "']").removeClass(
                            "label_hidden"
                        );
                    }
                }
            }
        });
    });

    if (!$(this).val()) {
        $(this).removeClass("error");
    } else {
        $(this).addClass("error");
    }
    /********** Remove Placehold End**************/

    function setProgressBar(curStep) {
        var percent = parseFloat(100 / steps) * curStep;
        console.log(percent);
        percent = percent.toFixed();
        $(".progress-bar").css("width", percent + "%");
    }
});

// ==============================================================
// Login Validation
// ==============================================================

$("input[type='password'][data-eye]").each(function (i) {
    var $this = $(this);
    $this.wrap(
        $("<div/>", {
            style: "position:relative",
        })
    );
    $this.css({
        paddingRight: 60,
    });
    $this.after(
        $("<div/>", {
            html: "Show",
            class: "btn btn-primary btn-sm",
            id: "passeye-toggle-" + i,
            style: "position:absolute;right:10px;top:50%;transform:translate(0,-50%);-webkit-transform:translate(0,-50%);-o-transform:translate(0,-50%);padding: 2px 7px;font-size:12px;cursor:pointer;",
        })
    );
    $this.after(
        $("<input/>", {
            type: "hidden",
            id: "passeye-" + i,
        })
    );
    $this.on("keyup paste", function () {
        $("#passeye-" + i).val($(this).val());
    });
    $("#passeye-toggle-" + i).on("click", function () {
        if ($this.hasClass("show")) {
            $this.attr("type", "password");
            $this.removeClass("show");
            $(this).removeClass("btn-outline-primary");
        } else {
            $this.attr("type", "text");
            $this.val($("#passeye-" + i).val());
            $this.addClass("show");
            $(this).addClass("btn-outline-primary");
        }
    });
});
$("#password").on("keyup", function () {
    var number = /([0-9])/;
    var alphabets = /([a-zA-Z])/;
    var special_characters = /([~,!,@,#,$,%,^,&,*,-,_,+,=,?,>,<])/;
    if ($("#password").val().length < 6) {
        $("#password-strength-status").removeClass();
        $("#password-strength-status").addClass("weak-password");
        $("#password-strength-status").html(
            "Weak (should be atleast 6 characters.)"
        );
    } else {
        if (
            $("#password").val().match(number) &&
            $("#password").val().match(alphabets) &&
            $("#password").val().match(special_characters)
        ) {
            $("#password-strength-status").removeClass();
            $("#password-strength-status").addClass("strong-password");
            $("#password-strength-status").html("Strong");
        } else {
            $("#password-strength-status").removeClass();
            $("#password-strength-status").addClass("medium-password");
            $("#password-strength-status").html(
                "Medium (should include alphabets, numbers and special characters or some combination.)"
            );
        }
    }
});

$("#password-confirm").on("keyup", function () {
    var password = $("#password").val();
    var confirm_password = $(this).val();
    if (password == confirm_password) {
        $("#password-strength-status").removeClass();
        $("#password-strength-status").addClass("strong-password");
        $("#password-strength-status").html("Good");
    } else {
        $("#password-strength-status").removeClass();
        $("#password-strength-status").addClass("medium-password");
        $("#password-strength-status").html(
            "Password and confirm password does not match)"
        );
    }
});
