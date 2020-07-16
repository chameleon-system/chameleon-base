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
        $currentTableId = $this->extractTableId($currentUrl);
        $tableConf = $this->getTableConf($currentTableId);

        // TODO MTTableManager considers a field ? $fieldName = $inputFilterUtil->getFilteredInput('field');
        // TODO MTTableManager considers sTableEditorPagdef as pagedef (for one record redirect)
        // TODO MTTableManager considers $parameters['bOnlyOneRecord'] = 'true';

        // TODO errors (log?) on load errors?

        $tdb = null;
        if (null !== $tableConf) {
            $id = $this->inputFilterUtil->getFilteredInput('id');

            if (null !== $id) {
                $tdb = $this->getTdb($tableConf->fieldName, $id);
            }
        }

        $parentTdb = null;
        if (null !== $tableConf && null !== $tdb) {
            $parentTdb = $this->loadParent($tableConf, $tdb);
        }

        // TODO these two are (conceptually) doubled - and only cached one level lower
        $menuItemUrls = $this->getMenuItemUrls();
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

            $parentItems = $this->getBreadcrumbItems($menuItemsPointingToTables, $menuItemUrls, $parentTableConf->id, $parentEntryUrl, $parentTdb);
            $items = \array_merge($items, $parentItems);
        }

        // TODO this is heuristic - note sidebar.js (markSelected, extractTableId) for equal code

        $currentItems = $this->getBreadcrumbItems($menuItemsPointingToTables, $menuItemUrls, $currentTableId, $currentUrl, $tdb);
        $items = \array_merge($items, $currentItems);

        $visitor->SetMappedValue('items', $items);
    }

    // TODO caching?

    private function getMenuItemUrls(): array
    {
        $menuCategories = $this->menuItemDataAccess->getMenuCategories();

        $menuItemUrls = [];

        foreach ($menuCategories as $menuCategory) {
            foreach ($menuCategory->getMenuItems() as $menuItem) {
                $menuItemUrls[$menuItem->getUrl()] = $menuItem->getName();
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
    private function getBreadcrumbItems(array $menuItemsPointingToTables, array $menuItemUrls, ?string $tableId, ?string $entryUrl, ?\TCMSRecord $entry): array
    {
        // TODO!! this misses the menu category ?!


        // TODO odd special case (is also the last "if" down here)
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
                $items[] = new BackendBreadcrumbItem($menuItem->getUrl(), $menuItem->getName());
                $foundMenuEntry = true;
            }
        }

        if (false === $foundMenuEntry && null !== $entryUrl) {
            foreach ($menuItemUrls as $url => $name) {
                if ($url === $entryUrl){
                    $items[] = new BackendBreadcrumbItem($url, $name);
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

    private function getMatchingEntryFromMenu(string $tableId, array $menuItemsPointingToTables): ?MenuItem
    {
        if (false === \array_key_exists($tableId, $menuItemsPointingToTables)) {
            return null;
        }

        return $menuItemsPointingToTables[$tableId];
    }

    private function extractTableId(string $url): ?string
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
            return $urlParameters['id'];
        }

        if (($pagedef === 'tableeditor' || $pagedef === 'templateengine') && true === \array_key_exists('tableid', $urlParameters)) {
            return $urlParameters['tableid'];
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
                    $tableMenuItems[$menuItem->getTableId()] = $menuItem;
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
