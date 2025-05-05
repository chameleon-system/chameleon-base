<?php

use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @var string $activeEditLanguageIso
 * @var array $aPortalLinks
 * @var string $clearCacheURL
 * @var string $sLogoURL
 * @var string $sModuleSpotName
 */

/**
 * @var TranslatorInterface $translator
 */
$translator = ServiceLocator::get('translator');
/** @var SecurityHelperAccess $securityHelper */
$securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
$user = $securityHelper->getUser();

if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
    echo '<span class="navbar-brand"><img src="'.TGlobal::OutHTML($sLogoURL).'" alt="" /></span>';

    return;
}

?>
<div class="d-flex">
  <button class="header-toggler px-md-0 megamenu-md-3 me-3" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
    <i class="fas fa-bars"></i>
  </button>
  <ul class="header-nav d-flex">
      <?php
      if (true === isset($data['check_messages'])) {
          ?>
        <li class="nav-item px2 dropdown">
          <a
              class="nav-link dropdown-toggle text-danger"
              data-coreui-toggle="dropdown"
              data-coreui-auto-close="outside"
              href="#"
              role="button"
              aria-haspopup="true"
              aria-expanded="false"
          >
            <i class="fas fa-exclamation-triangle"></i>
            <span class="d-md-down-none">
                                <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.admin_message.button_title')); ?>
                            </span>
          </a>
          <div class="dropdown-menu dropdown-menu-start">
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
              data-coreui-toggle="dropdown"
              data-coreui-auto-close="outside"
              href="#"
              role="button"
              aria-haspopup="true"
              aria-expanded="false"
          >
            <span class="cmsNavIcon" style="background-image: url(<?php echo $urlToActiveLanguageFlag; ?>)"></span>
            <span class="d-md-down-none">
                            <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_header.menu_edit_language_menu')); ?>
                        </span>
          </a>
          <div class="dropdown-menu dropdown-menu-start">
              <?php
        $authenticityTokenId = AuthenticityTokenManagerInterface::TOKEN_ID;
    $aParam = TGlobal::instance()->GetUserData(null, ['module_fnc', '_fnc', 'editLanguageIsoCode', $authenticityTokenId]);
    foreach ($editLanguages as $languageIso => $languageName) {
        if (strtolower($activeEditLanguageIso) != strtolower($languageIso)) {
            $aParam['module_fnc'] = [$data['sModuleSpotName'] => 'ChangeEditLanguage'];
            $aParam['editLanguageIsoCode'] = $languageIso;
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
                <span class="cmsNavIcon" style="background-image: url(<?php echo TGlobal::OutHTML($aItemContent['iconUrl']); ?>);"></span>
                <span class="d-md-down-none">
                                    <?php echo TGlobal::OutHTML($aItemContent['name']); ?>
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
              data-coreui-toggle="dropdown"
              data-coreui-auto-close="outside"
              href="#"
              role="button"
              aria-haspopup="true"
              aria-expanded="false"
          >
            <i class="fas fa-sync"></i>
            <span class="d-md-down-none">
                                <?php echo $translator->trans('chameleon_system_core.cms_module_header.menu_cache'); ?>
                            </span>
          </a>
          <div class="dropdown-menu dropdown-menu-start">
            <a class="dropdown-item" href="javascript:GetAjaxCall('<?php echo $clearCacheURL; ?>', DisplayAjaxMessage)" title="<?php echo $translator->trans('chameleon_system_core.cms_module_header.action_clear_page_cache_title'); ?>">
              <i class="fas fa-sync"></i>
                <?php echo $translator->trans('chameleon_system_core.cms_module_header.action_clear_page_cache'); ?>
            </a>
            <a class="dropdown-item" href="javascript:GetAjaxCall('<?php echo $clearCacheURL; ?>&clearFiles=true', DisplayAjaxMessage)" title="<?php echo $translator->trans('chameleon_system_core.cms_module_header.action_clear_full_cache_title'); ?>">
              <i class="fas fa-sync"></i>
                <?php echo $translator->trans('chameleon_system_core.cms_module_header.action_clear_full_cache'); ?>
            </a>
          </div>
        </li>
          <?php
} ?>
    <li class="nav-item px-2">
      <a href="<?php echo PATH_CMS_CONTROLLER; ?>?pagedef=CMSModuleHelp" class="nav-link" onclick="CreateModalIFrameDialog(this.href+'&isInIFrame=1',0,0,'<?php echo TGlobal::OutJS($translator->trans('chameleon_system_core.cms_module_header.action_help')); ?>');return false;" target="_blank">
        <i class="fas fa-question-circle"></i>
        <span class="d-md-down-none">
                            <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_header.action_help')); ?>
                        </span>
      </a>
    </li>
  </ul>
</div>

<ul class="header-nav d-flex">
    <?php
  /**
   * @var ViewRenderer $viewRenderer
   */
  $viewRenderer = ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
$viewRenderer->addMapperFromIdentifier('chameleon_system_core.mapper.update_recorder');
$viewRenderer->AddSourceObject('sModuleSpotName', $sModuleSpotName);

echo $viewRenderer->Render('MTUpdateRecorder/flyout.html.twig');

$userButtonStyle = '';

$bIsAdminUser = $securityHelper->isGranted(CmsUserRoleConstants::CMS_ADMIN);
if (!_DEVELOPMENT_MODE && $bIsAdminUser) {
    $userButtonStyle = 'text-danger';
} ?>

        <li class="nav-item px-2 dropdown">
            <a
                class="nav-link dropdown-toggle <?php echo $userButtonStyle; ?>"
                data-coreui-toggle="dropdown"
                data-coreui-auto-close="outside"
                href="#"
                role="button"
                aria-haspopup="true"
                aria-expanded="false"
            >
                <i class="fas fa-user"></i>
                <span class="d-md-down-none">
                    <?php echo $securityHelper->getUser()?->getUserIdentifier(); ?>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="<?php echo PATH_CMS_CONTROLLER; ?>?pagedef=tableeditor&tableid=<?php echo $data['iTableIDCMSUser']; ?>&id=<?php echo $user?->getId(); ?>&<?php echo urlencode('module_fnc[contentmodule]'); ?>">
                    <i class="fas fa-user"></i>
                    <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_header.action_open_profile')); ?>
                </a>
                <a class="dropdown-item" href="<?php echo $data['logoutUrl']; ?>">
                    <i class="fas fa-sign-out-alt"></i>
                    <?php echo TGlobal::OutHTML($translator->trans('chameleon_system_core.cms_module_header.action_logout')); ?>
                </a>
            </div>
        </li>
</ul>
