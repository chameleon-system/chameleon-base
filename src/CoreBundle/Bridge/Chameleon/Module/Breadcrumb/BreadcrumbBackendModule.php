<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\Breadcrumb;

use ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\Sidebar\MenuItem;
use ChameleonSystem\CoreBundle\DataAccess\MenuItemDataAccessInterface;
use ChameleonSystem\CoreBundle\DataModel\BackendBreadcrumbItem;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use IMapperCacheTriggerRestricted;
use IMapperVisitorRestricted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use TCMSTableToClass;

class BreadcrumbBackendModule extends \MTPkgViewRendererAbstractModuleMapper
{
    /**
     * @var MenuItemDataAccessInterface
     */
    private $menuItemDataAccess;

    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var UrlUtil
     */
    private $urlUtil;

    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        MenuItemDataAccessInterface $menuItemDataAccess,
        RequestStack $requestStack,
        InputFilterUtilInterface $inputFilterUtil,
        UrlUtil $urlUtil,
        LanguageServiceInterface $languageService,
        TranslatorInterface $translator
    ) {
        parent::__construct();

        $this->menuItemDataAccess = $menuItemDataAccess;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->requestStack = $requestStack;
        $this->urlUtil = $urlUtil;
        $this->languageService = $languageService;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(
        IMapperVisitorRestricted $visitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        $visitor->SetMappedValue('pathCmsRoot', PATH_CMS_CONTROLLER);

        // TODO entfernen/deprecaten? \TCMSURLHistory <- \MTTableEditor::AddURLHistory
        //   interessante URL-Parameter: _rmhist, popLastURL, _histid
        // TODO there is some code (for history; #101) in BackendBreadcrumbService for example

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $currentUrl = $request->getRequestUri();
        $currentTableAndEntryId = $this->extractTableAndEntryId($currentUrl);
        $tableConf = $this->getTableConf($currentTableAndEntryId[0] ?? null);

        // TODO MTTableManager considers a field ? $fieldName = $inputFilterUtil->getFilteredInput('field');
        // TODO MTTableManager considers sTableEditorPagdef as pagedef (for one record redirect)
        // TODO MTTableManager considers $parameters['bOnlyOneRecord'] = 'true';

        // TODO errors (log?) on load errors?

        $tdbEntry = null;
        if (null !== $tableConf && null !== $currentTableAndEntryId[1]) {
            $tdbEntry = $this->getTdb($tableConf->fieldName, $currentTableAndEntryId[1]);
        }

        $parentTdb = null;
        if (null !== $tableConf && null !== $tdbEntry) {
            $parentTdb = $this->loadParent($tableConf, $tdbEntry);
        }

        // TODO these two are (conceptually) doubled - and only cached one level lower
        $menuItemsByUrl = $this->getMenuItemsByUrl();
        $menuItemsPointingToTables = $this->getMenuItemsPointingToTable();

        $items = [];

        if (null !== $parentTdb) {
            $parentTableConf = $parentTdb->GetTableConf();

            // TODO see for example MTTableManager::HandleOneRecordTables - is there a "routing" for this?
            $parentEntryUrl = $this->urlUtil->getArrayAsUrl([
                'pagedef' => 'tableeditor',
                'tableid' => $parentTableConf->id,
                'id' => $parentTdb->id,
            ], PATH_CMS_CONTROLLER . '?', '&');

            $parentItems = $this->getBreadcrumbItems($menuItemsPointingToTables, $menuItemsByUrl, $parentTableConf->id, $parentEntryUrl, $parentTdb);
            $items = \array_merge($items, $parentItems);
        }

        // TODO this is heuristic - note sidebar.js (markSelected, extractTableId) for equal code

        $currentItems = $this->getBreadcrumbItems($menuItemsPointingToTables, $menuItemsByUrl, $currentTableAndEntryId[0] ?? null, $currentUrl, $tdbEntry);
        $items = \array_merge($items, $currentItems);

        $visitor->SetMappedValue('items', $items);
    }

    // TODO caching?

    private function getMenuItemsByUrl(): array
    {
        $menuCategories = $this->menuItemDataAccess->getMenuCategories();

        $menuItemUrls = [];

        foreach ($menuCategories as $menuCategory) {
            foreach ($menuCategory->getMenuItems() as $menuItem) {
                $menuItemUrls[$menuItem->getUrl()] = [$menuCategory, $menuItem];
            }
        }

        return $menuItemUrls;
    }

    private function getTableConf(?string $currentTableId): ?\TdbCmsTblConf
    {
        if (null === $currentTableId) {
            return null;
        }

        $tableConf = \TdbCmsTblConf::GetNewInstance();

        if (false === $tableConf->Load($currentTableId)) {
            return null;
        }

        return $tableConf;
    }

    /**
     * @return BackendBreadcrumbItem[]
     */
    private function getBreadcrumbItems(array $menuItemsPointingToTables, array $menuItemsByUrl, ?string $tableId, ?string $entryUrl, ?\TCMSRecord $entry): array
    {
        // TODO odd special case (is also the last "if" down here)
        // TODO this is basically not right (?): should/must list the menu entry (if any) and not the url directly without question - see adding the category below
        if (null !== $entry  && null !== $tableId) {
            $tableConf = \TdbCmsTblConf::GetNewInstance();

            if (true === $tableConf->Load($tableId)) {
                if (true === $tableConf->fieldOnlyOneRecordTbl) {
                    return [new BackendBreadcrumbItem($entryUrl, $this->getNameString($entry->GetName()))];
                }
            }
        }

        $items = [];

        $foundMenuEntry = false;
        if (null !== $tableId) {
            $menuItem = $this->getMatchingEntryFromMenu($tableId, $menuItemsPointingToTables);

            if (null !== $menuItem) {
                $items[] = new BackendBreadcrumbItem('', $menuItem[0]->getName());
                $items[] = new BackendBreadcrumbItem($menuItem[1]->getUrl(), $menuItem[1]->getName());
                $foundMenuEntry = true;
            }
        }

        if (false === $foundMenuEntry && null !== $entryUrl) {
            foreach ($menuItemsByUrl as $url => $menuItem) {
                if ($url === $entryUrl){
                    $items[] = new BackendBreadcrumbItem('', $menuItem[0]->getName());
                    $items[] = new BackendBreadcrumbItem($menuItem[1]->getUrl(), $menuItem[1]->getName());
                    $foundMenuEntry = true;
                    break;
                }
            }
        }

        // TODO similar code should be used to show "menu entry" for a table conf - and remove field "view in category window" (-> there is still a legacy case for it?)

        if (false === $foundMenuEntry && null !== $tableId) {
            $tableConf = \TdbCmsTblConf::GetNewInstance();

            if (true === $tableConf->Load($tableId)) {
                // TODO could use a valid tablemanager url - however there might be no valid view configured for this table (?)
                $items[] = new BackendBreadcrumbItem('', $this->getNameString($tableConf->fieldTranslation));
            }
        }

        if (null !== $entry) {
            $items[] = new BackendBreadcrumbItem($entryUrl, $this->getNameString($entry->GetName()));
        }

        return $items;
    }

    private function getMatchingEntryFromMenu(string $tableId, array $menuItemsPointingToTables): ?array
    {
        if (false === \array_key_exists($tableId, $menuItemsPointingToTables)) {
            return null;
        }

        return $menuItemsPointingToTables[$tableId];
    }

    private function extractTableAndEntryId(string $url): ?array
    {
        if (false === \strpos($url, '?')) {
            return null;
        }

        $urlParameters = $this->urlUtil->getUrlParametersAsArray($url);
        $pagedef = true === \array_key_exists('pagedef', $urlParameters) ? $urlParameters['pagedef'] : null;

        if (null === $pagedef) {
            return null;
        }

        if ($pagedef === 'tablemanager' && true === \array_key_exists('id', $urlParameters)) {
            return [$urlParameters['id'], null];
        }

        if (($pagedef === 'tableeditor' || $pagedef === 'templateengine') && true === \array_key_exists('tableid', $urlParameters)) {
            return [$urlParameters['tableid'], $urlParameters['id'] ?? null];
        }

        return null;
    }

    private function getNameString(string $name): string
    {
        if ('' !== $name) {
            return $name;
        }

        return $this->translator->trans('chameleon_system_core.text.unnamed_record', [], null, $this->languageService->getActiveLocale());
    }

    private function getTdb(string $tableName, string $id): ?\TCMSRecord
    {
        // Does the same as \TCMSTableConf::GetTableObjectInstance() but with more direct error checking.

        $tdbName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $tableName);

        if (false === \class_exists($tdbName)) {
            return null;
        }

        /** @var \TCMSRecord $tdb */
        $tdb = $tdbName::GetNewInstance();

        if (false === $tdb->Load($id)) {
            return null;
        }

        return $tdb;
    }

    private function getMenuItemsPointingToTable(): array
    {
        // TODO / NOTE this loop is basically the same as in getMenuItemUrls()

        $tableMenuItems = [];

        $menuCategories = $this->menuItemDataAccess->getMenuCategories();

        foreach ($menuCategories as $menuCategory) {
            foreach ($menuCategory->getMenuItems() as $menuItem) {
                if (null !== $menuItem->getTableId()) {
                    $tableMenuItems[$menuItem->getTableId()] = [$menuCategory, $menuItem];
                }
            }
        }

        return $tableMenuItems;
    }

    private function loadParent(\TdbCmsTblConf $tableConf, \TCMSRecord $tdb): ?\TCMSRecord
    {
        $parentKeyFields = $tableConf->GetFieldDefinitions(['CMSFIELD_PROPERTY_PARENT_ID']);

        if ($parentKeyFields->Length() <= 0) {
            return null;
        }

        $parentKeyField = $parentKeyFields->Next();

        return $tdb->GetLookup($parentKeyField->fieldName);
    }
}
