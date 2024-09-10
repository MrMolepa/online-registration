/********** Event Handler On Next **************/

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

$(".next").click(function (e) {
    // test
    e.preventDefault();

    $.validator.addMethod("minStrict", function (value, el, param) {
        return value > param;
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

        ignore: ":hidden:not(.do-not-ignore)",
        submitHandler: function (form) {
            // $(form).find('.submitSpin').show();
            $(form).find(".action-button").hide();
            form.submit();
        },
        rules: {
            candidate_email: {
                required: true,
                email: true,
            },
            cadidate_phone: {
                required: true,
                number: true,
                minStrict: 8,
            },
            special_need: {
                required: true,
            },
            candidate_postal_address: {
                required: true,
            },
            candidate_physical_address: {
                required: true,
            },
            candidate_village: {
                required: true,
            },
            candidate_district: {
                required: true,
            },
            guardian_national_id: {
                required: true,
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
            guardian_village: {
                required: true,
            },
        },
        messages: {},
    });

    //load if form is valid
    if (form.valid() === true) {
        current_fs = $(this).parent().parent();
        next_fs = $(this).parent().parent().next();
        var current_page = $("fieldset").index(next_fs);
        console.log(current_page);
        formUpdate();
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ),
            },
        });
        $.ajax({
            url: "/candidate/profile-update",
            method: "POST",
            cache: false,
            data: $("#msform").serialize() +
                "&current_page" +
                "=" +
                current_page,
            success: function (data) {
                console.log(data);
                if ($.isEmptyObject(data.errors)) {
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
                    toastr.success(data.success);

                } else {
                    var parent="#msform";
                    $(`${parent} input, ${parent} select, textarea`).each(function(index) {
                        $(`${parent} .invalid-feedback`).remove();
                        $(`${parent} .is-invalid`).removeClass('is-invalid');

                    });
                    $.each(data.errors, function(key, errors) {
                        for (const error in errors) {
                            const value = errors[error];
                            $(`[name='${key}']`).addClass('is-invalid');
                            $(`<span class='invalid-feedback'>${value}</span>`).insertAfter(
                                `${parent} [name='${key}']`)

                        }
                    });



                }
            },

        });









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



$("[name=payment]").on("change", function () {
    var is_checked = $(this).prop("checked");

    // $(type).index(this) == nth-of-type
    var i = $("[name=payment]").index(this);
    $(".payment-tab-pane").removeClass("on");
    $(".payment-tab-pane").eq(i).addClass("on");
});




function formUpdate() {
    var candidate_surname = $("input[name='candidate_surname']").val();
    var candidate_other_name = $(
        "input[name='candidate_other_name']"
    ).val();
    var gender = $("input[name='gender']").val();
    var email = $("input[name='candidate_email']").val();
    var number_of_subjects = $("input[name='number_of_subjects']").val();
    var totalAmount = $("input[name='total_amount']").val();
    var totalAmount1 = totalAmount * 100;
    $("#Ecom_BillTo_Postal_Name_First").val(candidate_other_name);
    $("#Ecom_BillTo_Postal_Name_Last").val(candidate_surname);
    if (gender == "M") {
        $("#Ecom_BillTo_Postal_Name_Prefix").val("Mr");
    } else {
        $("#Ecom_BillTo_Postal_Name_Prefix").val("Miss");
    }

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
