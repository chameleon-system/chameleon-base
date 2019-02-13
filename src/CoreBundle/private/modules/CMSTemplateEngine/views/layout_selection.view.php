<?php require __DIR__.'/parts/navi.inc.php'; ?>
<div class="card card-accent-primary mb-2" id="templateengine">
    <div class="card-header p-1">
        <?php
        require_once dirname(__FILE__).'/../../MTTableEditor/views/includes/editorheader.inc.php';
        ?>
    </div>
    <div class="card-body p-0">
        <div class="row">
            <div class="col-lg-2 col-md-3 col-xs-4 pr-0"><iframe name="layoutliste" id="layoutliste" frameborder="0" width="100%" style="height: 100%; overflow: hidden;" height="100%"
                                          src="<?=PATH_CMS_CONTROLLER; ?>?pagedef=templateengineplain&_mode=layoutlist&id=<?=$data['id']; ?>"></iframe></div>
            <div class="col-lg-10 col-md-9 col-xs-8 pl-1 pr-3">
                <?php $src = isset($data['sActualMasterLayout']) ? " src=\"{$data['sActualMasterLayout']}\"" : ''; ?>
                <iframe name="layoutpreview" id="userwebpageiframe" frameborder="0" style="height: 100%; overflow: hidden;" width="100%"
                        height="100%"<?= $src; ?>>
                </iframe>
            </div>
        </div>
    </div>
</div>