<div class="MTGlobalListCore">
    <div class="listitem">
        <div class="articledate"><?php echo TGlobal::OutHTML($data['oItem']->GetDateField('datum')); ?></div>
        <h1><?php echo TGlobal::OutHTML($data['oItem']->GetName()); ?></h1>

        <div class="listbody">
            <?php echo $data['oItem']->GetTextField('intro', 547); ?><br/>
            <?php echo $data['oItem']->GetTextField('content', 547); ?>
        </div>
        <br/>
        <a class="listlink" href="<?php echo $data['oItem']->sListLink; ?>">zur&uuml;ck zur Liste</a>

        <div class="cleardiv">&nbsp;</div>
    </div>
</div>
