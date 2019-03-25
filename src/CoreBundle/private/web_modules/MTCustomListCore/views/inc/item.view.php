<div class="presseartikelliste">
    <?php if (!empty($data['oItemListConfig']->sqlData['name'])) {
    echo '<h1>'.TGlobal::OutHTML($data['oItemListConfig']->sqlData['name'])."</h1>\n";
} ?>
    <div class="item">
        <div class="backlink"><a href="<?=$data['oItem']->sListLink; ?>">zurück</a></div>
        <h3><?=TGlobal::OutHTML($data['oItem']->sqlData['von']); ?></h3>

        <h2><?=TGlobal::OutHTML($data['oItem']->sqlData['name']); ?></h2>

        <div class="itemText"><?=$data['oItem']->GetTextField('artikel'); ?></div>
        <?php
        /**
         * @var TCMSRecord $item
         */
        $item = $data['oItem'];
        $oDownloads = $item->GetDownloads('cms_document_mlt');
        while ($oDownload = $oDownloads->Next()) {
            ?>
            <div style="padding-bottom:3px"><?=$oDownload->getDownloadHtmlTag(); ?></div>
            <?php
        } ?>
    </div>
</div>