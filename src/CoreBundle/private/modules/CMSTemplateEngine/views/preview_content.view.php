<?php require dirname(__FILE__).'/parts/navi.inc.php'; ?>
<div class="card card-accent-primary mb-2" id="templateengine">
    <div class="card-header p-1">
    <?php
    require_once dirname(__FILE__).'/../../MTTableEditor/views/includes/editorheader.inc.php';
    ?>
    </div>
    <div class="card-body p-0">
        <iframe name="userwebpage" id="userwebpageiframe" frameborder="0" src="<?=$sPreviewURL; ?>" style="height: 100%; overflow: hidden;" width="100%" height="100%"></iframe>
    </div>
</div>