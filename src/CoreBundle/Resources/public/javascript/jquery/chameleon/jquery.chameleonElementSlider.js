/*
  allows sliding elements within a container element
  sample:
<div class="TShopArticleList"><div class="scroll-all-loaded">
  <div class="naviContainer">
    <a href="#" class="nextItemLink" rel="nofollow">next</a>
    <a href="#" class="previousItemLink" rel="nofollow">back</a>
  </div>
  <div class="chameleonInlineSlider"><div class="sliderElement">
    <?php
      $iCount = 0;
      $iMaxPerRow = 3;
        while($oArticle = $oArticleList->Next()) {
          if ($iCount >= $iMaxPerRow) {
            echo '</div><div class="sliderElement">';
            $iCount = 0;
          }
          echo $oArticle->Render('simple','Customer', $aCallTimeVars);
          $iCount++;
        }
    ?>
    </div>
  </div>
</div></div>
*/
(function($){
  $.fn.chameleonElementSlider = function(options) {
    var defaults = {
      iSlideDurationInMS:1000,
      sEaseOutFunctionName: 'easeOutExpo'
    };
    var options = $.extend(defaults, options);
    return this.each(function() {
      var oNextLink = $('.nextItemLink', $(this).parent());
      var oPreviousLink = $('.previousItemLink', $(this).parent());

      var iBlockContentWidth = $(this).innerWidth();

      // absolute position the content elements
      var currentOffset = 0;
      var elementHeight = 0;
      $(this).children().each(function() {
        oChildElement = $(this);
        if (elementHeight == 0) elementHeight = oChildElement.innerHeight();
        oChildElement.css('position','absolute').css('top','0px').css('left',currentOffset+'px');
        currentOffset += iBlockContentWidth;
      });
      var totalScrollerLength = currentOffset;

      // wrap elements into a slide container
      $(this).wrapInner('<div style="position:relative;overflow:hidden;width:'+iBlockContentWidth+'px;height:'+elementHeight+'px;"><div class="slideContainer" style="position:absolute;width:'+totalScrollerLength+'px;height:'+elementHeight+'px;"></div></div>');
      var oSlideContainer = $('.slideContainer', $(this));


      var customOnClickNextEvent = (function() {
        oNextLink.unbind('click');
        oPreviousLink.unbind('click');
        var sOriginalEasingMethod = jQuery.easing.def;
        jQuery.easing.def = options.sEaseOutFunctionName;
        dNewWidth = oSlideContainer.position().left - iBlockContentWidth;
        oSlideContainer.animate({left:dNewWidth},{duration:options.iSlideDurationInMS,complete:
          function() {
            jQuery.easing.def = sOriginalEasingMethod; // restore original animation effect
            if ((oSlideContainer.position().left - iBlockContentWidth) == (-1*totalScrollerLength)) {
              oNextLink.addClass('disabled');
            } else oNextLink.click(customOnClickNextEvent);

            if (oSlideContainer.position().left < 0) {
              oPreviousLink.removeClass('disabled');
              oPreviousLink.click(customOnClickPreviousEvent);
            }



          }
        });
        return false;
      });

      var customOnClickPreviousEvent = (function() {
        oNextLink.unbind('click');
        oPreviousLink.unbind('click');
        var sOriginalEasingMethod = jQuery.easing.def;
        jQuery.easing.def = options.sEaseOutFunctionName;
        dNewWidth = oSlideContainer.position().left + iBlockContentWidth;
        oSlideContainer.animate({left:dNewWidth},{duration:options.iSlideDurationInMS,complete:
          function() {
            jQuery.easing.def = sOriginalEasingMethod; // restore original animation effect
            if (oSlideContainer.position().left >= 0) {
              oPreviousLink.addClass('disabled');
            } else oPreviousLink.click(customOnClickPreviousEvent);
            if ((totalScrollerLength+oSlideContainer.position().left) >=  iBlockContentWidth) {
              oNextLink.removeClass('disabled');
              oNextLink.click(customOnClickNextEvent);
            }
          }
        });
        return false;
      });
      if (oSlideContainer.position().left >= 0) {
        oPreviousLink.addClass('disabled');
      } else oPreviousLink.click(customOnClickPreviousEvent);
      if ((oSlideContainer.position().left - iBlockContentWidth) == (-1*totalScrollerLength)) {
        oNextLink.addClass('disabled');
      } else oNextLink.click(customOnClickNextEvent);

    });
  };


})(jQuery);
