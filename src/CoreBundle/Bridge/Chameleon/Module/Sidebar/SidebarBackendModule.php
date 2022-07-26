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

use ChameleonSystem\CoreBundle\DataAccess\UserMenuItemDataAccessInterface;
use ChameleonSystem\CoreBundle\Response\ResponseVariableReplacerInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SidebarBackendModule extends \MTPkgViewRendererAbstractModuleMapper
{
    private const OPEN_CATEGORIES_SESSION_KEY = 'chameleonSidebarBackendModuleOpenCategories';
    private const DISPLAY_STATE_SESSION_KEY = 'chameleonSidebarBackendModuleDisplayState';
    private const POPULAR_CATEGORY_ID = '0000000-0000-0001-0000-000000000001';

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

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UserMenuItemDataAccessInterface
     */
    private $userMenuItemDataAccess;

    public function __construct(
        UrlUtil $urlUtil,
        RequestStack $requestStack,
        InputFilterUtilInterface $inputFilterUtil,
        ResponseVariableReplacerInterface $responseVariableReplacer,
        MenuItemFactoryInterface $menuItemFactory,
        TranslatorInterface $translator,
        UserMenuItemDataAccessInterface $userMenuItemDataAccess
    ) {
        parent::__construct();

        $this->urlUtil = $urlUtil;
        $this->requestStack = $requestStack;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->responseVariableReplacer = $responseVariableReplacer;
        $this->menuItemFactory = $menuItemFactory;
        $this->translator = $translator;
        $this->userMenuItemDataAccess = $userMenuItemDataAccess;
    }

    /**
     * {@inheritdoc}
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();

        $this->methodCallAllowed[] = 'toggleCategoryOpenState';
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
        $this->responseVariableReplacer->addVariable('sidebarOpenCategoryIds', \TGlobal::OutHTML(implode(',', $this->getOpenCategories())));
    }

    private function getSession(): ?SessionInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }

        return $request->getSession();
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(\IMapperVisitorRestricted $visitor, $cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        $visitor->SetMappedValue('sidebarToggleCategoryNotificationUrl', $this->getNotificationUrl('toggleCategoryOpenState'));
        $visitor->SetMappedValue('sidebarElementClickNotificationUrl', $this->getNotificationUrl('reportElementClick'));
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
        $activeUser = \TCMSUser::GetActiveUser();
        if (null === $activeUser) {
            return [];
        }

        $menuCategories = [];
        $menuItemMap = [];

        $tdbCategoryList = \TdbCmsMenuCategoryList::GetList();
        $tdbCategoryList->ChangeOrderBy([
            '`cms_menu_category`.`position`' => 'ASC',
        ]);
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

                    $menuItemMap[$menuItem->getId()] = $menuItem;
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

        $popularCategory = $this->getPopularMenuEntries($menuItemMap);
        if (null !== $popularCategory) {
            \array_unshift($menuCategories, $popularCategory);
        }

        return $menuCategories;
    }

    private function getOpenCategories(): array
    {
        $session = $this->getSession();

        if (null === $session) {
            return [];
        }

        return $session->get(self::OPEN_CATEGORIES_SESSION_KEY, []);
    }

    private function getPopularMenuEntries(array $menuItemMap): ?MenuCategory
    {
        $activeUser = \TCMSUser::GetActiveUser();
        if (null === $activeUser) {
            return null;
        }

        $menuItemsClickedByUser = $this->userMenuItemDataAccess->getMenuItemIds($activeUser->id);
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

    /**
     * @deprecated since 6.3.8 - use toggleCategoryOpenState
     */
    protected function saveActiveCategory(): void
    {
        $this->toggleCategoryOpenState();
    }

    protected function toggleCategoryOpenState(): void
    {
        $session = $this->getSession();
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

    protected function reportElementClick(): void
    {
        $menuId = $this->inputFilterUtil->getFilteredPostInput('clickedMenuId', '');

        if ('' === $menuId) {
            return;
        }

        $activeUser = \TCMSUser::GetActiveUser();
        if (null === $activeUser) {
            return;
        }

        $this->userMenuItemDataAccess->trackMenuItem($activeUser->id, $menuId);
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

            $session = $this->getSession();
            if (null !== $session) {
                $parameters['sessionId'] = $session->getId();
            }
        }

        return $parameters;
    }
}
