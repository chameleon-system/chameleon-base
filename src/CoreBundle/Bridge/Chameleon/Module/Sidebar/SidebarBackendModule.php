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

use ChameleonSystem\CoreBundle\Response\ResponseVariableReplacerInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\HttpFoundation\RequestStack;

class SidebarBackendModule extends \MTPkgViewRendererAbstractModuleMapper
{
    /**
     * @deprecated since 6.3.8 - use OPEN_CATEGORIES_SESSION_KEY with an array
     */
    private const ACTIVE_CATEGORY_SESSION_KEY = 'chameleonSidebarBackendModuleActiveCategory';
    private const OPEN_CATEGORIES_SESSION_KEY = 'chameleonSidebarBackendModuleOpenCategories';
    private const DISPLAY_STATE_SESSION_KEY = 'chameleonSidebarBackendModuleDisplayState';

    /**
     * @var UrlUtil
     */
    private $urlUtil;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;
    /**
     * @var ResponseVariableReplacerInterface
     */
    private $responseVariableReplacer;
    /**
     * @var MenuItemFactoryInterface
     */
    private $menuItemFactory;

    public function __construct(UrlUtil $urlUtil, RequestStack $requestStack, InputFilterUtilInterface $inputFilterUtil, ResponseVariableReplacerInterface $responseVariableReplacer, MenuItemFactoryInterface $menuItemFactory)
    {
        parent::__construct();
        $this->urlUtil = $urlUtil;
        $this->requestStack = $requestStack;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->responseVariableReplacer = $responseVariableReplacer;
        $this->menuItemFactory = $menuItemFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'toggleSidebar';
        $this->methodCallAllowed[] = 'toggleCategoryOpenState';
    }

    public function Init()
    {
        parent::Init();
        $this->restoreDisplayState();
    }

    /**
     * To enable sensible module caching.
     * 
     * TODO! -> problem with popular entries
     */
    private function restoreDisplayState(): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $displayState = $session->get(self::DISPLAY_STATE_SESSION_KEY, '');
        if ('minimized' === $displayState) {
            $value = 'sidebar-minimized';
        } else {
            $value = '';
        }
        
        $this->responseVariableReplacer->addVariable('sidebarDisplayState', $value);
        $this->responseVariableReplacer->addVariable('sidebarOpenCategoryIds', \TGlobal::OutHTML(implode(',', $this->getOpenCategories())));
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(\IMapperVisitorRestricted $visitor, $cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        $visitor->SetMappedValue('sidebarToggleNotificationUrl', $this->getSidebarToggleNotificationUrl());
        $visitor->SetMappedValue('sidebarToggleCategoryNotificationUrl', $this->getToggleCategoryNotificationUrl());
        $visitor->SetMappedValue('menuItems', $this->getMenuItems());

        if (true === $cachingEnabled) {
            $cmsUser = \TCMSUser::GetActiveUser();
            $cacheTriggerManager->addTrigger('cms_tbl_conf', null);
            $cacheTriggerManager->addTrigger('cms_module', null);
            $cacheTriggerManager->addTrigger('cms_user', null === $cmsUser ? null : $cmsUser->id);
            $cacheTriggerManager->addTrigger('cms_menu_category', null);
            $cacheTriggerManager->addTrigger('cms_menu_item', null);
        }
    }

    private function getSidebarToggleNotificationUrl(): string
    {
        return $this->urlUtil->getArrayAsUrl([
            'module_fnc' => [
                $this->sModuleSpotName => 'ExecuteAjaxCall',
            ],
            '_fnc' => 'toggleSidebar',
        ], $this->getBaseUri(), '&');
    }

    /**
     * Returns the base URI for sidebar-related actions.
     * The method uses a dummy pagedef because otherwise there would be interference with other modules (even for AJAX
     * calls, the Init() method of all modules on a page is called. If the list module is on that page, it would add all
     * parameters of the AJAX call to its own, which leads to the sidebar action being called on form submits).
     * Similar problems are expected for other modules, so we use a dummy page that is both always present and contains
     * no other modules.
     *
     * @return string
     */
    private function getBaseUri(): string
    {
        return \PATH_CMS_CONTROLLER.'?pagedef=sidebarDummy&';
    }

    private function getToggleCategoryNotificationUrl(): string
    {
        return $this->urlUtil->getArrayAsUrl([
            'module_fnc' => [
                $this->sModuleSpotName => 'ExecuteAjaxCall',
            ],
            '_fnc' => 'toggleCategoryOpenState',
        ], $this->getBaseUri(), '&');
    }

    private function getMenuItems(): array
    {
        $activeUser = \TCMSUser::GetActiveUser();
        if (null === $activeUser) {
            return [];
        }

        $tdbCategoryList = \TdbCmsMenuCategoryList::GetList();
        $tdbCategoryList->ChangeOrderBy([
            '`cms_menu_category`.`position`' => 'ASC',
        ]);
        $menuCategories = [];
        while (false !== $tdbCategory = $tdbCategoryList->Next()) {
            $menuItems = [];
            $tdbMenuItemList = $tdbCategory->GetFieldCmsMenuItemList();
            $tdbMenuItemList->ChangeOrderBy([
                '`cms_menu_item`.`cms_menu_category_id`' => 'ASC',
                '`cms_menu_item`.`position`' => 'ASC',
            ]);
            while (false !== $tdbMenuItem = $tdbMenuItemList->Next()) {
                $menuItem = $this->menuItemFactory->createMenuItem($tdbMenuItem);
                if (null !== $menuItem) {
                    $menuItems[] = $menuItem;
                }
            }
            if (\count($menuItems) > 0) {
                $menuCategories[] = new MenuCategory(
                    $tdbCategory->id,
                    $tdbCategory->fieldName,
                    $tdbCategory->fieldIconFontCssClass,
                    $menuItems
                );
            }
        }

        return $menuCategories;
    }

    private function getOpenCategories(): array
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();

        if (null === $session) {
            return [];
        }

        return $session->get(self::OPEN_CATEGORIES_SESSION_KEY, []);
    }

    /**
     * @deprecated since 6.3.8 - use toggleCategoryOpenState
     */
    protected function saveActiveCategory(): void
    {
        $this->toggleCategoryOpenState();
    }

    protected function toggleCategoryOpenState(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        $session = $request->getSession();
        if (null === $session) {
            return;
        }

        $toggledCategoryId = $this->inputFilterUtil->getFilteredPostInput('categoryId', '');

        if ('' === $toggledCategoryId) {
            return;
        }

        $activeCategoryIds = $session->get(self::OPEN_CATEGORIES_SESSION_KEY, []);

        $index = array_search($toggledCategoryId, $activeCategoryIds, true);
        if (false !== $index){
            unset($activeCategoryIds[$index]);
        } else {
            $activeCategoryIds[] = $toggledCategoryId;
        }

        $session->set(self::OPEN_CATEGORIES_SESSION_KEY, $activeCategoryIds);

        // TODO could/should save this connected to the user (and not login session)?
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $includes = parent::GetHtmlHeadIncludes();
        $includes[] = sprintf('<link rel="stylesheet" href="%s/coreui/css/perfect-scrollbar.css" type="text/css" />',
            \TGlobal::GetPathTheme());

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

    protected function toggleSidebar(): void
    {
        $displayState = $this->inputFilterUtil->getFilteredPostInput('displayState');
        if (false === \in_array($displayState, ['minimized', 'shown'])) {
            return;
        }

        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->set(self::DISPLAY_STATE_SESSION_KEY, $displayState);
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

        $cmsUser = \TCMSUser::GetActiveUser();
        if (null !== $cmsUser) {
            $parameters['cmsUserId'] = $cmsUser->id;
            $parameters['backendLanguageId'] = $cmsUser->fieldCmsLanguageId;
        }

        return $parameters;
    }
}
