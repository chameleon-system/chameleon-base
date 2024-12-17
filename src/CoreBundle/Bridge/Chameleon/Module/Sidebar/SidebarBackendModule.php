<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\Sidebar;

use ChameleonSystem\CoreBundle\DataAccess\MenuItemDataAccessInterface;
use ChameleonSystem\CoreBundle\DataAccess\UserMenuItemDataAccessInterface;
use ChameleonSystem\CoreBundle\Response\ResponseVariableReplacerInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SidebarBackendModule extends \MTPkgViewRendererAbstractModuleMapper
{
    private const OPEN_CATEGORIES_SESSION_KEY = 'chameleonSidebarBackendModuleOpenCategories';
    private const DISPLAY_STATE_SESSION_KEY = 'chameleonSidebarBackendModuleDisplayState';
    private const POPULAR_CATEGORY_ID = '0000000-0000-0001-0000-000000000001';

    public function __construct(
        private readonly UrlUtil $urlUtil,
        private readonly RequestStack $requestStack,
        private readonly InputFilterUtilInterface $inputFilterUtil,
        private readonly ResponseVariableReplacerInterface $responseVariableReplacer,
        private readonly TranslatorInterface $translator,
        private readonly MenuItemDataAccessInterface $menuItemDataAccess,
        private readonly UserMenuItemDataAccessInterface $userMenuItemDataAccess
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();

        $this->methodCallAllowed[] = 'saveActiveCategory';
        $this->methodCallAllowed[] = 'reportElementClick';
    }

    public function Init()
    {
        parent::Init();
        $this->restoreDisplayState();
    }

    /**
     * To enable sensible module caching.
     */
    private function restoreDisplayState(): void
    {
        $session = $this->getSession();
        $displayState = null !== $session ? $session->get(self::DISPLAY_STATE_SESSION_KEY, '') : '';
        if ('minimized' === $displayState) {
            $value = 'sidebar-minimized';
        } else {
            $value = '';
        }

        $this->responseVariableReplacer->addVariable('sidebarDisplayState', $value);
        $foo = \TGlobal::OutHTML($this->getOpenCategory());
        $this->responseVariableReplacer->addVariable('sidebarOpenCategoryId', $foo);
    }

    private function getSession(): ?SessionInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }
        if (false === $request->hasSession()) {
            return null;
        }

        return $request->getSession();
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(\IMapperVisitorRestricted $visitor, $cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        $visitor->SetMappedValue('popularCategoryId', self::POPULAR_CATEGORY_ID);
        $visitor->SetMappedValue('sidebarToggleCategoryNotificationUrl', $this->getNotificationUrl('saveActiveCategory'));
        $visitor->SetMappedValue('sidebarElementClickNotificationUrl', $this->getNotificationUrl('reportElementClick'));
        $visitor->SetMappedValue('menuItems', $this->getMenuItems());

        $oConfig = \TdbCmsConfig::GetInstance();
        $logoUrl = $oConfig->GetThemeURL().'/images/chameleon_logo.svg';
        $visitor->SetMappedValue('logoUrl', $logoUrl);

        if (true === $cachingEnabled) {
            /** @var SecurityHelperAccess $securityHelper */
            $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
            $cacheTriggerManager->addTrigger('cms_tbl_conf', null);
            $cacheTriggerManager->addTrigger('cms_module', null);
            $cacheTriggerManager->addTrigger('cms_user', $securityHelper->getUser()?->getId());
            $cacheTriggerManager->addTrigger('cms_menu_category', null);
            $cacheTriggerManager->addTrigger('cms_menu_item', null);
        }
    }

    /**
     * Returns the base URI for sidebar-related actions.
     * The method uses a dummy pagedef because otherwise there would be interference with other modules (even for AJAX
     * calls, the Init() method of all modules on a page is called. If the list module is on that page, it would add all
     * parameters of the AJAX call to its own, which leads to the sidebar action being called on form submits).
     * Similar problems are expected for other modules, so we use a dummy page that is both always present and contains
     * no other modules.
     */
    private function getBaseUri(): string
    {
        return \PATH_CMS_CONTROLLER.'?pagedef=sidebarDummy&';
    }

    private function getNotificationUrl(string $subFunction): string
    {
        return $this->urlUtil->getArrayAsUrl([
            'module_fnc' => [
                $this->sModuleSpotName => 'ExecuteAjaxCall',
            ],
            '_fnc' => $subFunction,
        ], $this->getBaseUri(), '&');
    }

    private function getMenuItems(): array
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
            return [];
        }

        $menuCategories = $this->menuItemDataAccess->getMenuCategories();

        $popularCategory = $this->getPopularMenuEntries($menuCategories);
        if (null !== $popularCategory) {
            \array_unshift($menuCategories, $popularCategory);
        }

        return $menuCategories;
    }

    private function getOpenCategory(): string
    {
        $session = $this->getSession();

        if (null === $session) {
            return '';
        }

        $openCategory = $session->get(self::OPEN_CATEGORIES_SESSION_KEY, '');

        // Compatibility with version 7.x, because it was an array
        if (is_array($openCategory) && !empty($openCategory)) {
            return reset($openCategory);
        }

        return $openCategory;
    }

    /**
     * @param MenuCategory[] $menuCategories
     */
    private function getPopularMenuEntries(array $menuCategories): ?MenuCategory
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
            return null;
        }

        $userId = $securityHelper->getUser()?->getId();
        if (null === $userId) {
            return null;
        }

        $menuItemMap = [];

        foreach ($menuCategories as $menuCategory) {
            foreach ($menuCategory->getMenuItems() as $menuItem) {
                $menuItemMap[$menuItem->getId()] = $menuItem;
            }
        }

        $menuItemsClickedByUser = $this->userMenuItemDataAccess->getMenuItemIds($userId);
        $menuItemsClickedByUser = \array_slice($menuItemsClickedByUser, 0, 6);

        $items = [];

        foreach ($menuItemsClickedByUser as $menuId) {
            if (\array_key_exists($menuId, $menuItemMap)) {
                $items[] = $menuItemMap[$menuId];
            }
        }

        if (0 === $items) {
            return null;
        }

        return new MenuCategory(
            self::POPULAR_CATEGORY_ID,
            $this->translator->trans('chameleon_system_core.sidebar.popular_entries'),
            'fas fa-fire',
            $items
        );
    }

    protected function saveActiveCategory(): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }

        $newCategoryId = $this->inputFilterUtil->getFilteredPostInput('categoryId', '');
        $session->set(self::OPEN_CATEGORIES_SESSION_KEY, $newCategoryId);
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $includes = parent::GetHtmlHeadIncludes();
        $includes[] = sprintf('<link rel="stylesheet" href="%s/coreui/css/simplebar.css" type="text/css" />', \TGlobal::GetPathTheme());

        return $includes;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlFooterIncludes()
    {
        $includes = parent::GetHtmlFooterIncludes();
        $includes[] = sprintf('<script src="%s" type="text/javascript"></script>',
            \TGlobal::GetStaticURLToWebLib('/javascript/modules/sidebar/sidebar.js'));

        return $includes;
    }

    protected function reportElementClick(): void
    {
        $menuId = $this->inputFilterUtil->getFilteredPostInput('clickedMenuId', '');

        if ('' === $menuId) {
            return;
        }
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
            return;
        }

        $userId = $securityHelper->getUser()?->getId();

        if (null === $userId) {
            return;
        }

        $this->userMenuItemDataAccess->trackMenuItem($userId, $menuId);
    }

    /**
     * {@inheritdoc}
     */
    public function _AllowCache()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function _GetCacheParameters()
    {
        $parameters = parent::_GetCacheParameters();

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $user = $securityHelper->getUser();

        if (null !== $user) {
            $parameters['cmsUserId'] = $user->getId();
            $parameters['backendLanguageId'] = $user->getCmsLanguageId();

            $session = $this->getSession();
            if (null !== $session) {
                $parameters['sessionId'] = $session->getId();
            }
        }

        return $parameters;
    }
}
