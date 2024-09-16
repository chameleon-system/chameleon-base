<?php

/**
 * @deprecated since 7.1.34
 * use MapCoordinates.php instead
 */
/**
 * @var string $googleMapId
 * @var string $fieldName
 */
$translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');

if (array_key_exists('sMessage', $data) && !empty($data['sMessage'])) {
    echo '<div class="alert alert-info">'.$data['sMessage'].'</div>';
}

?>
<form class="form-inline" id="addressPickerForm" style="margin-bottom: 10px;">
    <div class="form-group">
        <div class="input-group">
            <input type="text" class="form-control" name="place" id="place" placeholder="<?=TGlobal::OutHTML($translator->trans('chameleon_system_core.google_map.address')); ?>">
            <div class="input-group-prepend">
                <button class="btn btn-secondary" type="button" id="btnFindAddress"><i class="fas fa-search" title="<?=TGlobal::OutHTML($translator->trans('chameleon_system_core.google_map.find_coordinates')); ?>"></i></button>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary" id="btnSaveCoordinates"><?=TGlobal::OutHTML($translator->trans('chameleon_system_core.google_map.action_use_coordinates')); ?></button>
    <div class="form-group ml-4">
        <div id="coordinates">
        </div>
    </div>
</form>

<?php
if (array_key_exists('googleMapHtml', $data) && !empty($data['googleMapHtml'])) {
    echo $data['googleMapHtml'];
}
?>

<?php if (\ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.geocoding.attribution.show')): ?>
    <small>
        <i>
            <?= $translator->trans('chameleon_system_core.google_map.geocoding_via', [
                '%name%' => \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.geocoding.attribution.name'),
                '%url%' => \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.geocoding.attribution.url'),
            ]) ?>
        </i>
    </small>
<?php endif; ?>

<script type="text/javascript">
    $(document).ready(function() {
        CHAMELEON.CORE.TCMSFieldGMapCoordinate.initAddressPicker(
            '<?=TGlobal::OutJS($googleMapId); ?>',
            '<?=TGlobal::OutJS($fieldName); ?>',
            '<?=TGlobal::OutJS($translator->trans('chameleon_system_core.google_map.coordinates')); ?>');
    });
</script>
