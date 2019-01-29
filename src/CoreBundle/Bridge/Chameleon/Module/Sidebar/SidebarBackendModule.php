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
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\HttpFoundation\RequestStack;

class SidebarBackendModule extends \MTPkgViewRendererAbstractModuleMapper
{
    private const SIDEBAR_STATE_SESSION_KEY = 'chameleonSidebarBackendModuleState';

    /**
     * @var LanguageServiceInterface
     */
    private $languageService;
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

    public function __construct(LanguageServiceInterface $languageService, UrlUtil $urlUtil, RequestStack $requestStack, InputFilterUtilInterface $inputFilterUtil, ResponseVariableReplacerInterface $responseVariableReplacer)
    {
        parent::__construct();
        $this->languageService = $languageService;
        $this->urlUtil = $urlUtil;
        $this->requestStack = $requestStack;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->responseVariableReplacer = $responseVariableReplacer;
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(\IMapperVisitorRestricted $oVisitor, $bCachingEnabled, \IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $this->setSidebarState();
        $oVisitor->SetMappedValue('sidebarToggleNotificationUrl', $this->getSidebarToggleNotificationUrl());
        $oVisitor->SetMappedValue('menuItems', $this->getMenuItems());
    }

    private function setSidebarState(): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $state = $session->get(self::SIDEBAR_STATE_SESSION_KEY, '');
        if ('minimized' === $state) {
            $value = 'sidebar-minimized';
        } else {
            $value = '';
        }
        $this->responseVariableReplacer->addVariable('sidebarState', $value);
    }

    private function getSidebarToggleNotificationUrl(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $baseUri = $request->getRequestUri();
        if (false === \strpos($baseUri, '?')) {
            $baseUri .= '?';
        } else {
            $baseUri .= '&';
        }

        return $this->urlUtil->getArrayAsUrl([
            'pagedef' => 'tablemanager',
            'module_fnc' => [
                $this->sModuleSpotName => 'ExecuteAjaxCall',
            ],
            '_fnc' => 'toggleSidebar',
        ], $baseUri, '&');
    }

    private function getMenuItems(): array
    {
        $activeLanguageId = $this->languageService->getActiveLanguageId();
        $activeUser = \TCMSUser::GetActiveUser();
        if (null === $activeUser) {
            return [];
        }

        $menuItemsRaw = [];
        $this->addTableMenuItems($menuItemsRaw, $activeUser, $activeLanguageId);
        $this->addModuleMenuItems($menuItemsRaw, $activeUser, $activeLanguageId);

        return $this->mergeMenuItems($menuItemsRaw);
    }

    private function addTableMenuItems(array &$aMenuItemsTemp, \TCMSUser $activeUser, string $activeLanguageId): void
    {
        $query = "SELECT * FROM `cms_tbl_conf` WHERE `cms_content_box_id` <> '' AND `cms_content_box_id` <> '0'";
        $tableList = \TdbCmsTblConfList::GetList($query, $activeLanguageId);
        while ($tableObject = $tableList->Next()) {
            if (false === $this->isTableAccessAllowed($activeUser, $tableObject)) {
                continue;
            }
            if (false === \array_key_exists($tableObject->fieldCmsContentBoxId, $aMenuItemsTemp)) {
                $aMenuItemsTemp[$tableObject->fieldCmsContentBoxId] = [];
            }
            $aMenuItemsTemp[$tableObject->fieldCmsContentBoxId][] = new MenuItem(
                $tableObject->fieldTranslation,
                $tableObject->fieldIconFontCssClass,
                $this->getTableTargetUrl($tableObject->id)
            );
        }
    }

    private function isTableAccessAllowed(\TCMSUser $activeUser, \TdbCmsTblConf $tableObject): bool
    {
        $tableInUserGroup = $activeUser->oAccessManager->user->IsInGroups($tableObject->fieldCmsUsergroupId);
        $isEditAllowed = $activeUser->oAccessManager->HasEditPermission($tableObject->fieldName);
        $isShowAllReadonlyAllowed = $activeUser->oAccessManager->HasShowAllReadOnlyPermission($tableObject->fieldName);

        return (true === $tableInUserGroup && (true === $isEditAllowed || true === $isShowAllReadonlyAllowed));
    }

    private function getTableTargetUrl(string $tableId): string
    {
        return PATH_CMS_CONTROLLER."?pagedef=tablemanager&id=$tableId";
    }

    private function addModuleMenuItems(array &$menuItemsRaw, \TCMSUser $activeUser, string $activeLanguageId): void
    {
        $query = "SELECT * FROM `cms_module` WHERE `active` = '1'";
        $cmsModuleList = \TdbCmsModuleList::GetList($query, $activeLanguageId);
        while ($cmsModule = $cmsModuleList->Next()) {
            if (false === $this->isModuleAccessAllowed($activeUser, $cmsModule)) {
                continue;
            }
            if (false === \array_key_exists($cmsModule->fieldCmsContentBoxId, $menuItemsRaw)) {
                $menuItemsRaw[$cmsModule->fieldCmsContentBoxId] = [];
            }
            $menuItemsRaw[$cmsModule->fieldCmsContentBoxId][] = new MenuItem(
                $cmsModule->fieldName,
                $cmsModule->fieldIconFontCssClass,
                $this->getModuleTargetUrl($cmsModule)
            );
        }
    }

    private function isModuleAccessAllowed(\TCMSUser $activeUser, \TdbCmsModule $cmsModule): bool
    {
        return true === $activeUser->oAccessManager->user->IsInGroups($cmsModule->fieldCmsUsergroupId);
    }

    private function getModuleTargetUrl(\TdbCmsModule $cmsModule): string
    {
        $url = PATH_CMS_CONTROLLER.'?pagedef='.$cmsModule->fieldModule;
        if ('' !== $cmsModule->fieldParameter) {
            $url .= '&'.$cmsModule->fieldParameter;
        }
        if ('' !== $cmsModule->fieldModuleLocation) {
            $url .= '&_pagedefType='.$cmsModule->fieldModuleLocation;
        }

        return $url;
    }

    private function mergeMenuItems(array $menuItemsRaw): array
    {
        $categoryNames = $this->getCategoryNames();
        $menuItems = [];
        foreach ($menuItemsRaw as $categoryId => $items) {
            if (true === \array_key_exists($categoryId, $categoryNames)) {
                $categoryName = $categoryNames[$categoryId];
            } else {
                $categoryName = '-';
            }
            \usort($items, function (MenuItem $menuItem1, MenuItem $menuItem2) {
                return \strcmp($menuItem1->getName(), $menuItem2->getName());
            });
            $menuItems[] = new MenuCategory($categoryName, $items);
        }
        \usort($menuItems, function (MenuCategory $menuCategory1, MenuCategory $menuCategory2) {
            return \strcmp($menuCategory1->getName(), $menuCategory2->getName());
        });

        return $menuItems;
    }

    private function getCategoryNames(): array
    {
        $contentBoxList = \TdbCmsContentBoxList::GetList('SELECT * FROM `cms_content_box`');
        $names = [];
        while ($contentBox = $contentBoxList->Next()) {
            $names[$contentBox->id] = $contentBox->fieldName;
        }

        return $names;
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
    }

    public function toggleSidebar(): void
    {
        $state = $this->inputFilterUtil->getFilteredPostInput('state');
        if (false === \in_array($state, ['minimized', 'shown'])) {
            return;
        }

        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->set(self::SIDEBAR_STATE_SESSION_KEY, $state);
    }
}
