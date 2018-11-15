<?php
if (isset($data['oUser'])) {
    $sMainMenuText = TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_main_menu')); ?>
    <div id="cmsbreadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;<a
                        href="<?= PATH_CMS_CONTROLLER; ?>?pagedef=main&amp;_rmhist=true&amp;_histid=0"><?= $sMainMenuText; ?></a>
            </li>
            <?php
    $maxCount = 8;
    $count = 0;
    $totalCount = count($data['breadcrumb']);
    foreach ($data['breadcrumb'] as $histid => $item) {
        ++$count;
        // skip older entries if we have more than 10 nodes
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
    </div>
    <?php
}
?>