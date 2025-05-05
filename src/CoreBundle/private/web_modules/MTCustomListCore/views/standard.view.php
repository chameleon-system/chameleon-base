<div class="presseartikelliste">
    <?php if (!empty($data['oItemListConfig']->sqlData['name'])) {
        echo '<h1>'.TGlobal::OutHTML($data['oItemListConfig']->sqlData['name'])."</h1>\n";
    } ?>
    <?php if ('<div>&nbsp;</div>' != $data['oItemListConfig']->sqlData['intro'] && !empty($data['oItemListConfig']->sqlData['intro'])) {
        ?>
    <div class="introtext"><?php echo $data['oItemListConfig']->GetTextField('intro', 500); ?></div><?php
    } ?>
    <div class="itemlist">
        <?php if ($data['numberOfPages'] > 1) {
            ?>
        <div class="listpageing">
            <div class="backlink"><?php if (!empty($data['sPreviousPageLink'])) {
                ?><a
                href="<?php echo $data['sPreviousPageLink']; ?>">zurück</a><?php
            } else {
                echo '&nbsp;';
            } ?></div>
            <div class="nextlink"><?php if (!empty($data['sNextPageLink'])) {
                ?><a href="<?php echo $data['sNextPageLink']; ?>">weiter</a><?php
            } else {
                echo '&nbsp;';
            } ?>
            </div>
            <div class="pageinfo">Seite <?php echo TGlobal::OutHTML($data['iPage']); ?>
                von <?php echo TGlobal::OutHTML($data['numberOfPages']); ?></div>
        </div>
        <?php
        } ?>
        <table>
            <?php while ($oItem = $data['oItemList']->Next()) {
                /* @var $oItem MTCustomListCoreItem */ ?>
            <tr>
                <td class="published"><?php echo TGlobal::OutHTML($oItem->GetPublishedDisplayDate()); ?></td>
                <td class="info">
                    <?php echo TGlobal::OutHTML($oItem->sqlData['von']); ?><br/>
                    <?php
                            if (!is_null($oItem->sDetailLink)) {
                                ?>
                        <a href="<?php echo $oItem->sDetailLink; ?>&module_fnc[<?php echo $data['sModuleSpotName']; ?>]=ShowItem"><?php echo TGlobal::OutHTML($oItem->sqlData['name']); ?></a>
                        <?php
                            } else {
                                $oDownloads = $oItem->GetDownloads('cms_document_mlt');
                                while ($oDownload = $oDownloads->Next()) {
                                    ?>
                            <div style="padding-bottom:3px"><?php echo $oDownload->getDownloadHtmlTag(); ?></div>
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
                href="<?php echo $data['sPreviousPageLink']; ?>">zurück</a><?php
            } else {
                echo '&nbsp;';
            } ?></div>
            <div class="nextlink"><?php if (!empty($data['sNextPageLink'])) {
                ?><a href="<?php echo $data['sNextPageLink']; ?>">weiter</a><?php
            } else {
                echo '&nbsp;';
            } ?>
            </div>
            <div class="pageinfo">Seite <?php echo TGlobal::OutHTML($data['iPage']); ?>
                von <?php echo TGlobal::OutHTML($data['numberOfPages']); ?></div>
        </div>
        <?php
        } ?>
    </div>
</div>