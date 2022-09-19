$(document).ready(function () {

    /*** Content and speed optimization ***/

    // make it harder for bots to harvest emails
    $('a.nospam').each(function() {
        var user = $(this).text();
        var domain = $(this).data('domain');
        if (!domain) {
            domain = 'mozello.com';
        }
        var email = user + '@' + domain;
        $(this).attr('href', 'mailto:' + email).text(email);
    });

    // lazy load images
    //$("img").unveil(500);
    $('img[data-src]').each(function(){
        $(this).attr('src', $(this).data('src'));
    });

    // lazy load of secondary CSS for faster load times
    $('link[disabled]').removeAttr('disabled');


    /*** Scrolling and fixed menu ***/

    // scrolled
    var scrollThreshold = 1;

    // adds shadow
    function updateScrollState() {
        var scrollTop = $(document).scrollTop();
        if (scrollTop > scrollThreshold) {
            $('body').addClass('scrolled');
        } else {
            $('body').removeClass('scrolled');
        }
    }

    // fixed menu only for inner pages
    $(window).on('resize scroll', function () {

        if ($(window).scrollTop() > scrollThreshold) {
            $('#headerMenu').addClass('fixed');
            // if not slice page
            if ($('#headerPage, #headerMain').length) {
                $('#headerPage, #headerMain').css('margin-top', $('#headerMenu').height());
            } else {
                $('#content').css('margin-top', $('#headerMenu').height());
            }
        } else {
            $('#headerMenu').removeClass('fixed');
            $('#headerPage, #headerMain, #content').css('margin-top', '');
        }

        updateScrollState();
    });

    /*** Sticky side menu for articles ***/

    function initStickySideMenu() {

        if ($('#article').length) {

            var step = 1;
            $('#article h2').each(function()
                {
                    var id = 'step' + (step++).toString();
                    var text = $(this).text();
                    $(this).attr('id', id);
                    $('#sidemenu').append($('<a>').attr('href', '#' + id).text(text));
                }
            );

            /*** Fixable side menu ***/

            var sidePanelScrollThreshold = $('#sidepanel').offset().top - $('#header').height();
            var sidePanelTop = $('#article').position().top + $('#header').height();

            $(window).on('resize scroll', function ()
                {
                    if (($(window).scrollTop() > sidePanelScrollThreshold)) {
                        if ($(window).scrollTop() > ($(document).height() - $('#footer').height() - 600)) {
                            $('#sidepanel').removeClass('fixed');
                            $('#sidepanel').css('top', '');
                        } else {
                            $('#sidepanel').addClass('fixed');
                            $('#sidepanel').css('top', sidePanelTop);
                        }
                    } else {
                        $('#sidepanel').removeClass('fixed');
                        $('#sidepanel').css('top', '');
                    }
                }
            );

        }

    }

    initStickySideMenu();


    /*** Init local scroll plugin - after sticky menus are set ***/

    $.localScroll({
        duration: 400,
        hash: true,
        offset: -($('#headerMenu').height() + 20)
    });


    /*** Mobile menu ***/

    $('a#menuOpener').on('click', function () {
        $('#mobileMenu').show();
        return false;
    });

    $('#menuCloser').on('click', function () {
        $('#mobileMenu').css('display', '');
        return false;
    });

    $('#language > a').on('touchend', function () {
        $('#languages').toggle();
        $('body').one('touchend', function (e) {
            if (!$(e.target).filter('#languages a').length) {
                $('#languages').hide();
            }
        });
        return false;
    });

    $('#language').on('mouseenter', function () {
        $('#languages').show();
    });

    $('#language').on('mouseleave', function () {
        $('#languages').hide();
    });




    /*** Fancybox ***/

    $("a.fancybox").fancybox({

    });

    $.fancybox.defaults.buttons = [
        'slideShow',
        'thumbs',
        'close'
    ];

    $("a.video").fancybox({
        fitToView: true,
        width: 896,
        height: 504,
        autoSize: true,
        closeClick: false,
        openEffect: 'none',
        closeEffect: 'none'
    });

    $('img.canzoom').click(function(){
        img = $(this);
        $.fancybox.open({
            src  : img.attr('src'),
            opts : {
                caption : img.attr('alt')
            }
        });
    });


    /*** Transition ***/

    $('a.button:not(.video), #login').on('click touchend', function () {
        $('body').addClass('transition-go-inside');
    });


    /*** Reviews ***/

    var stopReviewCarousel = false;
    $('.overlay-dots span:first-child svg').addClass('selected');

    function activateReviewItem(item) {
        $('.overlay-dots svg').removeClass('selected');
        $(item).children('svg').addClass('selected');
        var i = $(item).index();
        $('.overlay-container div.review:visible').fadeOut(200, function(){
            $($('.overlay-container div.review').get(i)).fadeIn(200);
        });
    }

    function nextReview() {
        if (!stopReviewCarousel) {
            var selected = $('.overlay-dots .selected').parent();
            var index = selected.index();
            if (index == $('.overlay-dots span').length - 1) {
                index = 0;
            } else {
                index++;
            }
            activateReviewItem($('.overlay-dots span').get(index))
            setTimeout(function() {
                nextReview();
            }, 6000);
        }
    }

    $('.overlay-dots span').click(function() {
        stopReviewCarousel = true;
        activateReviewItem(this);
    });

    setTimeout(function() {
        nextReview();
    }, 6000);


    /*** Image rotator ***/

    var productImage = 1;
    var productImageMax = 4;

    function nextProductImage() {

        // do not rotate before into view
        if ($('.overlay-product').hasClass('uk-scrollspy-inview')) {
            // switch image
            if (productImage < productImageMax) {
                productImage++;
            } else {
                productImage = 1;
            }
            // remove uikit animation to avoid conflict
            $('.overlay-product').removeClass('uk-animation-slide-left-medium');
            // change image via fade
            $('.overlay-product').css('opacity', '0' );
            setTimeout(function(){
                $('.overlay-product').css('background-image', 'url(' + MOZELLO_CDN + '/website/images/store-item-' + productImage + '.jpg)');
                $('.overlay-product').css('opacity', '1');
            }, 200);

        }

        setTimeout(function() {
            nextProductImage();
        }, 3000);
    }

    // preload images after a small delay
    setTimeout(function() {
        var images = new Array();
        for (i = 1; i < productImageMax + 1; i++) {
            images[i] = new Image()
            images[i].src = MOZELLO_CDN + '/website/images/store-item-' + i + '.jpg';
        }
    }, 1500);

    setTimeout(function() {
        nextProductImage();
    }, 3000);

    /* End */

});