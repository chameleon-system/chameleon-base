/*
  simple gallery slider - expects the following html:
  
<div class="TBsArticleImage"><div class="standard">
  <div class="sActiveImage"><?=$oImage->GetThumbnailTag($iMaxWidth,$iMaxHeight,_BS_IMAGE_MAX_ZOOM_X, _BS_IMAGE_MAX_ZOOM_Y,'','sGalleryImage',$oArticleImage->fieldName.'<br />'.$oArticleImage->fieldDescription)?></div>
  <div class="sActiveImageText">
    <?php if (!empty($oArticleImage->fieldName)) {?><h4 class="headline"><?=TGlobal::OutHTML($oArticleImage->fieldName)?></h4><?php } ?>
    <?=nl2br(TGlobal::OutHTML($oArticleImage->fieldDescription))?>
    <?php if (!empty($oImage->aData['credits'])) {?><div class="imageCredit"><?=TGlobal::OutHTML($oImage->aData['credits'])?></div><?php } ?>
  </div>
  <div class="sThumb"><img src="<?=$oThumb->GetFullURL()?>" width="<?=$oThumb->aData['width']?>" height="<?=$oThumb->aData['height']?>" alt="<?=TGlobal::OutHTML($oImage->aData['description'])?>" /></div>
</div></div>

*/
(function($){
  $.fn.chameleonImageGallerySimple = function(options) {
    var defaults = {
    };
    var options = $.extend(defaults, options);
    return this.each(function() {
      var oImageList = $('.imageList', $(this).parent());
      oImageList.css('display','none');
      var iNumberOfChildren = oImageList.children().length;
      // wrap elements into a slide container
      var galleryControlContainer = '';
      galleryControlContainer += '<div class="galleryControl">';
      galleryControlContainer += '<div class="sActiveImage"></div>';
      galleryControlContainer += '<div class="imageNavi">';
      galleryControlContainer += '<div class="sPrevImage"></div>';
      galleryControlContainer += '<a class="sPrevImageLink" href="#">back</a>';
      galleryControlContainer += '<a class="sNextImageLink" href="#">next</a>';
      galleryControlContainer += '<div class="sNextImage"></div>';
      galleryControlContainer += '</div>';
      galleryControlContainer += '<div class="imageListCounter"><span class="sActiveImageIndex">0</span> von '+iNumberOfChildren+'</div>';
      galleryControlContainer += '<div class="sActiveImageText"></div>';
      galleryControlContainer += '</div>';
      $(this).append(galleryControlContainer);

      // get container
      var oGalleryControl = $('.galleryControl', $(this));
      var oActiveImage = $('.sActiveImage', oGalleryControl);
      var oPrevImage = $('.sPrevImage', oGalleryControl);
      var oPrevImageLink = $('.sPrevImageLink', oGalleryControl);
      var oNextImageLink = $('.sNextImageLink', oGalleryControl);
      var oNextImage = $('.sNextImage', oGalleryControl);
      var oActiveImageIndex = $('.sActiveImageIndex', oGalleryControl);
      var oActiveImageText = $('.sActiveImageText', oGalleryControl);

      // get elements
      function SetNewChild(iChildIndex) {
        if (iChildIndex < iNumberOfChildren) {
          var oChild = oImageList.children().eq(iChildIndex);
          if (oChild) {
            oActiveImageIndex.html(iChildIndex+1);
            oActiveImage.html($('.sActiveImage', oChild).html());
            tb_init($('.thickbox', oActiveImage));

            oActiveImageText.html($('.sActiveImageText', oChild).html());

            var iPrevItemIndex = iChildIndex-1;
            if (iPrevItemIndex < 0) iPrevItemIndex = iNumberOfChildren-1;

            var iNextItemIndex = iChildIndex+1;
            if (iNextItemIndex >= iNumberOfChildren) iNextItemIndex = 0;

            oPrevImage.html($('.sThumb', oImageList.children().eq(iPrevItemIndex)).html());
            oNextImage.html($('.sThumb', oImageList.children().eq(iNextItemIndex)).html());

            oPrevImageLink.unbind('click').click(function() {
              SetNewChild(iPrevItemIndex);
              return false;
            });
            oNextImageLink.unbind('click').click(function() {
              SetNewChild(iNextItemIndex);
              return false;
            });
            oPrevImage.unbind('click').click(function() {
              SetNewChild(iPrevItemIndex);
              return false;
            });
            oNextImage.unbind('click').click(function() {
              SetNewChild(iNextItemIndex);
              return false;
            });
          } else alert('image #'+iChildIndex+' not found');

        }
      }
      SetNewChild(0);
    });
  };


})(jQuery);
