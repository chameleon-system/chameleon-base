<form name="cmseditform" id="cmseditform" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" style="margin 0; padding 0;"
      accept-charset="UTF-8" onsubmit="CHAMELEON.CORE.showProcessingModal();">
    <input type="hidden" name="pagedef" value="<?=$data['pagedef']; ?>"/>
    <input type="hidden" name="tableid" value="<?=TGlobal::OutHTML($data['tableid']); ?>"/>
    <input type="hidden" name="id" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="referer_id" value="<?=TGlobal::OutHTML($data['referer_id']); ?>"/>
    <input type="hidden" name="referer_table" value="<?=TGlobal::OutHTML($data['referer_table']); ?>"/>
    <input type="hidden" name="_fnc" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <input type="hidden" name="_noModuleFunction" value="false"/>
    <?php foreach ($data['aHiddenFields'] as $key => $value) {
    ?>
    <input type="hidden" name="<?=TGlobal::OutHTML($key); ?>" value="<?=TGlobal::OutHTML($value); ?>"/>
    <?php
} ?>

    <div id="tabs-wrapper">
        <?php
        $sFormTabsContent = '';

        /** @var TdbCmsTblFieldTabList $oTabs */
        $oTabs = $data['oTabs'];
        $oTabs->GoToStart();
        $iTabCount = 0;
        $sTabId = '';

        if ($oTabs->Length() > 0) { // fields WITH headers
            $sFormTabsTitles = ''; // '<ul>';
            /** @var TdbCmsTblFieldTab $oTab */
            for ($iCount = 0; $iCount <= $oTabs->Length(); ++$iCount) {
                if ($iCount > 0) {
                    $oTab = $oTabs->Next();
                    $sTabName = $oTab->fieldName;
                    $sTabId = $oTab->id;
                } else {
                    $sTabName = TGlobal::Translate('chameleon_system_core.cms_module_table_editor.tab_default');
                    $sTabId = '';
                }
                ++$iTabCount;

                require dirname(__FILE__).'/fields.inc.php';
            }
            if (!empty($sFormTabsTitles)) {
                echo '<ul class="nav nav-tabs" role="tablist">'.$sFormTabsTitles."\n</ul>";
            }
        } else {
            $iTabCount = 0;
            require dirname(__FILE__).'/fields.inc.php';
        }
        echo '<div class="tab-content">'.$sFormTabsContent.'</div>';
        ?>
    </div>
</form>