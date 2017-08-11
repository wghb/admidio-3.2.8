$(function () {
    // Highlight the top nav as scrolling occurs
    $('body').scrollspy({
        target: '.navbar-fixed-top',
        offset: 51
    });

    // Offset for Main Navigation
    $('#main').affix({
        offset: {
            top: 50
        }
    });

    var helper = (function () {
        var touchClickEvent = 'click';
        var touchStartEvent = 'mousedown';
        var touchEndEvent = 'mouseup';
        var touchMoveEvent = 'mousemove';
        var touchScrollEvent = 'scroll';

        function isMobile() {
            return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) ? true : false;
        }

        function setMobileEvents() {
            if (/IEMobile/i.test(navigator.userAgent))
                return;

            touchClickEvent = 'touchstart';
            touchStartEvent = 'touchstart';
            touchEndEvent = 'touchend';
            touchMoveEvent = 'touchmove';
            touchScrollEvent = 'touchmove';
        }

        function getTouchClickEvent() {
            return touchClickEvent;
        }

        function getTouchStartEvent() {
            return touchStartEvent;
        }

        function getTouchEndEvent() {
            return touchEndEvent;
        }

        function getTouchMoveEvent() {
            return touchMoveEvent;
        }

        function getTouchScrollEvent() {
            return touchScrollEvent;
        }

        function getDomain() {
            return window.location.protocol + '//' + window.location.host;
        }

        function detectIE() {
            var agent = navigator.userAgent;
            var reg = /MSIE\s?(\d+)(?:\.(\d+))?/i;
            var matches = agent.match(reg);
            var isTheBrowser = false;
            var version = {
                major: -1,
                minor: -1
            };

            if (matches) {
                version = {
                    major: matches[1],
                    minor: matches[2]
                };
                isTheBrowser = (version.major === 9 || version.major === 10);
            }

            return {
                isTheBrowser: isTheBrowser,
                actualVersion: version.major
            };
        }

        return {
            isMobile: isMobile,
            setMobileEvents: setMobileEvents,
            touchClickEvent: getTouchClickEvent,
            touchStartEvent: getTouchStartEvent,
            touchEndEvent: getTouchEndEvent,
            touchMoveEvent: getTouchMoveEvent,
            touchScrollEvent: getTouchScrollEvent,
            detectIE: detectIE,
            getDomain: getDomain
        };
    })();

    // navigation sidebar
    $('[data-toggle=sidebar]').on('click', function (e) {
        $('.sidebar').toggleClass('active');
        $('.black-canvas').toggleClass('active');
        $('.wrapper').toggleClass('active');
        $('body').toggleClass('scroll-lock');
        e.preventDefault();
    });

    // change event-names on mobile-devices
    if (helper.isMobile())
        helper.setMobileEvents();

    // Scroll to-top-button
    $('.btn-to-top').on(helper.touchClickEvent(), function () {
        var duration = 500;
        $.scrollTo({top: 0}, duration, {
            axis: 'y'
        });

        setTimeout(function () {
            $.scrollTo({top: 0}, 100);
        }, duration + 75);
    });

    // show fixed navbar on scrollup
    setInterval(function () {
        var scrollY = $(document).scrollTop();
//        // only execute this on mobile devices (excluding tablets)
//        if ($(window).innerWidth() < 720) {
        // SHOW/HIDE to-top-btn
        if (scrollY > 200) {
            $('.btn-to-top').fadeIn('fast');
        } else {
            $('.btn-to-top').fadeOut('fast');
        }
//        } else {
//            if ($('.btn-to-top').is(':visible')) {
//                $('.btn-to-top').hide();
//            }
//        }


        //show mobile navigation for mobile devices
        if (scrollY > 100) {
            if (!$(".right-sidebar-navigation").hasClass('in-content')) {
                $('.right-sidebar-navigation .right-sidebar-link').removeClass('right-sidebar-link-not-visible');
                $(".right-sidebar-navigation").addClass('in-content');
            }
        } else {
            if ($(".right-sidebar-navigation").hasClass('in-content')) {
                $('.right-sidebar-navigation .right-sidebar-link').removeClass('right-sidebar-link-not-visible');
                $(".right-sidebar-navigation").removeClass('in-content');
            }
        }
    }, 500);

    if (document.querySelector('.right-sidebar-btn')) {
        initMobileNavigation();
    }

    $(document).on('touchmove', function () {
        var navbarfixed = $('.navbar-fixed');
        if ('down' == 'up') {
            // scrolling down => hide fixed menu
            navbarfixed.hide();
        }
    });

    function initMobileNavigation() {
        // right sidebar-button event (show mobile navigation links)
        $('.right-sidebar-btn').on(helper.touchClickEvent(), function () {

            //show sidebar navigation
            $('.right-sidebar-navigation').addClass('right-sidebar-navigation-active');
            //show black background
            $('.wrapper').addClass('wrapper-disabled');
            $('html').addClass('stop-scrolling');
            return false;
        });

        //disabled black background click event
        $('.disabled-background-disable').on(helper.touchClickEvent(), function (event) {
            if ($(event.target).is('.right-sidebar-navigation .right-sidebar-link')
                    || $(event.target).is('.right-sidebar-navigation .right-sidebar-content')) {
                return false;
            }

            //hide mobile navigation / Right sidebar google analytics clickevent
            if ($('.right-sidebar-navigation').hasClass('right-sidebar-navigation-active')) {
                $('.right-sidebar-navigation').removeClass('right-sidebar-navigation-active');
            }

            //hide mobile navigation / right sidebar
            disableRightSidebar();
            return false;
        });

        var disableRightSidebar = function () {
            $('.right-sidebar-navigation .right-sidebar-link a').removeClass('bound-active');
            $('.wrapper').removeClass('wrapper-disabled');
            $('.wrapper').removeClass('sidebar-right');
            $('.theme-top').removeClass('sidebar-right');
            $('.theme-right').removeClass('sidebar-right');
            $('nav').removeClass('sidebar-right');
            $('body, html').removeClass('stop-scrolling');
        };

        //show sidebar navigation
        $('a[data-sidebar]').on(helper.touchClickEvent(), function (event) {
            $('.right-sidebar-navigation').removeClass('right-sidebar-navigation-active');
            $('.wrapper').addClass('sidebar-right');
            $('.theme-top').addClass('sidebar-right');
            $('.theme-right').addClass('sidebar-right');
            $('nav').addClass('sidebar-right');
            $(this).addClass('bound-active');

            //show the correct content container
            $('.wrapper .right-sidebar-content > div.contentcontainer').hide();
            $('.wrapper .right-sidebar-content').find('div.contentcontainer.' + $(this).attr('data-sidebar')).show();

            //show the correct sticky-headline container
            $('.wrapper .right-sidebar-content > div.sticky-headline').hide();
            $('.wrapper .right-sidebar-content').find('div.sticky-headline.' + $(this).attr('data-sidebar')).show();

            $('.right-sidebar-navigation .right-sidebar-link').removeClass('right-sidebar-link-visible').removeClass('right-sidebar-link-not-visible');
            $('html').addClass('stop-scrolling');
            return false;
        });

        //jump to content index
        $('a.inhaltsverzeichnis_link').on('click', function (event) {
            location.hash = $(this).attr('href');
            disableRightSidebar();
            return true;
        });

        //add event listener for ios devices
        /*removeIOSRubberEffect(document.querySelector('div.contentcontainer.drugs'));
        removeIOSRubberEffect(document.querySelector('div.contentcontainer.related'));
        removeIOSRubberEffect(document.querySelector('div.contentcontainer.all_about'));
        removeIOSRubberEffect(document.querySelector('div.contentcontainer.inhaltsverzeichnis'));*/

        var deviceAgent = navigator.userAgent;
        var showMona = true;
        //die mobile navigation from iphone ios < 7
        if (/(iPhone|iPod|iPad)/i.test(deviceAgent)) {
            // supports iOS 2.0 and later: <http://bit.ly/TJjs1V>
            var appversion = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
            var version = [parseInt(appversion[1], 10), parseInt(appversion[2], 10), parseInt(appversion[3] || 0, 10)];
            if (version[0] >= 7) {
            } else {
                showMona = false;
            }
        }

        //deactivate mobile navigation from android < 2+3
        var agentIndex = deviceAgent.indexOf('Android');
        if (agentIndex != -1) {
            var androidversion = parseFloat(deviceAgent.match(/Android\s+([\d\.]+)/)[1]);
            if (androidversion >= 4.0)
            {
            } else {
                showMona = false;
            }
        }
        if (showMona) {
            $('.right-sidebar-navigation, .right-sidebar-content').show();
        }
    }
    function removeIOSRubberEffect(element) {
        element.addEventListener("touchstart", function () {
            var top = element.scrollTop,
                    totalScroll = element.scrollHeight,
                    currentScroll = top + element.offsetHeight;

            if (top === 0) {
                element.scrollTop = 1;
            } else if (currentScroll === totalScroll) {
                element.scrollTop = top - 1;
            }

        });
    }
});

$(window).scroll(function () {
    if ($("#main").offset().top > 50) {
        $("#main.navbar-fixed-top").addClass("top-nav-collapse");
    } else {
        $("#main.navbar-fixed-top").removeClass("top-nav-collapse");
    }
});