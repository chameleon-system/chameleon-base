<?php

use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @var TranslatorInterface $translator
 */
$translator = ServiceLocator::get('translator');
?>
<div class="card">
    <div class="card-header">
        <h3 class="mb-0"><i class="fas fa-bug mr-2"></i>SanityCheck<h3>
    </div>
    <div class="card-body">
        <form method="post" action="<?= PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
            <input type="hidden" name="pagedef" value="<?= TGlobal::OutHTML($data['pagedef']); ?>"/>
            <input type="hidden" name="module_fnc[<?= TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value=""/>
            <input type="hidden" name="_pagedefType" value="<?= $data['_pagedefType']; ?>"/>

            <div class="form-group">
                <label class="font-weight-bold">
                    <?= TGlobal::OutHTML($translator->trans('label.perform_single_check', [], 'chameleon_system_sanitycheck')); ?>
                </label>
                <select class="form-control" name="singleCheck[]" multiple="multiple" size="8">
                    <?php
                    foreach ($data['checks'] as $check) {
                        echo '<option value="'.$check[0].'">'.(null !== $check[1] ? $data['translator']->trans(
                                $check[1],
                                array(),
                                'chameleon_system_sanitycheck'
                            ) : $check[0]).'</option>';
                    }
                    ?>
                </select>
                <input class="btn btn-primary mt-2" type="submit" value="<?= TGlobal::OutHTML($translator->trans('label.perform_check', [], 'chameleon_system_sanitycheck')); ?>" />
            </div>
        </form>

        <form method="post" action="<?= PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
            <input type="hidden" name="pagedef" value="<?= TGlobal::OutHTML($data['pagedef']); ?>"/>
            <input type="hidden" name="module_fnc[<?= TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value=""/>
            <input type="hidden" name="_pagedefType" value="<?= $data['_pagedefType']; ?>"/>

            <div class="form-group">
                <label class="font-weight-bold">
                    <?= TGlobal::OutHTML($translator->trans('label.perform_checks_for_bundle', [], 'chameleon_system_sanitycheck')); ?>
                </label>
                <select class="form-control" name="bundleCheck[]" multiple="multiple" size="8">
                    <?php
                    foreach ($data['bundlesWithChecks'] as $bundle) {
                        echo '<option>'.$bundle.'</option>';
                    }
                    ?>
                </select>
                <input class="btn btn-primary mt-2" type="submit" value="<?= TGlobal::OutHTML($translator->trans('label.perform_checks', [], 'chameleon_system_sanitycheck')); ?>" />
            </div>
        </form>
    </div>
</div>
