<div class="presseartikelliste">
    <?php if (!empty($data['oItemListConfig']->sqlData['name'])) {
    echo '<h1>'.TGlobal::OutHTML($data['oItemListConfig']->sqlData['name'])."</h1>\n";
} ?>
    <?php if ('<div>&nbsp;</div>' != $data['oItemListConfig']->sqlData['intro'] && !empty($data['oItemListConfig']->sqlData['intro'])) {
    ?>
    <div class="introtext"><?=$data['oItemListConfig']->GetTextField('intro', 500); ?></div><?php
} ?>
    <div class="itemlist">
        <?php if ($data['numberOfPages'] > 1) {
        ?>
        <div class="listpageing">
            <div class="backlink"><?php if (!empty($data['sPreviousPageLink'])) {
            ?><a
                href="<?=$data['sPreviousPageLink']; ?>">zurück</a><?php
        } else {
            echo '&nbsp;';
        } ?></div>
            <div class="nextlink"><?php if (!empty($data['sNextPageLink'])) {
            ?><a href="<?=$data['sNextPageLink']; ?>">weiter</a><?php
        } else {
            echo '&nbsp;';
        } ?>
            </div>
            <div class="pageinfo">Seite <?=TGlobal::OutHTML($data['iPage']); ?>
                von <?=TGlobal::OutHTML($data['numberOfPages']); ?></div>
        </div>
        <?php
    } ?>
        <table>
            <?php while ($oItem = $data['oItemList']->Next()) {
        /** @var $oItem MTCustomListCoreItem */ ?>
            <tr>
                <td class="published"><?=TGlobal::OutHTML($oItem->GetPublishedDisplayDate()); ?></td>
                <td class="info">
                    <?=TGlobal::OutHTML($oItem->sqlData['von']); ?><br/>
                    <?php
                    if (!is_null($oItem->sDetailLink)) {
                        ?>
                        <a href="<?=$oItem->sDetailLink; ?>&module_fnc[<?=$data['sModuleSpotName']; ?>]=ShowItem"><?=TGlobal::OutHTML($oItem->sqlData['name']); ?></a>
                        <?php
                    } else {
                        $oDownloads = &$oItem->GetDownloads('cms_document_mlt');
                        while ($oDownload = $oDownloads->Next()) {
                            ?>
                            <div style="padding-bottom:3px"><?=$oDownload->getDownloadHtmlTag(); ?></div>
                            <?php
                        }
                    } ?>

                </td>
            </tr>
            <?php
    } ?>
            <tr>
            </tr>
        </table>
        <?php if ($data['numberOfPages'] > 1) {
        ?>
        <div class="listpageing">
            <div class="backlink"><?php if (!empty($data['sPreviousPageLink'])) {
            ?><a
                href="<?=$data['sPreviousPageLink']; ?>">zurück</a><?php
        } else {
            echo '&nbsp;';
        } ?></div>
            <div class="nextlink"><?php if (!empty($data['sNextPageLink'])) {
            ?><a href="<?=$data['sNextPageLink']; ?>">weiter</a><?php
        } else {
            echo '&nbsp;';
        } ?>
            </div>
            <div class="pageinfo">Seite <?=TGlobal::OutHTML($data['iPage']); ?>
                von <?=TGlobal::OutHTML($data['numberOfPages']); ?></div>
        </div>
        <?php
    } ?>
    </div>
</div>