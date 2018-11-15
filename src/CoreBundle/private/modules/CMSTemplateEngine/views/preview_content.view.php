<?php require dirname(__FILE__).'/parts/navi.inc.php'; ?>
<div class="card" id="templateengine">
    <div class="card-header">
    <?php
    require dirname(__FILE__).'/parts/header.inc.php';
    ?>
    </div>
    <div class="card-body p-0">
        <iframe name="userwebpage" id="userwebpageiframe" frameborder="0" src="<?=$sPreviewURL; ?>" style="height: 100%; overflow: hidden;" width="100%" height="100%"></iframe>
    </div>
</div>