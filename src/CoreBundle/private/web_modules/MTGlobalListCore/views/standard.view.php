<div class="MTGlobalListCore">
    <?php while ($oItem = $data['oItemList']->Next()) {
        /* @var $oItem MTGlobalListItem */ ?>
    <div class="listitem">
        <div class="articledate"><?php echo TGlobal::OutHTML($oItem->GetDateField('datum')); ?></div>
        <h1><?php echo TGlobal::OutHTML($oItem->GetName()); ?></h1>

        <div class="listbody"><?php echo $oItem->GetTextField('intro', 547); ?></div>
        <br/>
        <a class="detaillink" href="<?php echo $oItem->sDetailLink; ?>">Show Details</a>

        <div class="cleardiv">&nbsp;</div>
    </div>
    <?php
    } ?>
</div>