$(document).ready(function () {

    /* Initialization */

    signUpFormFx();
    logInFormFx();
    brandNameFormFx();

    /* Functions */

    function errorCodeToMessage(errorCode)
    {
        var message = '';

        switch (errorCode) {
            case 1:
                message = SFX_ERROR_EMAIL_NOT_VALID;
                break;
            case 2:
                message = SFX_ERROR_SHORT_PASSWORD;
                break;
            case 4:
                message = SFX_ERROR_ACCOUNT_EXISTS;
                break;
            case 8:
                message = SFX_ERROR_ACCOUNT_DISABLED;
                break;
            case 16:
                message = SFX_ERROR_WRONG_PASSWORD;
                break;
        }

        return message;
    }

    /**
     * Validates the Sign Up form.
     */
    function signUpFormFx()
    {
        // Validates the signup form.

        $('#signUpForm').submit(function(e) {

            e.preventDefault();

            $.ajax({
                url: '/session/ajax-check-signup/',
                type: 'post',
                data: {
                    email: $('input[name="pastinysh"]').val(),
                    password: $('input[name="paroleens"]').val()
                },
                success: function (payload) {

                    if (payload.isError) {

                        $('#signUpForm input').removeClass('error');

                        if (payload.errorCode == 1) {
                            $('input[name="pastinysh"]').addClass('error').focus();
                        }
                        if (payload.errorCode == 2) {
                            $('input[name="paroleens"]').addClass('error').focus();
                        }
                        if (payload.errorCode == 4) {
                            $('input[name="paroleens"]').addClass('error').focus();
                            $('#forgotPassword').fadeIn(1000);
                        }
                        if (payload.errorCode == 8) {

                        }
                        if (payload.errorCode == 16) {
                            $('input[name="paroleens"]').addClass('error').focus();
                        }

                        var message = errorCodeToMessage(payload.errorCode);

                        if (message != '') {
                            $('.row.error').show().html(message);
                            return false;
                        }
                    }
                    else {
                        $('body').addClass('transition-go-inside');
                        $('#signUpForm').off().submit();
                    }
                }
            });
        });
    }

    /**
     * Validates Log In form.
     */
    function logInFormFx()
    {
        // Validates the log-in form.

        $('#logInForm').submit(function(e) {

            e.preventDefault();

            $.ajax({
                url: '/session/ajax-check-login/',
                type: 'post',
                data: {
                    email: $('input[name="pastinysh"]').val(),
                    password: $('input[name="paroleens"]').val()
                },
                success: function(payload) {

                    if (payload.isError) {

                        $('#logInForm input').removeClass('error');

                        if (payload.errorCode == 1) {
                            $('input[name="pastinysh"]').addClass('error').focus();
                        }
                        if (payload.errorCode == 2) {
                            $('input[name="paroleens"]').addClass('error').focus();
                        }
                        if (payload.errorCode == 4) {
                            $('input[name="paroleens"]').addClass('error').focus();
                        }
                        if (payload.errorCode == 8) {

                        }
                        if (payload.errorCode == 16) {
                            $('input[name="paroleens"]').addClass('error').focus();
                        }

                        var message = errorCodeToMessage(payload.errorCode);

                        if (message != '') {
                            $('.row.error').show().html(message);
                            return false;
                        }
                    }
                    else {
                        $('body').addClass('transition-go-inside');
                        $('#logInForm').off().submit();
                    }
                }
            });
        });
    }

    function brandNameFormFx() {
        // get fingerprint
        $('form#brandnameForm').submit(function () {
            var fp1 = new Fingerprint({canvas: true});
            if ($('form#brandnameForm #fp').length == 0) {
                $(this).append('<input id="fp" type="hidden" name="fp">');
                $('form#brandnameForm #fp').val(fp1.get());
            }
        });
    }

    /* End */

});

function testSvg()
{
    if (document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#Image", "1.1")) {
        return true;
    } else {
        console.log('SVG test fail.');
        return false;
    }
}

function testCssSelector(selector)
{
    try {
        document.querySelector(selector);
    } catch (e) {
        console.log('Selector test fail:' + selector);
        return false;
    }
    return true;
}

function testCssProperty(prop)
{
    if (typeof document.documentElement.style[prop] == 'string') {
        return true;
    } else {
        console.log('CSS property test fail:' + prop);
        return false;
    }
}

function isUnsupported()
{
    var nua = navigator.userAgent;
    var is_android_native = nua.match(/Android/i) && !nua.match(/Opera/i) && !nua.match(/Chrome/i) && !nua.match(/Firefox/i);
    var is_opera_mini = nua.match(/Opera Mini/i);
    // filter out unsupported
    if (is_android_native || is_opera_mini) {
        return true;
    } else {
        return false;
    }
}