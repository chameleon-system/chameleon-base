<?php
/** @var $oField TCMSField* */
/** @var $bFieldHasErrors boolean* */
/** @var $aRecordsConnected array* */
$iPosition = 0;
foreach ($aRecordsConnected as $aValue) {
    /** @var $oImage TCMSImage* */
    $sValue = $aValue['cms_media_id'];
    echo '<div class="image-box">';
    if (!is_numeric($aValue['cms_media_id']) || (is_numeric($aValue['cms_media_id']) && $aValue['cms_media_id'] > 1000)) {
        $oImage = new TCMSImage();
        $oImage->Load($aValue['cms_media_id']);
        $oThumb = $oImage->GetThumbnail(100, 100); ?>
    <div class="image-connected">
        <img src="<?=$oThumb->GetFullURL(); ?>" alt="<?=TGlobal::OutHTML($oImage->aData['description']); ?>" border="0"/>
        <span class="close"
              onclick="$(this).parent().remove();$('#<?=TGlobal::OutHTML($oField->name).$iPosition; ?>cms_media_id').attr('value','1')"><?=TGlobal::OutHTML(\ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_media.remove')); ?></span>
    </div>
    <?php
    } ?>
<div class="image-input">
    <input type="file" name="<?=TGlobal::OutHTML($oField->name); ?>image[<?=$iPosition; ?>]"/>
    <input type="hidden" id="<?=TGlobal::OutHTML($oField->name).$iPosition; ?>cms_media_id"
           name="<?=TGlobal::OutHTML($oField->name); ?>[<?=$iPosition; ?>][cms_media_id]"
           value="<?=TGlobal::OutHTML($sValue); ?>"/>
</div>
</div>
<?php
    ++$iPosition;
}
?>