/*
 chameleonImageSlider modifies the content of a div so that you can replace it with content loaded via ajax in a slide animation
 the next and previous links must be marked with the class nextItemLink and previousItemLink respectively.
 if you need to use a different url to fetch content via ajax then what is used to reload the page with the next item
 active, then place the ajax url in the rel attribute of the links
 requires: jquery.dimensions.js and jquery.easing.js
 */
(function ($) {
    $.fn.chameleonImageSlider = function (options) {
        var defaults = {
            iOverhangRight:0,
            iSlideDurationInMS:1000,
            iOverhangDisplayDurationInMS:500,
            sEaseOutFunctionName:'easeOutExpo',
            iMaxDelayBeforePleaseWaitInMS:500,
            autoslideon:false, // Enables autolsiding
            autoslidetime:10000, // time ineterval for sliding
            bFixateWidth:true // you need to set this to true if you have floating content. if you use negative margin in the content you will want to turn this OFF!
        };
        var options = $.extend(defaults, options);
        return this.each(function () {
            var aItemCache = {};
            var obj = $(this);
            var iBlockContentWidth = obj.innerWidth();
            var iBlockContentHeight = obj.innerHeight();

            obj.children().children().children('img').load(function() {
                iBlockContentHeight = $(this).height();
                obj.css('height', $(this).height());
            });

            obj.wrapInner('<div style="position:relative;width:' + iBlockContentWidth + 'px;height:' + iBlockContentHeight + 'px;"><div class="slideContainer" style="position:absolute;width:' + iBlockContentWidth + 'px;height:' + iBlockContentHeight + 'px;"></div></div>');

            var oSlideContainer = $('.slideContainer', obj);
            oSlideContainer.children('div').css("position", "absolute").css("top", "0px").css("left", "0px");
            if (options.bFixateWidth) oSlideContainer.children('div').css("width", iBlockContentWidth);

            var addPleaseWaitBlock = (function () {
                if (options.iMaxDelayBeforePleaseWaitInMS >= 0) {
                    var blockData = '<div class="ChameleonImageSlidePleaseWait" style="background:url(/chameleon/blackbox/javascript/jquery/chameleon/pixelTransparentHalf.png) repeat transparent; z-index:50;position:absolute;overflow:hidden;width:' + iBlockContentWidth + 'px;height:' + obj.innerHeight() + 'px;"></div>';
                    obj.prepend(blockData);
                }
            });


            var customOnClickNextEvent = (function () {
                var oNextLinkObject = $(this);
                if (!oNextLinkObject) return false;
                var refTimeoutId = null;
                if (options.iMaxDelayBeforePleaseWaitInMS >= 0) refTimeoutId = setTimeout(addPleaseWaitBlock, options.iMaxDelayBeforePleaseWaitInMS);
                var ajaxURL = oNextLinkObject.attr('rel');
                if (ajaxURL == '') oNextLinkObject.attr('href');

                var fMoveNext = function (data, responseMessage) {
                    aItemCache[ajaxURL] = data;
                    if (options.iMaxDelayBeforePleaseWaitInMS >= 0) {
                        clearTimeout(refTimeoutId);
                        obj.children('.ChameleonImageSlidePleaseWait').remove();
                    }
                    obj.css('overflow', 'hidden');

                    var iOriginalInnerWidth = oSlideContainer.innerWidth();

                    // remove the overhang
                    oSlideContainer.parent().width(oSlideContainer.parent().innerWidth() - options.iOverhangRight);

                    // resize the slider
                    oSlideContainer.width((iOriginalInnerWidth + iBlockContentWidth));

                    //return false;

                    // add the new content
                    oSlideContainer.append(data.sItemPage);
                    oSlideContainer.children('div:last').css("position", "absolute").css("top", "0px").css("left", (iOriginalInnerWidth) + "px");
                    if (options.bFixateWidth) {
                        oSlideContainer.children('div:last').css("width", iOriginalInnerWidth+'px');
                    }
                    $('.previousItemLink', oSlideContainer).css('visibility', 'hidden');
                    $('.nextItemLink', oSlideContainer).css('visibility', 'hidden');


                    // set easing method - backup original so we can restore it
                    var sOriginalEasingMethod = jQuery.easing.def;
                    jQuery.easing.def = options.sEaseOutFunctionName;

                    // animate the replacement
                    $('.slideContainer', obj).animate({left:(oSlideContainer.position().left - (iBlockContentWidth)) + 'px'}, {duration:options.iSlideDurationInMS, queue:false, complete:function () {
                        // once the animation is completed, we remove the original content and then display the overhang
                        jQuery.easing.def = sOriginalEasingMethod; // restore original animation effect
                        oSlideContainer.children('div:first').remove();
                        $('.previousItemLink', oSlideContainer).click(customOnClickPreviousEvent).css('visibility', 'visible');
                        $('.nextItemLink', oSlideContainer).click(customOnClickNextEvent).css('visibility', 'visible');
                        var dNewWidth = oSlideContainer.parent().innerWidth() + options.iOverhangRight;

                        // now reduce the the slide window back to original size
                        oSlideContainer.css("left", "0px").css("width", (iOriginalInnerWidth) + "px").children('div:last').css("left", "0px");

                        // update height
                        dNewHeight = oSlideContainer.children('div:first').innerHeight();
                        oSlideContainer.parent().css("height", dNewHeight);
                        oSlideContainer.animate({height:dNewHeight}, {duration:options.iOverhangDisplayDurationInMS});

                        // and show the overhang again
                        if (options.iOverhangRight > 0) oSlideContainer.parent().animate({width:dNewWidth}, {duration:options.iOverhangDisplayDurationInMS});
                        obj.css('overflow', 'visible');

                        if (options.bFixateWidth) oSlideContainer.children('div').css("width", obj.innerWidth()); // reset size of content - need this to supress floating objects from having to much room
                    }
                    });
                };
                if (aItemCache[ajaxURL] === undefined) {
                    $.ajax({
                        url:ajaxURL,
                        processData:false,
                        dataType:'json',
                        success:fMoveNext,
                        type:'POST'
                    });

                } else {
                    fMoveNext(aItemCache[ajaxURL], null);
                }
                return false;
            });


            var customOnClickPreviousEvent = (function () {

                var oBackLinkObject = $(this);
                var refTimeoutId = null;
                if (options.iMaxDelayBeforePleaseWaitInMS >= 0) refTimeoutId = setTimeout(addPleaseWaitBlock, options.iMaxDelayBeforePleaseWaitInMS);
                var ajaxURL = oBackLinkObject.attr('rel');
                if (ajaxURL == '') oBackLinkObject.attr('href');

                var fMovePrevious = function (data, responseMessage) {
                    aItemCache[ajaxURL] = data;
                    if (options.iMaxDelayBeforePleaseWaitInMS >= 0) {
                        clearTimeout(refTimeoutId);
                        obj.children('.ChameleonImageSlidePleaseWait').remove();
                    }
                    obj.css('overflow', 'hidden');
                    var iOriginalInnerWidth = oSlideContainer.innerWidth();

                    // remove the overhang
                    oSlideContainer.parent().width(oSlideContainer.parent().innerWidth() - options.iOverhangRight);

                    // resize the slider and move the current item to the right - so we can add the new item to the left
                    oSlideContainer.width((iOriginalInnerWidth + iBlockContentWidth)).css("left", "-" + (iBlockContentWidth) + "px").children('div:last').css("left", (iBlockContentWidth) + "px");


                    // add the new content
                    oSlideContainer.append(data.sItemPage);
                    oSlideContainer.children('div:last').css("position", "absolute").css("top", "0px").css("left", "0px");
                    if (options.bFixateWidth) {
                        oSlideContainer.children('div:last').css("width", iOriginalInnerWidth+'px');
                    }
                    $('.previousItemLink', oSlideContainer).css('visibility', 'hidden');
                    $('.nextItemLink', oSlideContainer).css('visibility', 'hidden');


                    // set easing method - backup original so we can restore it
                    var sOriginalEasingMethod = jQuery.easing.def;
                    jQuery.easing.def = options.sEaseOutFunctionName;
                    // animate the replacement
                    $('.slideContainer', obj).animate({left:'0px'}, {duration:options.iSlideDurationInMS, queue:false, complete:function () {
                        // once the animation is completed, we remove the original content and then display the overhang
                        jQuery.easing.def = sOriginalEasingMethod; // restore original animation effect
                        oSlideContainer.children('div:first').remove();
                        $('.previousItemLink', oSlideContainer).click(customOnClickPreviousEvent).css('visibility', 'visible');
                        $('.nextItemLink', oSlideContainer).click(customOnClickNextEvent).css('visibility', 'visible');
                        var dNewWidth = oSlideContainer.parent().innerWidth() + options.iOverhangRight;
                        // now reduce the the slide window back to original size
                        oSlideContainer.css("width", (iOriginalInnerWidth) + "px");

                        // update height
                        dNewHeight = oSlideContainer.children('div:first').innerHeight();
                        oSlideContainer.parent().css("height", dNewHeight);
                        oSlideContainer.animate({height:dNewHeight}, {duration:options.iOverhangDisplayDurationInMS});

                        // and show the overhang again
                        if (options.iOverhangRight > 0) oSlideContainer.parent().animate({width:dNewWidth}, {duration:options.iOverhangDisplayDurationInMS});
                        obj.css('overflow', 'visible');
                        if (options.bFixateWidth) oSlideContainer.children('div').css("width", obj.innerWidth()); // reset size of content - need this to supress floating objects from having to much room
                    }
                    });
                };

                if (aItemCache[ajaxURL] === undefined) {
                    $.ajax({
                        url:ajaxURL,
                        processData:false,
                        dataType:'json',
                        success:fMovePrevious,
                        type:'POST'
                    });

                } else {
                    fMovePrevious(aItemCache[ajaxURL], null);
                }
                return false;
            });
            var bNextFound = ($('.nextItemLink', obj).html() != null);
            var bBackFound = ($('.previousItemLink', obj).html() != null);
            if (bNextFound) $('.nextItemLink', obj).click(customOnClickNextEvent);
            if (bBackFound) $('.previousItemLink', obj).click(customOnClickPreviousEvent);
            if (options.autoslideon && bNextFound) {
                autoslide(obj, options.autoslidetime);
            }
        });
    };

    function autoslide(obj, autoslidetime) {
        var callback = function(){
            setTimeout(function(){
                $('a.next.nextItemLink', obj).trigger('click');
                autoslide(obj, autoslidetime);
            }, 1000);
        };
        setTimeout(callback, autoslidetime);
    }
})(jQuery);
