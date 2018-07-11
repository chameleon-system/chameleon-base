<div style="padding: 20px;">
    <form id="updateForm">
        <input type="hidden" name="pagedef" value="CMSUpdateManager"/>
        <input type="hidden" name="module_fnc[contentmodule]" id="module_fnc" value=""/>
        <?php

        echo '<h1 style="margin-top: 0px;">'.TGlobal::Translate('chameleon_system_core.cms_module_update.headline')."</h1>\n";
        echo "<div class=\"notice\" style=\"width: 400px;\">\n";
        echo TGlobal::Translate('chameleon_system_core.cms_module_update.intro_text');
        echo "</div>
  <div class=\"cleardiv\"></div>\n";

        echo "<div style=\"margin-top: 30px;\">\n";
        echo TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.cms_module_update.show_all'), "javascript:document.getElementById('module_fnc').value='RunUpdates';document.getElementById('updateForm').submit();", URL_CMS.'/images/icons/action_go.gif');
        if (_DEVELOPMENT_MODE) {
            echo '<br /><br />';
            echo TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.cms_module_update.select_single_update'), "javascript:document.getElementById('module_fnc').value='RunUpdateSingle';document.getElementById('updateForm').submit();", URL_CMS.'/images/icons/action_go.gif');
        }
        echo "</div>
  <div class=\"cleardiv\"></div>
  <div style=\"padding-top: 200px;\">&nbsp;</div>\n";
        ?>
    </form>
</div>