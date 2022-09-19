var pauseListeningToClicks = false; // disable onclicks temporarily after touch
var isSwipeTakingPlace = false; // Detect swipe - used to see if touchend is end of normal touch
var touchMoveCounter = 0; // Touchmove counter, because usually 1 is not enough to count move
var isRetina = (
    window.devicePixelRatio > 1 ||
    (window.matchMedia && window.matchMedia("(-webkit-min-device-pixel-ratio: 1.5),(-moz-min-device-pixel-ratio: 1.5),(min-device-pixel-ratio: 1.5)").matches)
    );

function noClicks() {
    if (pauseListeningToClicks) {
        return true;
    } else {
        pauseListeningToClicks = true;
        setTimeout(function () {
            pauseListeningToClicks = false;
        }, 500);
        return false;
    }
}

$(document).ready(function () {

    /* Initialization */

    linkConfim();
    openExternalLinks();
    openButtonUrl();
    attachCloseModalPopupEvent();
    detectSVG();

    if (isMobileDevice()) {
        $('body').addClass('touch-mode');
    }

    if (hasMouse()) {
        $('body').addClass('has-mouse');
    }

    // Detect swipe - used to see if touchend is end of normal touch

    $("body").on("touchmove", function () {
        touchMoveCounter = touchMoveCounter + 1;
        if (touchMoveCounter > 2) {
            isSwipeTakingPlace = true;
        }
    });

    $("body").on("touchstart", function () {
        touchMoveCounter = 0;
        isSwipeTakingPlace = false;
    });

    // :hover does not exist on touch devices, emulate hover with JS where necessary

    $('body').not('.has-mouse').find('.hoverable, ul.listBone li').on('touchstart', function () {
        // second touch
        if ($(this).hasClass('hover')) { // already hovered
            return true;
        } else {
            // first touch
            $('.hoverable, ul.listBone li').removeClass('hover'); // remove other hovers
            $(this).addClass('hover'); // set this hover
            return false;
        }

    });

    // Prevent mobile back-forward cache

    window.onpageshow = function (event) {
        if (event.persisted) {
            window.location.reload()
        }
    };

    /* SVG detection */

    function supportsSVG() {
        return !!document.createElementNS && !!document.createElementNS('http://www.w3.org/2000/svg', 'svg').createSVGRect;
    }

    function detectSVG() {
        if (!supportsSVG()) {
            $('html').addClass('no-svg');
        }
    }

    /* Functions */

    /**
     * Displays a confirmation message box.
     */
    function linkConfim()
    {
        $('a[data-confirm]').click(function () {
            var msg = $(this).data('confirm');
            return (confirm(msg) == true);
        });
    }

    /**
     * Opens external links in a blank window.
     */
    function openExternalLinks()
    {
        $('a[rel="external"]').click(function () {
            window.open($(this).attr('href'), '_blank');
            return false;
        });
    }

    /**
     * Opens URLs on form button click.
     */
    function openButtonUrl()
    {
        $('input[type="button"][data-href]').click(function () {
            window.location = $(this).data('href');
            return false;
        });
    }

    /* End */

});

/* Global Functions */

/**
 * Closes the popup by pressing the Close button.
 */
function attachCloseModalPopupEvent()
{
    try
    {
        $('input[type="button"].button.close, .actionBone a.close').on('touchend click', function () {
            if (isSwipeTakingPlace) {
                return false;
            }
            $(this).off('click touchend');
            if (self != top) {
                parent.$.smartModalWindow().close();
            } else {
                window.location = '/m/refresh/';
            }
            return false;
        });
    }
    catch (e)
    {

    }
}

/**
 * Changes the title of the Colorbox popup.
 */
function changeColorboxPopupTitle(title)
{
    if (self == top)
        return false;
    var theTitle = (typeof title !== 'undefined') ? title : document.title;
    if (parent.$('#modal-header').length) parent.$('#modal-header').text(theTitle);
}

/**
 * Resizes the Colorbox popup to fit contents.
 */

function resizeColorboxPopupForContents()
{
    if (self == top) {
        return false;
    }

    var newWidth = parent.$('#modal-iframe').width();
    var newHeight = $(document).outerHeight();

    if (newHeight > $('html').outerHeight())
    {
        if (parent.$('#modal').length) {
            parent.$.smartModalWindow().resize({
                innerWidth: newWidth.toString() + 'px',
                innerHeight: newHeight.toString() + 'px'
            });
        }

    }

    return true;
}

/**
 * Resizes the Colorbox popup.
 */
function resizeColorboxPopup(width, height, minWidth, minHeight)
{
    if (self == top) {
        $('body').css('padding-top', '15px').addClass('fullscreen');
        return false;
    }

    if (typeof width != 'undefined' && typeof height != 'undefined')
    {
        var newWidth = (width < 1) ? ($(parent).width() * width) : width;  // < 1 can be for relative / percent values
        var newHeight = (height < 1) ? ($(parent).height() * height) : height;

        if (typeof minWidth !== 'undefined')
            newWidth = (newWidth >= minWidth) ? newWidth : minWidth;
        if (typeof minHeight !== 'undefined')
            newHeight = (newHeight >= minHeight) ? newHeight : minHeight;

        if (newWidth > ($(parent).width() - 30))
            newWidth = $(parent).width() - 30;
        if (newHeight > ($(parent).height() - 60))
            newHeight = $(parent).height() - 60;

        if (newWidth > 0 && newHeight > 0)
        {
            if (parent.$('#modal').length) {
                parent.$.smartModalWindow().resize({
                    innerWidth: newWidth.toString() + 'px',
                    innerHeight: newHeight.toString() + 'px'
                });
            }

        }
    }

    // Ensures the footer stays at the bottom.

    if ($('#footer').length) {

        $('#footer')
            .css('position', 'fixed')
            .css('bottom', 0)
            .css('left', 0)
            .css('right', 0);

        $('body').css('margin-bottom', $('#footer').outerHeight());

    }

    return true;
}

function doNotUsePopups()
{
    if (window.matchMedia) {
        if (!window.matchMedia("(min-width: 750px)").matches || !window.matchMedia("(min-height: 500px)").matches) {
            return true;
        }
    }
    return false;
}

function showPremiumPopup(options)
{
    var defaults = {
        sender: null,
        required: null,
        height: 550,
        width: 1100,
        resellerBuyLink: null
    };

    if (options.sender == 'shop') {
        defaults.height = 570;
    }

    var options = $.extend({}, defaults, options);

    if (options.resellerBuyLink && options.resellerBuyLink != '') {
        window.location = options.resellerBuyLink;
        return;
    }

    var urla = '/m/premium/';

    if (options.sender || options.required) {
        urla += '/params/';
    }
    if (options.sender) {
        urla += 'sender/' + options.sender + '/';
    }
    if (options.required) {
        urla += 'required/' + options.required + '/';
    }

    $.smartModalWindow({
        href: urla,
        innerWidth: options.width,
        innerHeight: options.height
    });
}

/* Show loading screen */

function showLoadingScreen() {
    $('body').append($('<div>').attr("id", "loading-screen"));
}

function hideLoadingScreen() {
    $('div#loading-screen').remove();
}

/**
 * Encodes an ISO-8859-1 string to UTF-8.
 * http://phpjs.org/functions/utf8_encode:577
 */
function utf8_encode(argString)
{
    if (argString === null || typeof argString === 'undefined')
        return '';

    var string = (argString + '').replace(/\r\n/g, '\n').replace(/\r/g, '\n');
    var utftext = '',
        start, end, stringl = 0;

    start = end = 0;
    stringl = string.length;
    for (var n = 0; n < stringl; n++)
    {
        var c1 = string.charCodeAt(n);
        var enc = null;

        if (c1 < 128)
            end++;
        else if (c1 > 127 && c1 < 2048)
            enc = String.fromCharCode((c1 >> 6) | 192, (c1 & 63) | 128);
        else
            enc = String.fromCharCode((c1 >> 12) | 224, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128);

        if (enc !== null)
        {
            if (end > start)
                utftext += string.slice(start, end);
            utftext += enc;
            start = end = n + 1;
        }
    }

    if (end > start)
        utftext += string.slice(start, stringl);
    return utftext;
}

/* Detection utils */

/* Color utils */

function colorToHex(color)
{
    function hex(x) {
        return ("0" + parseInt(x).toString(16)).slice(-2);
    }

    if (color == "transparent" || color === false) {
        return "#FFFFFF";
    }
    if (color.search("rgb") == -1) {
        return color;
    }
    else {
        color = color.match(/^rgb(?:a)?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d*\.?\d+))?\)$/);
        if (color == null || (color.length > 4 && color[4] == "0")) {
            return "#FFFFFF";
        }
        return "#" + hex(color[1]) + hex(color[2]) + hex(color[3]);
    }
}

// from http://24ways.org/2010/calculating-color-contrast/
function isColorTooLightYIQ(hexcolor)
{
    if (hexcolor.charAt(0) != '#')
        return false;
    hexcolor = hexcolor.substr(1, 6);
    var r = parseInt(hexcolor.substr(0, 2), 16);
    var g = parseInt(hexcolor.substr(2, 2), 16);
    var b = parseInt(hexcolor.substr(4, 2), 16);
    var yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
    return yiq >= 128;
}

/* Detect browser */
var detectedBrowser = detectWebBrowser();
// TODO: Combine all browser detection functions into one object

function detectWebBrowser() {
    /* code form jQuery */
    var matched, browser;

    uaMatch = function (ua) {
        ua = ua.toLowerCase();
        var match = /(chrome)[ \/]([\w.]+)/.exec(ua) ||
            /(webkit)[ \/]([\w.]+)/.exec(ua) ||
            /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) ||
            /(msie) ([\w.]+)/.exec(ua) ||
            ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) ||
            [];

        return {
            browser: match[ 1 ] || "",
            version: match[ 2 ] || "0"
        };
    };

    matched = uaMatch(navigator.userAgent);
    browser = {};

    if (matched.browser) {
        browser[ matched.browser ] = true;
        browser.version = matched.version;
    }

    // Chrome is Webkit, but Webkit is also Safari.
    if (browser.chrome) {
        browser.webkit = true;
    } else if (browser.webkit) {
        browser.safari = true;
    }

    return browser;

}

/* Detect mobiles */
function isMobileDevice() {
    if (navigator.userAgent.match(/Android/i) ||
        navigator.userAgent.match(/webOS/i) ||
        navigator.userAgent.match(/iPhone/i) ||
        navigator.userAgent.match(/iPad/i) ||
        navigator.userAgent.match(/iPod/i) ||
        navigator.userAgent.match(/BlackBerry/i) ||
        navigator.userAgent.match(/Windows Phone/i) ||
        navigator.userAgent.match(/IEMobile/i)
        ) {
        return true;
    } else {
        return false;
    }
}

function isiOS() {
    if (navigator.userAgent.match(/iPhone/i) ||
        navigator.userAgent.match(/iPad/i) ||
        navigator.userAgent.match(/iPod/i)
        ) {
        return true;
    } else {
        return false;
    }
}

function isSmallMobileDevice() {
    var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    return (isMobileDevice() && (w <= 750 || h <= 500));
}

function hasMouse() {   // this needs to be rewritten
    return !isMobileDevice();
}

/* Preloader - preloads images and some common CSS */

function preLoadBackend(MOZELLO_CDN, MOZELLO_VERSION) {
    setTimeout(function () {

        // backend css
        var xhr = new XMLHttpRequest();
        xhr.open('GET', MOZELLO_CDN + '/backend/css/backend.css?v=' + MOZELLO_VERSION);
        xhr.send('');

        // popup css
        xhr = new XMLHttpRequest();
        xhr.open('GET', MOZELLO_CDN + '/backend/css/popup.css?v=' + MOZELLO_VERSION);
        xhr.send('');

        // signed in backend css
        xhr = new XMLHttpRequest();
        xhr.open('GET', MOZELLO_CDN + '/backend/css/fullscreen.css?v=' + MOZELLO_VERSION);
        xhr.send('');

        // main topbar images
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-pages-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-blogs-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-design-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-cart-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-world-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-settings-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-view-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-rocket-launch-white.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-logout-light.svg";

        // main topbar submenu images
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-font-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-paint-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-arrow-right-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-arrow-down-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-house-light.svg";

        // overlay images
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-add-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-remove-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-edit-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-back-light.svg";

        // thebutton images
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-wrench-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-grid-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-webform-light.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-data-light.svg";

        // wys toolbar images
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-align-left-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-align-center-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-align-right-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-align-justify-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-undo-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-redo-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-bold-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-italic-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-underline-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-bullets-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-numbers-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-image-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-link-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-insert-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-table-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-reset-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-textcolor-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-html-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-filesplit-dark.svg";

        // wys submenu images
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-attach-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-video-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-maps-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-symbol-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-code-dark.svg";

        // tables
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-table-addcolumnbefore.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-table-addcolumnafter.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-table-deletecolumn.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-table-addrowbefore.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-table-addrowafter.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-table-rowstyle.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-table-deleterow.svg";

        // images
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-resize-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-wrench-dark.svg";
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-delete-dark.svg";

        // misc
        new Image().src = MOZELLO_CDN + "/backend/css/toolbar-grip.svg";
        new Image().src = MOZELLO_CDN + "/backend/css/dropdown-arrow.svg";

        // colorbox
        new Image().src = MOZELLO_CDN + "/libs/icons/icon-cross.svg";

    }, 1000);
}

/* CSS3 animation trigger */

function execCSS3Animation(jqelem, animation, hide, delay) {
    if (!delay)
        delay = 500;
    jqelem.addClass(animation);
    setTimeout(function () {
        if (hide)
            jqelem.hide();
        jqelem.removeClass(animation);
    }, delay);
}

/* Checks if backend session is still alive */
function checkConnectionLost(disconnectCallback)
{
    $.ajax({
        url: '/m/ajax-is-alive/',
        type: 'post',
        success: function (data) {
            if (data !== 'alive')
            {
                disconnectCallback();
            }
        },
        error: function (xhr, textStatus, thrownError) {
            if (textStatus != 'abort') {
                disconnectCallback();
            }
        }
    });
}

/* Checks if file is an image (for uploads) */

function isImageFile(fname) {
    var _validFileExtensions = [".jpg", ".jpeg", ".gif", ".png"];
    for (var j = 0; j < _validFileExtensions.length; j++) {
        var sCurExtension = _validFileExtensions[j];
        if (fname.substr(fname.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
            return true;
        }
    }
    return false;
}

var exeFileExtensions = ["exe", "msi", "msp", "scr", "cpl", "bat", "cmd", "com", "pif", "app", "osx", "apk", "ipa", "htm", "html", "js"];

function getFileExt(fileName) {
    var a = fileName.split(".");
    if (a.length === 1 || (a[0] === "" && a.length === 2)) {
        return "";
    }
    return a.pop().toLowerCase();
}

function hasFileExtension(fileName, fileExtensions) {
    var fileExt = getFileExt(fileName);
    for (var j = 0; j < fileExtensions.length; j++) {
        var sCurExtension = fileExtensions[j];
        if (fileExt == fileExtensions[j]) {
            return true;
        }
    }
    return false;
}

/* File upload error processor (local) */

function fileuploadOnError(error) {
    $('#upload-progress').addClass('failed');
    if (error != '') {
        $('#upload-status-message').html('Error: ' + error);
    } else {
        $('#upload-status-message').html(MGA_STATUS_UPLOAD_FAILED);
    }
}

/* Show / hide progress */

function showProgressPopover() {
    if (!$('#loading-popover').length) $('body').append($('<div>').attr('id', 'loading-popover'));
    $('#loading-popover').fadeIn();
}

function hideProgressPopover() {
    $('#loading-popover').hide();
}

/* Get unique array items */

function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
}
// usage: var uArr = rArr.filter( onlyUnique );


/* Truncate text, add ellipsis at last word break */

function truncateTextWithEllipsis(s, n, useWordBoundary) {
    var isTooLong = s.length > n,
        s_ = isTooLong ? s.substr(0,n-1) : s;
        s_ = (useWordBoundary && isTooLong) ? s_.substr(0,s_.lastIndexOf(' ')) : s_;
    return  isTooLong ? s_ + '&hellip;' : s_;
};

function mozelloConfirm(text, btnOk, btnCancel, okHandler, cancelHandler)
{
    var html = [
        '<div id="mozelloConfirmForm" style="padding: 0px 35px 35px 35px;">',
        '    <form class="formBone" method="post">',
        '        <div class="row">',
        '            <p style="font-size: 1.15em">' + text + '</p>',
        '        </div>',
        '        <div id="footer" style="position: absolute; padding-bottom: 35px; bottom: 0;">',
        '            <ul class="actionBone">',
        '                <li><input class="button submit" type="button" value="' + btnOk + '"></li>',
        '                <li><input class="button cancel secondary" type="button" value="' + btnCancel + '"></li>',
        '            </ul>',
        '        </div>',
        '    </form>',
        '    <div style="clear: both"></div>',
        '</div>',
    ].join('');

    $.smartModalWindow({
        width: 515,
        height: 250,
        inline: true,
        href: html,
        onComplete: function() {
            var source = $('#mozelloConfirmForm');
            source.find('input.button.submit').off().click(function() {
                if (typeof okHandler == 'function' && okHandler(source) === false) {
                    return false;
                }
                $.smartModalWindow().close();
                return false;
            });
            source.find('input.button.cancel, #modal-close').off().click(function() {
                if (typeof cancelHandler == 'function' && cancelHandler(source) === false) {
                    return false;
                }
                $.smartModalWindow().close();
                return false;
            });
        },
        onCleanup: function() {
            var source = $('#mozelloConfirmForm');
            if (typeof cancelHandler == 'function') {
                if (cancelHandler(source) === false) {
                    return false;
                };
            }
        }
    });
}

/* Simple popup window  */

// todo: Add ESC close

(function (window, $) {

    function winheight() {
        return window.innerHeight ? window.innerHeight : $(window).height();
    }

    function setSize(size, dimension) {
        return Math.round((/%/.test(size) ? ((dimension === 'x' ? $(window).width() : winheight()) / 100) : 1) * parseInt(size, 10));
    }

    var smartModalWindowObject = {

        defaults: {
            fullscreen: false,
            innerWidth: false,
            innerHeight: false,
            maxWidth: false,
            maxHeight: false,
            title: false,
            inline: false,
            onClosed: false,
            onCleanup: false,
            onComplete: false,
            width: 500,
            height: 500,
            result: null
        },

        onkeyup: function(e) {
            var base = smartModalWindowObject;
            if (e.keyCode == 27) { // escape key
                base.close();
            }
        },

        setResult: function(result) {
            this.options.result = result;
        },

        close: function() {

            var base = this;

            // return content to its place
            if (base.options.inline) {
                if (base.inlineContentParent) {
                    base.inlineContentParent.append(base.inlineContent);
                    base.inlineContentParent = false;
                    base.inlineContent = false;
                }
            }

            // execute onCleanup event
            if (base.options.onCleanup) {
                base.options.onCleanup();
            }

            // remove modal window
            $('#modal').remove();
            $('body').removeClass('modal-fullscreen');

            // release resize processor
            $(window).off('resize', base.resize);
            $(document).off('keyup', base.onkeyup);

            // execute onClosed event
            setTimeout(function () {
                if (base.options.onClosed) {
                    base.options.onClosed();
                }
                // reset global properties
                base.options.onClosed = false;
                base.options.onCleanup = false;
            }, 1);

        },

        resize: function (settings) {

            var base = smartModalWindowObject;
            var modal_window = base.modal_window;

            if (typeof settings != 'undefined') {
                if (typeof settings.innerWidth != 'undefined') {
                    base.options.innerWidth = settings.innerWidth;
                }
                if (typeof settings.innerHeight != 'undefined') {
                    base.options.innerHeight = settings.innerHeight;
                }
                if (typeof settings.maxWidth != 'undefined') {
                    base.options.maxWidth = settings.maxWidth;
                }
                if (typeof settings.maxHeight != 'undefined') {
                    base.options.maxHeight = settings.maxHeight;
                }
                if (typeof settings.width != 'undefined') {
                    base.options.width = settings.width;
                    base.options.innerWidth = false;
                }
                if (typeof settings.height != 'undefined') {
                    base.options.height = settings.height;
                    base.options.innerHeight = false;
                }
            }

            calculatedW = setSize(base.options.width, 'x');
            calculatedH = setSize(base.options.height, 'y');

            if (doNotUsePopups() || base.options.fullscreen) {
                $('body').addClass('modal-fullscreen');
                calculatedW = setSize('100%', 'x');
                calculatedH = setSize('100%', 'y');
            } else {
                $('body').removeClass('modal-fullscreen');
                if (base.options.innerWidth) {
                    calculatedW = setSize(base.options.innerWidth, 'x');
                }
                if (base.options.innerHeight) {
                    calculatedH = setSize(base.options.innerHeight, 'y');
                    if (!base.options.fullscreen) {
                        calculatedH = calculatedH + $('#modal-header').outerHeight();
                    }
                }
                if (base.options.maxWidth) {
                    calculatedW = Math.min(calculatedW, setSize(base.options.maxWidth, 'x'));
                }
                if (base.options.maxHeight) {
                    calculatedH = Math.min(calculatedH, setSize(base.options.maxHeight, 'y'));
                }
            }

            var calculatedW, calculatedH;

            if ((calculatedW) > $(window).width()) {
                calculatedW = $(window).width();
            }
            if ((calculatedH) > winheight()) {
                calculatedH = winheight();
            }
            modal_window.width(calculatedW);
            modal_window.height(calculatedH);
            var ileft = ($(window).width() - calculatedW) / 2;
            var itop = (winheight() - calculatedH) / 2;
            modal_window.css({ top: itop, left: ileft, right: 'auto' });  // set right and not left to work around bug in Chrome
        },

        show: function () {

            var base = this;

            // build elements
            var modal = $('<div>').attr('id', 'modal').hide();
            var modal_overlay = $('<div>').attr('id', 'modal-overlay');
            var modal_window = $('<div>').attr('id', 'modal-window').hide();
            var modal_workspace = $('<div>').attr('id', 'modal-workspace');
            var modal_close = $('<button>').attr('id', 'modal-close').attr('type', 'button');


            base.modal_window = modal_window;

            if (!base.options.fullscreen) {
            var modal_header = $('<div>').attr('id', 'modal-header');
                if (base.options.title) {
                    modal_header.text(base.options.title);
                }
                modal_window.append(modal_header).append(modal_close);
            } else {
                modal_workspace.css({top: 0});
            }

            if (base.options.inline) {
                var modal_content = $('<div>').attr('id', 'modal-content');
                modal_workspace.append(modal_content);
            } else {
                var modal_iframe = $('<iframe>').attr('id', 'modal-iframe').attr('width', '100%');
                modal_workspace.append(modal_iframe);
            }
            modal_window.append(modal_workspace);

            // attach close method
            modal_overlay.on('click touchend', function() {
                base.close();
            });
            modal_close.on('click touchend', function() {
                if (isSwipeTakingPlace) {
                    return false;
                }
                $(this).off('click touchend');
                base.close();
                return false;
            });

            // put everything together
            modal.append(modal_overlay).append(modal_window);
            $('body').append(modal);

            // resizing
            base.resize();

            // events
            $(window).resize(base.resize);
            $(document).keyup(base.onkeyup);

            // present window
            modal.show();
            modal_window.fadeIn(200);

            if (!base.options.inline) {
                modal_iframe.attr('src', base.options.href).on('load', function(){
                    $('#modal-workspace').css('background-image', 'none');
                    // execute onCleanup event
                    if (base.options.onComplete) {
                        base.options.onComplete();
                    }
                });
            } else {
                // load inline - must come after put everything together
                if (base.options.inline) {
                    var inline_content = $(base.options.href);
                    if (inline_content.length) {
                        inline_content = inline_content.first();
                        base.inlineContent = inline_content;
                        base.inlineContentParent = inline_content.parent();
                    } else {
                        base.inlineContent = false;
                        base.inlineContentParent = false;
                    }
                    inline_content.appendTo(modal_content);
                    modal_workspace.css('background-image', 'none');
                    // execute onCleanup event
                    if (base.options.onComplete) {
                        base.options.onComplete();
                    }
                }
            }

        },

        init: function(settings) {

            var base = this;

            if (typeof settings != 'undefined') {
                base.options = $.extend({}, base.defaults, settings);
            }

            // create only if does not exist
            if (!$('#modal').length) {
                base.show();
            }

        }

    };

    $.smartModalWindow = $.fn.smartModalWindow = function(settings) {
        $this = this;

        if ($.isFunction($this)) {    // $.plugin() direct call
            smartModalWindowObject.init(settings);
            return smartModalWindowObject;
        } else if (!$this[0]) {   // empty collection
            return $this;
        }

        return $this.each(function() {
            $(this).on('click', function() {
                settings.href = $(this).attr('href');
                settings.title = $(this).attr('title');
                smartModalWindowObject.init(settings);
                return false;
            });
        });

    };

}) (window, jQuery);

/* End */