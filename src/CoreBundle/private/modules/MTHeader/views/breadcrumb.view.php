<?php

if (isset($data['oUser'])) {
    $sMainMenuText = TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_main_menu')); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb my-0">
            <li class="breadcrumb-item">
              <i class="fas fa-home pe-3"></i>
              <a href="<?= PATH_CMS_CONTROLLER; ?>?_rmhist=true&amp;_histid=0"><?= $sMainMenuText; ?></a>
            </li>
            <?php
    $maxCount = 8;
    $count = 0;
    $totalCount = count($data['breadcrumb']);
    foreach ($data['breadcrumb'] as $histid => $item) {
        ++$count;
        // Skip older entries if we have more than $maxCount nodes
        if ($totalCount <= $maxCount || ($totalCount > $maxCount && $count > ($totalCount - $maxCount))) {
            $sBreadcrumbNodeName = $item['name'];
            if ($sBreadcrumbNodeName != $sMainMenuText) {
                $atagID = '';
                if ($count == $totalCount) {
                    $atagID = ' id="breadcrumbLastNode"';
                }

                if (empty($sBreadcrumbNodeName)) {
                    $sBreadcrumbNodeName = TGlobal::Translate('chameleon_system_core.text.unnamed_record');
                } ?>
                        <li class="breadcrumb-item">
                            <a href="<?= TGlobal::OutHTML($item['url']); ?>"<?= $atagID; ?>>
                                <?= $sBreadcrumbNodeName; ?>
                            </a>
                        </li>
                        <?php
            }
        }
    } ?>
        </ol>
    </nav>
    <?php
}
?>
