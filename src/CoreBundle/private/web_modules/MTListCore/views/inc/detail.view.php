<div class="moduleMTList">
    <div class="details">
        <h1 class="entryheader"><?=TGlobal::OutHTML($data['oArticle']->sqlData['name']); ?></h1>

        <h2 class="entryheader"><?=TGlobal::OutHTML($data['oArticle']->sqlData['sub_headline']); ?></h2>

        <div class="teaser">
            <?=$data['oArticle']->GetTextField('teaser_text', 400); ?>
        </div>
        <div class="content">
            <?=$data['oArticle']->GetTextField('description', 400); ?>
        </div>
        <a class="modulelink" href="<?=TGlobal::OutHTML($data['oActivePage']->GetURL()); ?>"
           title="zur&uuml;ck zur Liste">zur&uuml;ck zur Liste</a>
    </div>
</div>