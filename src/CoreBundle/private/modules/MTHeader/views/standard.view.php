<?php

use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @var string $activeEditLanguageIso
 * @var array  $aPortalLinks
 * @var string $clearCacheURL
 * @var string $sLogoURL
 * @var string $sModuleSpotName
 */

/**
 * @var TranslatorInterface $translator
 */
$translator = ServiceLocator::get('translator');

if (false === isset($data['oUser'])) {
    echo '<span class="navbar-brand"><img src="'.TGlobal::OutHTML($sLogoURL).'" alt="" /></span>';

    return;
}

?>
        <button type="button" class="navbar-toggler sidebar-toggler d-lg-none" data-toggle="sidebar-show">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a href="<?=PATH_CMS_CONTROLLER; ?>?_rmhist=true&_histid=0" class="navbar-brand d-sm-down-none" style="background-image: url('<?= TGlobal::OutHTML($sLogoURL); ?>');">
&nbsp;
        </a>
        <button type="button" class="navbar-toggler sidebar-toggler d-md-down-none" data-toggle="sidebar-lg-show">
            <span class="navbar-toggler-icon"></span>
        </button>
            <ul class="nav navbar-nav">
                <?php
                if (true === isset($data['check_messages'])) {
                    ?>
                    <li class="nav-item px2 dropdown">
                        <a
                                class="nav-link dropdown-toggle text-danger"
                                data-toggle="dropdown"
                                href="#"
                                role="button"
                                aria-haspopup="true"
                                aria-expanded="false"
                        >
                            <i class="fas fa-exclamation-triangle"></i>
                            <span class="d-md-down-none">
                                <?= TGlobal::OutHTML($translator->trans('chameleon_system_core.admin_message.button_title')); ?>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-left">
                            <?php
                            foreach ($data['check_messages'] as $message) {
                                echo '<span class="dropdown-item">'.$message.'</span>';
                            } ?>
                        </div>
                    </li>
                    <?php
                }

            if (isset($editLanguages) && count($editLanguages) > 1) {
                $urlToActiveLanguageFlag = TGlobal::GetPathTheme().'/images/icons/language-flags/'.strtolower($activeEditLanguageIso).'.png'; ?>
                <li class="nav-item px-2 dropdown">
                    <a
                            id="navbarDropdownLanguage"
                            class="nav-link dropdown-toggle"
                            data-toggle="dropdown"
                            href="#"
                            role="button"
                            aria-haspopup="true"
                            aria-expanded="false"
                    >
                        <span class="cmsNavIcon" style="background-image: url(<?= $urlToActiveLanguageFlag; ?>)"></span>
                        <span class="d-md-down-none">
                            <?= TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_header.menu_edit_language_menu')); ?>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-left">
                        <?php
                            $authenticityTokenId = AuthenticityTokenManagerInterface::TOKEN_ID;
                $aParam = TGlobal::instance()->GetUserData(null, array('module_fnc', '_fnc', 'editLanguageID', $authenticityTokenId));
                foreach ($editLanguages as $languageIso => $languageName) {
                    if (strtolower($activeEditLanguageIso) != strtolower($languageIso)) {
                        $aParam['module_fnc'] = array($data['sModuleSpotName'] => 'ChangeEditLanguage');
                        $aParam['editLanguageID'] = $languageIso;
                        $sLanguageURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURL($aParam);
                        $urlToLanguageFlag = TGlobal::GetPathTheme().'/images/icons/language-flags/'.strtolower($languageIso).'.png';
                        echo '<a href="'.$sLanguageURL.'" class="dropdown-item"><span class="cmsNavIcon" style="background-image: url('.$urlToLanguageFlag.')"></span>'.$languageName.'</a>';
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
                        <li class="nav-item px-2">
                            <a href="" class="nav-link">
                                <?php if (isset($aItemContent['iconUrl']) && !empty($aItemContent['iconUrl'])); ?>
                                <span class="cmsNavIcon" style="background-image: url(<?=TGlobal::OutHTML($aItemContent['iconUrl']); ?>);"></span>
                                <span class="d-md-down-none">
                                    <?= TGlobal::OutHTML($aItemContent['name']); ?>
                                </span>
                            </a>
                        </li>
                    <?php
                }
            }
            if ($data['showCacheButton']) {
                ?>
                    <li class="nav-item px-2 dropdown">
                        <a
                                id="navbarDropdownCache"
                                class="nav-link dropdown-toggle"
                                data-toggle="dropdown"
                                href="#"
                                role="button"
                                aria-haspopup="true"
                                aria-expanded="false"
                        >
                            <i class="fas fa-sync"></i>
                            <span class="d-md-down-none">
                                <?= $translator->trans('chameleon_system_core.cms_module_header.menu_cache'); ?>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-left">
                            <a class="dropdown-item" href="javascript:GetAjaxCall('<?=$clearCacheURL; ?>', DisplayAjaxMessage)" title="<?= $translator->trans('chameleon_system_core.cms_module_header.action_clear_page_cache_title'); ?>">
                                <i class="fas fa-sync"></i>
                                <?= $translator->trans('chameleon_system_core.cms_module_header.action_clear_page_cache'); ?>
                            </a>
                            <a class="dropdown-item" href="javascript:GetAjaxCall('<?=$clearCacheURL; ?>&clearFiles=true', DisplayAjaxMessage)" title="<?= $translator->trans('chameleon_system_core.cms_module_header.action_clear_full_cache_title'); ?>">
                                <i class="fas fa-sync"></i>
                                <?= $translator->trans('chameleon_system_core.cms_module_header.action_clear_full_cache'); ?>
                            </a>
                        </div>
                    </li>
                <?php
            } ?>
                <li class="nav-item px-2">
                    <a href="<?=PATH_CMS_CONTROLLER; ?>?pagedef=CMSModuleHelp" class="nav-link" onclick="CreateModalIFrameDialog(this.href+'&isInIFrame=1',0,0,'<?=TGlobal::OutJS($translator->trans('chameleon_system_core.cms_module_header.action_help')); ?>');return false;" target="_blank">
                        <i class="fas fa-question-circle"></i>
                        <span class="d-md-down-none">
                            <?=TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_header.action_help')); ?>
                        </span>
                    </a>
                </li>
            </ul>


            <ul class="nav navbar-nav ml-auto">
                <?php
                    /**
                     * @var ViewRenderer $viewRenderer
                     */
                    $viewRenderer = ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
                    $viewRenderer->addMapperFromIdentifier('chameleon_system_core.mapper.update_recorder');
                    $viewRenderer->AddSourceObject('sModuleSpotName', $sModuleSpotName);

                    echo $viewRenderer->Render('MTUpdateRecorder/flyout.html.twig');

            $userButtonStyle = '';
            $oUser = TCMSUser::GetActiveUser();
            $bIsAdminUser = ($oUser && $oUser->oAccessManager && $oUser->oAccessManager->user && $oUser->oAccessManager->user->IsAdmin());
            if (!_DEVELOPMENT_MODE && $bIsAdminUser) {
                $userButtonStyle = 'text-danger';
            } ?>

                    <li class="nav-item px-2 dropdown">
                        <a
                            class="nav-link dropdown-toggle <?= $userButtonStyle; ?>"
                            data-toggle="dropdown"
                            href="#"
                            role="button"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <i class="fas fa-user"></i>
                            <span class="d-md-down-none">
                                <?=$oUser->fieldLogin; ?>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="<?= PATH_CMS_CONTROLLER; ?>?pagedef=tableeditor&tableid=<?= $data['iTableIDCMSUser']; ?>&id=<?= $data['oUser']->id; ?>&<?= urlencode('module_fnc[contentmodule]'); ?>">
                                <i class="fas fa-user"></i>
                                <?= TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_header.action_open_profile')); ?>
                            </a>
                            <a class="dropdown-item" href="<?= PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURL(array('pagedef' => 'login', 'module_fnc' => array('contentmodule' => 'Logout'))); ?>">
                                <i class="fas fa-sign-out-alt"></i>
                                <?= TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_header.action_logout')); ?>
                            </a>
                        </div>
                    </li>
            </ul>
