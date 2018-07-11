<?php

use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

$translator = ServiceLocator::get('translator');

$sLogoLink = PATH_CMS_CONTROLLER;
if (isset($oUser)) {
    $sLogoLink .= '?pagedef=main';
}
?>
<div id="topNavSpacer">&nbsp;</div>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#cmsTopNavbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-brand"><a href="<?=$sLogoLink; ?>"><img src="<?=TGlobal::OutHTML($sLogoURL); ?>" /></a></div>
        </div>

        <?php
        if (isset($data['oUser'])) {
            ?>
        <div class="collapse navbar-collapse" id="cmsTopNavbar">
            <?php
            if (isset($data['check_messages'])) {
                ?>
                <div class="btn-group pull-left">
                    <button type="button" class="btn btn-sm dropdown-toggle navbar-btn btn-default" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-warning-sign"></span> <?php echo TGlobal::OutHTML('Achtung'); ?>
                        <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu" role="search" style="min-width: 370px;">

                        <div class="row">
                            <div class="col-md-12">
                                <ul>
                                    <?php
                                    foreach ($data['check_messages'] as $message) {
                                        echo '<li>'.$message.'</li>';
                                    } ?>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            <?php
            } ?>
            <ul class="nav navbar-nav">
                <?php
                if ($data['showWebsiteEditNavi'] && $data['show_template_engine']) {
                    if ($data['showNaviManager']) {
                        $windowTitle = $translator->trans('chameleon_system_core.cms_module_page_tree.headline');
                        $fieldName = 'mainNavNavi';
                        $url = PATH_CMS_CONTROLLER.'?pagedef=CMSModulePageTreePlain&table=cms_tpl_page&noassign=1&rootID='.$data['startTreeID'];
                        $naviJS = "CreateModalIFrameDialogCloseButton('".$url."',950,690,'".$windowTitle."');"; ?>
                        <li><a href="javascript:<?=$naviJS; ?>" data-toggle="tooltip" data-placement="bottom" title="<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_edit_navigation_help')); ?>"><span class="glyphicon glyphicon-leaf"></span> &nbsp;<?php echo TGlobal::Translate('chameleon_system_core.cms_module_header.action_edit_navigation'); ?></a></li>
                    <?php
                    } ?>
                    <li><a href="<?=PATH_CMS_CONTROLLER; ?>?pagedef=tablemanager&amp;id=<?=$data['table_id_cms_tpl_page']; ?>" data-toggle="tooltip" data-placement="bottom" title="<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_website_list_help')); ?>"><span class="glyphicon glyphicon-folder-open"></span> &nbsp;<?php echo TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_website_list'); ?></a></li>
                    <?php
                } ?>

                <?php
                if ($data['showImageManagerNavi']) {
                    $mediaManagerUrlGenerator = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.media_manager.url_generator');
                    $onClickEvent = '';
                    if ($mediaManagerUrlGenerator->openStandaloneMediaManagerInNewWindow()) {
                        $onClickEvent = 'onclick="window.open(\''.$mediaManagerUrlGenerator->getStandaloneMediaManagerUrl().'\',\'mediaManager\',\'_blank\'); return false;"';
                    } ?>
                    <li><a href="<?=$mediaManagerUrlGenerator->getStandaloneMediaManagerUrl(); ?>" <?=$onClickEvent; ?> data-toggle="tooltip" data-placement="bottom" title="<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_media_manager_help')); ?>"><span class="glyphicon glyphicon-picture"></span> &nbsp;<?php echo TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_media_manager'); ?></a></li>
                <?php
                }

            if ($data['showDocumentManagerNavi']) {
                ?>
                    <li><a href="javascript:loadStandaloneDocumentManager();" data-toggle="tooltip" data-placement="bottom" title="<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_document_manager_help')); ?>"><span class="glyphicon glyphicon-hdd"></span> &nbsp;<?php echo TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_document_manager'); ?></a></li>
                <?php
            }

            if (isset($editLanguages) && count($editLanguages) > 1) {
                $urlToActiveLanguageFlag = TGlobal::GetPathTheme().'/images/icons/language-flags/'.strtolower($activeEditLanguageIso).'.png'; ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="cmsNavIcon" style="background-image: url(<?=$urlToActiveLanguageFlag; ?>);"></span><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.menu_edit_language_menu')); ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                    <?php
                        $oGlobal = TGlobal::instance();
                $authenticityTokenId = AuthenticityTokenManagerInterface::TOKEN_ID;
                $aParam = $oGlobal->GetUserData(null, array('module_fnc', '_fnc', 'editLanguageID', $authenticityTokenId));
                foreach ($editLanguages as $languageIso => $languageName) {
                    if (strtolower($activeEditLanguageIso) != strtolower($languageIso)) {
                        $aParam['module_fnc'] = array($data['sModuleSpotName'] => 'ChangeEditLanguage');
                        $aParam['editLanguageID'] = $languageIso;
                        $sLanguageURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURL($aParam);
                        $urlToLanguageFlag = TGlobal::GetPathTheme().'/images/icons/language-flags/'.strtolower($languageIso).'.png'; ?>
                                <li><a href="<?=$sLanguageURL; ?>"><span class="cmsNavIcon" style="background-image: url(<?=$urlToLanguageFlag; ?>);"></span><?=$languageName; ?></a></li>
                            <?php
                    }
                } ?>
                    </ul>
                </li>
                <?php
            }

            $viewRenderer = new ViewRenderer();
            $viewRenderer->AddSourceObject('aPortalLinks', $aPortalLinks);
            echo $viewRenderer->Render('MTHeader/portalLinks.html.twig');

            if (isset($aCustomMenuItems) && is_array($aCustomMenuItems) && count($aCustomMenuItems) > 0) {
                foreach ($aCustomMenuItems as $sItemIndex => $aItemContent) {
                    ?>
                        <li><a href=""><?php if (isset($aItemContent['iconUrl']) && !empty($aItemContent['iconUrl'])); ?><span class="cmsNavIcon" style="background-image: url(<?=TGlobal::OutHTML($aItemContent['iconUrl']); ?>);"></span><?=TGlobal::OutHTML($aItemContent['name']); ?></a></li>
                    <?php
                }
            }
            if ($data['showCacheButton']) {
                ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-refresh"></span> <?=TGlobal::Translate('chameleon_system_core.cms_module_header.menu_cache'); ?> <b class="caret"></b></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="javascript:GetAjaxCall('<?=$clearCacheURL; ?>', DisplayAjaxMessage)" title="<?= $translator->trans('chameleon_system_core.cms_module_header.action_clear_page_cache_title'); ?>"><span class="glyphicon glyphicon-refresh"></span> <?= $translator->trans('chameleon_system_core.cms_module_header.action_clear_page_cache'); ?></a></li>
                            <li><a href="javascript:GetAjaxCall('<?=$clearCacheURL; ?>&clearFiles=true', DisplayAjaxMessage)" title="<?= $translator->trans('chameleon_system_core.cms_module_header.action_clear_full_cache_title'); ?>"><span class="glyphicon glyphicon-refresh"></span> <?= $translator->trans('chameleon_system_core.cms_module_header.action_clear_full_cache'); ?></a></li>
                        </ul>
                    </li>
                <?php
            } ?>

                <li><a href="<?=PATH_CMS_CONTROLLER; ?>?pagedef=CMSModuleHelp" target="_blank"><span class="glyphicon glyphicon-question-sign"></span> &nbsp;<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_help')); ?></a></li>
            </ul>

            <div class="btn-toolbar" role="toolbar">
                <?php
                /**
                 * @var ViewRenderer $viewRenderer
                 */
                $viewRenderer = ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
            $viewRenderer->addMapperFromIdentifier('chameleon_system_core.mapper.update_recorder');
            $viewRenderer->AddSourceObject('sModuleSpotName', $sModuleSpotName);
            echo $viewRenderer->Render('MTUpdateRecorder/flyout.html.twig');

            $sUserButtonStyle = 'btn-default';
            $oUser = TCMSUser::GetActiveUser();
            $bIsAdminUser = ($oUser && $oUser->oAccessManager && $oUser->oAccessManager->user && $oUser->oAccessManager->user->IsAdmin());
            if (!_DEVELOPMENT_MODE && $bIsAdminUser) {
                $sUserButtonStyle = 'btn-danger';
            } ?>

                <div class="btn-group pull-right">
                    <button type="button" class="btn <?=$sUserButtonStyle; ?> btn-sm dropdown-toggle navbar-btn" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-user"></span> <?=$oUser->fieldLogin; ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?=PATH_CMS_CONTROLLER; ?>?pagedef=tableeditor&amp;tableid=<?=$data['iTableIDCMSUser']; ?>&amp;id=<?=$data['oUser']->id; ?>&amp;<?=urlencode('module_fnc[contentmodule]'); ?>"><span class="glyphicon glyphicon-pencil"></span> <?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_profile')); ?></a></li>
                        <li><a href="<?=PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURL(array('pagedef' => 'login', 'module_fnc' => array('contentmodule' => 'Logout'))); ?>"><span class="glyphicon glyphicon-log-out"></span> <?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_logout')); ?></a></li>
                    </ul>
                </div>
            </div>


        </div>
        <?php
        }
        ?>
    </div>
</nav>