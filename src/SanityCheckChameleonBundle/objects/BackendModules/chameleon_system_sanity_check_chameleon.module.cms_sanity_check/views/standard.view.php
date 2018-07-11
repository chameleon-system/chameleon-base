<?php

use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @var TranslatorInterface $translator
 */
$translator = ServiceLocator::get('translator');
?>
<div class="CMSInterfacePopup">

    <h2><?= TGlobal::OutHTML($translator->trans('label.perform_single_check', [], 'chameleon_system_sanitycheck')); ?></h2>

    <form method="post" action="<?= PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
        <input type="hidden" name="pagedef" value="<?= TGlobal::OutHTML($data['pagedef']); ?>"/>
        <input type="hidden" name="module_fnc[<?= TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value=""/>
        <input type="hidden" name="_pagedefType" value="<?= $data['_pagedefType']; ?>"/>
        <select name="singleCheck[]" multiple="multiple" size="8">

            <?php
            foreach ($data['checks'] as $check) {
                echo '<option value="'.$check[0].'">'.(null !== $check[1] ? $data['translator']->trans(
                        $check[1],
                        array(),
                        'chameleon_system_sanitycheck'
                    ) : $check[0]).'</option>';
            }
            ?>
            <input
                type="submit"
                value="<?= TGlobal::OutHTML($translator->trans('label.perform_check', [], 'chameleon_system_sanitycheck')); ?>"
                style="margin-left: 1em;"
                />
        </select>
    </form>

    <h2><?= TGlobal::OutHTML($translator->trans('label.perform_checks_for_bundle', [], 'chameleon_system_sanitycheck')); ?></h2>

    <form method="post" action="<?= PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
        <input type="hidden" name="pagedef" value="<?= TGlobal::OutHTML($data['pagedef']); ?>"/>
        <input type="hidden" name="module_fnc[<?= TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value=""/>
        <input type="hidden" name="_pagedefType" value="<?= $data['_pagedefType']; ?>"/>
        <select name="bundleCheck[]" multiple="multiple" size="8">

            <?php
            foreach ($data['bundlesWithChecks'] as $bundle) {
                echo '<option>'.$bundle.'</option>';
            }
            ?>
            <input
                type="submit"
                value="<?= TGlobal::OutHTML($translator->trans('label.perform_checks', [], 'chameleon_system_sanitycheck')); ?>"
                style="margin-left: 1em;"
                />
        </select>
    </form>
</div>
