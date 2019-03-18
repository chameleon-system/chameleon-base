<div class="contentRoundedBox">
<div class="rightCorner">
<div class="topBorder">
</div>
<div class="leftBorder">
<div style="padding-left: 10px; padding-right: 10px;">
<div class="contentbox">
<div class="contentcontainer">
<div class="header"><h1><?=TGlobal::OutHTML(TGlobal::Translate('Lizenz Manager')); ?></h1></div>
<div class="content">

    <?=TGlobal::OutHTML(TGlobal::Translate('Für Ihre Chameleon Installation wird noch ein Lizenz-Schlüssel benötigt.')); ?><br />

    <form name="licenseform" method="post" action="<?=PATH_CMS_CONTROLLER; ?>">
        <input type="hidden" name="pagedef" value="<?=TGlobal::instance()->GetUserData('pagedef'); ?>" />
        <input type="hidden" name="module_fnc[[{sModuleSpotName}]]" value="enterLicense" />
        <table>
            <tr>
                <th style="vertical-align: top"><?=TGlobal::OutHTML(TGlobal::Translate('Lizenz-Schlüssel')); ?></th>
                <td style="vertical-align: top">
                    <input type="text" name="key" value="" />
                    <?=TCMSMessageManager::GetInstance()->RenderMessages('LICENSE-KEY', 'standard', 'Core'); ?>
                </td>
            </tr>
            <tr>
                <th style="vertical-align: top"><?=TGlobal::OutHTML(TGlobal::Translate('Lizenz-Nehmer')); ?></th>
                <td style="vertical-align: top">
                    <input type="text" name="owner" value="" />
                    <?=TCMSMessageManager::GetInstance()->RenderMessages('LICENSE-OWNER', 'standard', 'Core'); ?>
                </td>
            </tr>
            <tr>
                <th style="vertical-align: top"><?=TGlobal::OutHTML(TGlobal::Translate('Domain-Liste')); ?></th>
                <td style="vertical-align: top">
                    <textarea name="domain_list" cols="80"></textarea><br />
                    <?=TGlobal::OutHTML(TGlobal::Translate('(jede Domain auf einer eigenen Zeile eingeben)')); ?>
                    <?=TCMSMessageManager::GetInstance()->RenderMessages('LICENSE-DOMAIN-LIST', 'standard', 'Core'); ?>
                </td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td>
                    <input type="submit" name="submit" value="<?=TGlobal::OutHTML(TGlobal::Translate('Lizenz eintragen')); ?>" />

                </td>
            </tr>
        </table>
     <br />

     <br />
        <?=TGlobal::Translate('Sie können Chameleon zum Testen auch ohne Lizenz verwenden. [{sBackEndLinkStart}] > Klicken Sie hier, wenn Sie Chameleon weiter im Test-Modus verwenden möchten.[{sBackEndLinkEnd}]', array('%sBackEndLinkStart%' => '<a href="'.PATH_CMS_CONTROLLER.'">', '%sBackEndLinkEnd%' => '</a>')); ?>
    </form>


</div>
</div>
</div>
<br>
</div>
</div>
<div class="bottomBorder">
</div>
<div class="cleardiv">&nbsp;</div>
</div>
</div>