<?php require __DIR__.'/parts/navi.inc.php'; ?>
<div class="card card-accent-primary mb-2" id="templateengine">
    <div class="card-header p-1">
        <?php
        require_once dirname(__FILE__).'/../../MTTableEditor/views/includes/editorheader.inc.php';
?>
    </div>
    <div class="card-body p-0">
        <div class="row">
            <div class="col-xl-3 col-md-5 pr-0 col-template-selection"><iframe name="layoutliste" id="layoutliste" frameborder="0" width="100%" style="height: 100%; overflow: hidden;" height="100%"
                                          src="<?php echo PATH_CMS_CONTROLLER; ?>?pagedef=templateengineplain&_mode=layoutlist&id=<?php echo $data['id']; ?>"></iframe></div>
            <div class="col-xl-9 col-md-7 pl-1 pr-3 col-template-content">
                <?php $src = isset($data['sActualMasterLayout']) ? " src=\"{$data['sActualMasterLayout']}\"" : ''; ?>
                <iframe name="layoutpreview" id="userwebpageiframe" frameborder="0" style="height: 100%; overflow: hidden;" width="100%"
                        height="100%"<?php echo $src; ?>>
                </iframe>
            </div>
        </div>
    </div>
</div>