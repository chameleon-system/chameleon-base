<?php
if (!is_null($oListSettings)) {
    $sHeadLine = $oListSettings->fieldName;
    $sSubHeadline = $oListSettings->fieldSubHeadline;
}
?>
<div class="moduleMTList">
    <div class="MTListHeader">
        <?php
        if (!empty($sHeadLine)) {
            echo  '<h2 class="headline">'.TGlobal::OutHTML($sHeadLine).'</h2>';
        }
        if (!empty($sSubHeadline)) {
            echo  '<h3 class="subheadline">'.TGlobal::OutHTML($sSubHeadline).'</h3>';
        }
        ?>
    </div>
    <div class="text"><?=TGlobal::OutHTML($oListSettings->fieldDescription); ?></div>

    <?php while ($oArticle = $data['oList']->Next()) {
            /** @var $oArticle TCMSRecord */ ?>
    <div class="entry">
        <h1 class="entryheader"><?=TGlobal::OutHTML($oArticle->sqlData['name']); ?></h1>

        <div class="teaser">
            <?=$oArticle->GetTextField('teaser_text', 400); ?>
            [<a class="modulelink" href="<?=$oArticle->GetDetailURL(); ?>"
                title="<?=TGlobal::OutHTML($oArticle->sqlData['name']); ?> anzeigen">mehr</a>]
        </div>
    </div>
    <?php
        } ?>
</div>