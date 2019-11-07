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
    private const ACTIVE_CATEGORY_SESSION_KEY = 'chameleonSidebarBackendModuleActiveCategory';
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

    public function Init()
    {
        parent::Init();
        $this->restoreDisplayState();
    }

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
        $this->responseVariableReplacer->addVariable('sidebarActiveCategory', \TGlobal::OutHTML($this->getActiveCategory()));
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(\IMapperVisitorRestricted $visitor, $cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        $visitor->SetMappedValue('sidebarToggleNotificationUrl', $this->getSidebarToggleNotificationUrl());
        $visitor->SetMappedValue('sidebarSaveActiveCategoryNotificationUrl', $this->getActiveCategoryNotificationUrl());
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

    private function getActiveCategoryNotificationUrl(): string
    {
        return $this->urlUtil->getArrayAsUrl([
            'module_fnc' => [
                $this->sModuleSpotName => 'ExecuteAjaxCall',
            ],
            '_fnc' => 'saveActiveCategory',
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

    private function getActiveCategory(): ?string
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();

        return $session->get(self::ACTIVE_CATEGORY_SESSION_KEY);
    }

    protected function saveActiveCategory(): void
    {
        $activeCategory = $this->inputFilterUtil->getFilteredPostInput('categoryId');
        if ('' === $activeCategory) {
            $activeCategory = null;
        }

        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->set(self::ACTIVE_CATEGORY_SESSION_KEY, $activeCategory);
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

    /**
     * {@inheritdoc}
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'toggleSidebar';
        $this->methodCallAllowed[] = 'saveActiveCategory';
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
