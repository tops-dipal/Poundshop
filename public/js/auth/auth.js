/*
 * @author : Hitesh Tank
 */
(function($){
   'use strict'
   var authenticationClass = function ()
    {
        var c = this;
        $(document).ready(function ()
        {
            c._initialize();
        });
    };
    var c = authenticationClass.prototype;
    
    c._initialize = function ()
    {
        //PoundShopApp.commonClass._displaySuccessMessage('asd');
        this._validateLogin();
        this._validateForgot();
        this._validateResetPassword();
    };
    
    c._validateLogin = function () {
        $(".user-login").validate({
            focusInvalid: true, // do not focus the last invalid input
            errorElement: 'span',
            errorClass: 'invalid-feedback', // default input error message class
            ignore: [],
            rules: {
                "email": {
                    required: true,
                    normalizer: function (value) {
                        return $.trim(value);
                    },
                    customemail: true,
                },
                "password": {
                    required: true,
                },
            },
            messages: {
                "email": {
                    required: POUNDSHOP_MESSAGES.validations.email_required,
                    email: POUNDSHOP_MESSAGES.validations.valid_email,
                },
                "password": {
                    required: POUNDSHOP_MESSAGES.validations.password_required,
                },
            },
            errorPlacement: function (error, element) {
                var id = $(error).attr('id');
                $('#' + id).remove();
                error.insertAfter(element);
            },
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },
            submitHandler: function (form) {
                $("#page-loader").show();
                $('#login-btn').attr('disabled', 'disabled');
                form.submit();
            }
        });
        $.validator.addMethod("customemail", 
            function(value, element) {
                return /^[_a-zA-Z0-9-+.]+(\.[_a-zA-Z0-9-+.]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/.test(value);
            }, 
            "Please enter valid email address"
        );

    };
    
    c._validateForgot = function(){
        $("#forgot-form").validate({
            focusInvalid: false, // do not focus the last invalid input
            errorElement: 'span',
            errorClass: 'invalid-feedback', // default input error message class
            ignore: [],
            rules: {
                "email": {
                    required: true,
                    normalizer: function (value) {
                        return $.trim(value);
                    },
                    customemail: true,
                }
            },
            messages: {
                "email": {
                    required: POUNDSHOP_MESSAGES.validations.email_required,
                    email: POUNDSHOP_MESSAGES.validations.valid_email,
                }
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },
            submitHandler: function (form) {
                $('#forgot-button').attr('disabled', true);
                var dataString = $("#forgot-form").serialize();
                $.ajax({
                    type: "POST",
                    url: $("#forgot-form").attr("action"),
                    data: dataString,
                    cache: false,
                    beforeSend: function () {
                        $("#page-loader").show();
                    },
                    success: function (response) {
                        $("#page-loader").hide();
                        if (response.status == 1) {
                            $("#forgot-form")[0].reset();
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                            setTimeout(function () {
                                location.reload;
                            }, 1200);
                        } else {
                           
                        }
                        $('#forgot-button').attr('disabled', false);
                    },
                    error: function (xhr, err) {
                       $('#forgot-button').attr('disabled', false);
                       PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });

            }
        });
    }
    
    c._validateResetPassword=function(){
        $("#reset-password-form").validate({
            focusInvalid: false, // do not focus the last invalid input
            errorElement: 'span',
            errorClass: 'invalid-feedback', // default input error message class
            ignore: [],
            rules: {
                "email": {
                    required: true,
                    normalizer: function (value) {
                        return $.trim(value);
                    },
                    customemail: true,
                },
                "password": {
                    required: true,
                    minlength: 6,
                    maxlength: 20,
                },
                "password_confirmation": {
                    required: true,
                    minlength: 6,
                    maxlength: 20,
                    equalTo: "#password"
                },
            },
            messages: {
                "email": {
                    required: POUNDSHOP_MESSAGES.validations.email_required,
                    email: POUNDSHOP_MESSAGES.validations.valid_email,
                },
                "password": {
                    required: POUNDSHOP_MESSAGES.validations.password_required,
                    minlength: "Password contains minimum {0} characters.",
                    maxlength: "Password can't have more than {0} characters.",

                },
                "password_confirmation": {
                    required: POUNDSHOP_MESSAGES.validations.confirm_password_required,
                    minlength: "Confirm Password contains minimum {0} characters.",
                    maxlength: 'Confirm Password contains minimum {0} characters.',
                    equalTo: POUNDSHOP_MESSAGES.validations.confirmation_validation,
                },
            },
            errorPlacement: function (error, element) {
                var id = $(error).attr('id');
                $('#' + id).remove();
                error.insertAfter(element);
            },
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },
            submitHandler: function (form) {
              form.submit();
            }
        });
    }
   window.PoundShopApp = window.PoundShopApp || {}
   window.PoundShopApp.authenticationClass = new authenticationClass();
})(jQuery);