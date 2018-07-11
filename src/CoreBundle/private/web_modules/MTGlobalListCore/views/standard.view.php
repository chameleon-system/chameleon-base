<div class="MTGlobalListCore">
    <?php while ($oItem = $data['oItemList']->Next()) {
    /** @var $oItem MTGlobalListItem */ ?>
    <div class="listitem">
        <div class="articledate"><?=TGlobal::OutHTML($oItem->GetDateField('datum')); ?></div>
        <h1><?=TGlobal::OutHTML($oItem->GetName()); ?></h1>

        <div class="listbody"><?=$oItem->GetTextField('intro', 547); ?></div>
        <br/>
        <a class="detaillink" href="<?=$oItem->sDetailLink; ?>">Show Details</a>

        <div class="cleardiv">&nbsp;</div>
    </div>
    <?php
} ?>
</div>