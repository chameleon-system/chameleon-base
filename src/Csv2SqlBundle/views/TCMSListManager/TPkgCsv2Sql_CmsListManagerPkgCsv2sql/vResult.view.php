<?php
/** @var $aImportErrors array */
?>
<div class="TPkgCsv2Sql_CmsTableEditorPkgCsv2sql">
    <div class="vResult">
        <div class="import_title"><?php echo TGlobal::OutHTML($oImportName); ?></div>

        <?php
        if (!empty($successMessage)) {
            echo '<h2>'.$successMessage.'</h2>';
        }

        if (is_array($aValidationErrors) && count($aValidationErrors) > 0) {
            ?>
        <div style="margin:20px 0 10px 0px;font-weight: bold;"><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_csv2sql.error.validation_errors')); ?>:</div>
        <div class="ValidationErrors">
            <?php

                foreach ($aValidationErrors as $iK => $mVal) {
                    echo '<div style="border-bottom: 1px solid #EFEFEF;">';
                    if (is_array($mVal)) {
                        $mVal = '<pre>'.print_r($mVal, true).'</pre>';
                    }
                    echo '<div style="float:left;width:20px;text-align:left;color:#B8B8B8">'.$iK.'</div><div style="float:left;">"'.$mVal.'</div>';
                    echo '<div class="cleardiv" /></div><div class="cleardiv" />';
                } ?>
        </div>
        <?php
        }

        if (is_array($aImportErrors) && count($aImportErrors) > 0) {
            ?>
        <div style="margin:20px 0 10px 0px;font-weight: bold;"><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_csv2sql.error.import_errors')); ?>:</div>
        <div class="ImportErrors">
            <?php

                foreach ($aImportErrors as $iK => $mVal) {
                    echo '<div style="border-bottom: 1px solid #EFEFEF;">';
                    if (is_array($mVal)) {
                        $mVal = '<pre>'.print_r($mVal, true).'</pre>';
                    }
                    echo '<div style="float:left;width:20px;text-align:left;color:#B8B8B8">'.$iK.'</div><div style="float:left;">"'.$mVal.'</div>';
                    echo '<div class="cleardiv" /></div><div class="cleardiv" />';
                } ?>
        </div>
    </div>
    <?php
        }
    ?>
</div>
