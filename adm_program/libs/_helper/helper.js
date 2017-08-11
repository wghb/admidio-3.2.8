define(['jquery', 'jqueryAutocomplete', 'chosen'], function ($) {

    var touchClickEvent = 'click';
    var touchStartEvent = 'mousedown';
    var touchEndEvent = 'mouseup';
    var touchMoveEvent = 'mousemove';
    var touchScrollEvent = 'scroll';

    /**
     *  User-Agent test for mobile devices
     *
     *  @returns {boolean}
     */
    function isMobile() {
        return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) ? true : false;
    }

    function setMobileEvents() {
        // return if IEMobile => IEMobile doesnt support touch-Events
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

    /**
     *  Detect Internet Explorer
     *
     *  @returns {{isTheBrowser: boolean, actualVersion: *}}
     */
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
});