<?php

use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

$translator = ServiceLocator::get('translator');
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" role="navigation" id="header-nav">
    <div class="container-fluid">
        <a href="<?=PATH_CMS_CONTROLLER; ?>?_rmhist=true&_histid=0" class="navbar-brand"><img src="<?=TGlobal::OutHTML($sLogoURL); ?>" /></a>
        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#cmsTopNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <?php
        if (isset($data['oUser'])) {
            ?>
        <div class="collapse navbar-collapse" id="cmsTopNavbar">
            <?php
            if (isset($data['check_messages'])) {
                ?>
                <div class="btn-group float-left">
                    <button type="button" class="btn btn-sm dropdown-toggle navbar-item btn-secondary" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-warning-sign"></span> <?php echo TGlobal::OutHTML('Achtung'); ?>
                    </button>
                    <div class="dropdown-menu" role="search" style="min-width: 370px;">

                        <div class="row">
                            <div class="col-lg-12">
                                <ul>
                                    <?php
                                    foreach ($data['check_messages'] as $message) {
                                        echo '<li class="dropdown-item">'.$message.'</li>';
                                    } ?>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            <?php
            } ?>
            <ul class="navbar-nav mr-auto">
                <?php
                if ($data['showWebsiteEditNavi'] && $data['show_template_engine']) {
                    if ($data['showNaviManager']) {
                        $windowTitle = $translator->trans('chameleon_system_core.cms_module_page_tree.headline');
                        $fieldName = 'mainNavNavi';
                        $url = PATH_CMS_CONTROLLER.'?pagedef=CMSModulePageTreePlain&table=cms_tpl_page&noassign=1&rootID='.$data['startTreeID'];
                        $naviJS = "CreateModalIFrameDialogCloseButton('".$url."',950,690,'".$windowTitle."');"; ?>
                        <li class="nav-item"><a href="javascript:<?=$naviJS; ?>" class="nav-link" data-toggle="tooltip" data-placement="bottom" title="<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_edit_navigation_help')); ?>"><span class="glyphicon glyphicon-leaf"></span> &nbsp;<?php echo TGlobal::Translate('chameleon_system_core.cms_module_header.action_edit_navigation'); ?></a></li>
                    <?php
                    } ?>
                    <li class="nav-item"><a href="<?=PATH_CMS_CONTROLLER; ?>?pagedef=tablemanager&amp;id=<?=$data['table_id_cms_tpl_page']; ?>" class="nav-link" data-toggle="tooltip" data-placement="bottom" title="<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_website_list_help')); ?>"><span class="glyphicon glyphicon-folder-open"></span> &nbsp;<?php echo TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_website_list'); ?></a></li>
                    <?php
                } ?>

                <?php
                if ($data['showImageManagerNavi']) {
                    $mediaManagerUrlGenerator = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.media_manager.url_generator');
                    $onClickEvent = '';
                    if ($mediaManagerUrlGenerator->openStandaloneMediaManagerInNewWindow()) {
                        $onClickEvent = 'onclick="window.open(\''.$mediaManagerUrlGenerator->getStandaloneMediaManagerUrl().'\',\'mediaManager\',\'_blank\'); return false;"';
                    } ?>
                    <li class="nav-item"><a href="<?=$mediaManagerUrlGenerator->getStandaloneMediaManagerUrl(); ?>" <?=$onClickEvent; ?> class="nav-link" data-toggle="tooltip" data-placement="bottom" title="<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_media_manager_help')); ?>"><span class="glyphicon glyphicon-picture"></span> &nbsp;<?php echo TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_media_manager'); ?></a></li>
                <?php
                }

            if ($data['showDocumentManagerNavi']) {
                ?>
                    <li class="nav-item"><a href="javascript:loadStandaloneDocumentManager();" class="nav-link" data-toggle="tooltip" data-placement="bottom" title="<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_document_manager_help')); ?>"><span class="glyphicon glyphicon-hdd"></span> &nbsp;<?php echo TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_document_manager'); ?></a></li>
                <?php
            }

            if (isset($editLanguages) && count($editLanguages) > 1) {
                $urlToActiveLanguageFlag = TGlobal::GetPathTheme().'/images/icons/language-flags/'.strtolower($activeEditLanguageIso).'.png'; ?>
                <li class="nav-item dropdown">
                    <a href="#" id="navbarDropdownLanguage" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="cmsNavIcon" style="background-image: url(<?=$urlToActiveLanguageFlag; ?>);"></span><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.menu_edit_language_menu')); ?> </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownLanguage">
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
                                <a href="<?=$sLanguageURL; ?>" class="dropdown-item"><span class="cmsNavIcon" style="background-image: url(<?=$urlToLanguageFlag; ?>);"></span><?=$languageName; ?></a>
                            <?php
                    }
                } ?>
                    </div>
                </li>
                <?php
            }

            /**
             * @var ViewRenderer $viewRenderer
             */
            $viewRenderer = ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
            $viewRenderer->AddSourceObject('aPortalLinks', $aPortalLinks);
            echo $viewRenderer->Render('MTHeader/portalLinks.html.twig');

            if (isset($aCustomMenuItems) && is_array($aCustomMenuItems) && count($aCustomMenuItems) > 0) {
                foreach ($aCustomMenuItems as $sItemIndex => $aItemContent) {
                    ?>
                        <li class="nav-item"><a href="" class="nav-link"><?php if (isset($aItemContent['iconUrl']) && !empty($aItemContent['iconUrl'])); ?><span class="cmsNavIcon" style="background-image: url(<?=TGlobal::OutHTML($aItemContent['iconUrl']); ?>);"></span><?=TGlobal::OutHTML($aItemContent['name']); ?></a></li>
                    <?php
                }
            }
            if ($data['showCacheButton']) {
                ?>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" id="navbarDropdownCache" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-refresh"></span> <?=TGlobal::Translate('chameleon_system_core.cms_module_header.menu_cache'); ?></a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownCache">
                            <a href="javascript:GetAjaxCall('<?=$clearCacheURL; ?>', DisplayAjaxMessage)" class="dropdown-item" title="<?= $translator->trans('chameleon_system_core.cms_module_header.action_clear_page_cache_title'); ?>"><span class="glyphicon glyphicon-refresh"></span> <?= $translator->trans('chameleon_system_core.cms_module_header.action_clear_page_cache'); ?></a>
                            <a href="javascript:GetAjaxCall('<?=$clearCacheURL; ?>&clearFiles=true', DisplayAjaxMessage)" class="dropdown-item" title="<?= $translator->trans('chameleon_system_core.cms_module_header.action_clear_full_cache_title'); ?>"><span class="glyphicon glyphicon-refresh"></span> <?= $translator->trans('chameleon_system_core.cms_module_header.action_clear_full_cache'); ?></a>
                        </div>
                    </li>
                <?php
            } ?>

                <li class="nav-item"><a href="<?=PATH_CMS_CONTROLLER; ?>?pagedef=CMSModuleHelp" class="nav-link" target="_blank"><span class="glyphicon glyphicon-question-sign"></span> &nbsp;<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_help')); ?></a></li>
            </ul>
                    <?php
            /**
             * @var ViewRenderer $viewRenderer
             */
            $viewRenderer = ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
            $viewRenderer->addMapperFromIdentifier('chameleon_system_core.mapper.update_recorder');
            $viewRenderer->AddSourceObject('sModuleSpotName', $sModuleSpotName);
            echo $viewRenderer->Render('MTUpdateRecorder/flyout.html.twig');

            $sUserButtonStyle = 'btn-secondary';
            $oUser = TCMSUser::GetActiveUser();
            $bIsAdminUser = ($oUser && $oUser->oAccessManager && $oUser->oAccessManager->user && $oUser->oAccessManager->user->IsAdmin());
            if (!_DEVELOPMENT_MODE && $bIsAdminUser) {
                $sUserButtonStyle = 'btn-danger';
            } ?>

                    <div class="btn-group float-right" role="group">
                        <button type="button" class="btn <?=$sUserButtonStyle; ?> btn-sm dropdown-toggle nav-item" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-user"></span> <?=$oUser->fieldLogin; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">
                            <a href="<?=PATH_CMS_CONTROLLER; ?>?pagedef=tableeditor&amp;tableid=<?=$data['iTableIDCMSUser']; ?>&amp;id=<?=$data['oUser']->id; ?>&amp;<?=urlencode('module_fnc[contentmodule]'); ?>" class="dropdown-item"><span class="glyphicon glyphicon-pencil"></span> <?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_open_profile')); ?></a>
                            <a href="<?=PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURL(array('pagedef' => 'login', 'module_fnc' => array('contentmodule' => 'Logout'))); ?>" class="dropdown-item"><span class="glyphicon glyphicon-log-out"></span> <?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_header.action_logout')); ?></a>
                        </div>
                    </div>
        </div>
        <?php
        }
        ?>
    </div>
</nav>