<div class="MTGlobalListCore">
    <div class="listitem">
        <div class="articledate"><?=TGlobal::OutHTML($data['oItem']->GetDateField('datum')); ?></div>
        <h1><?=TGlobal::OutHTML($data['oItem']->GetName()); ?></h1>

        <div class="listbody">
            <?=$data['oItem']->GetTextField('intro', 547); ?><br/>
            <?=$data['oItem']->GetTextField('content', 547); ?>
        </div>
        <br/>
        <a class="listlink" href="<?=$data['oItem']->sListLink; ?>">zur&uuml;ck zur Liste</a>

        <div class="cleardiv">&nbsp;</div>
    </div>
</div>
