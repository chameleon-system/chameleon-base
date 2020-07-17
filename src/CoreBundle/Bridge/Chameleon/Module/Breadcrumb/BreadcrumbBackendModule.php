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

use ChameleonSystem\CoreBundle\DataAccess\MenuItemDataAccessInterface;
use ChameleonSystem\CoreBundle\DataModel\BackendBreadcrumbItem;
use ChameleonSystem\CoreBundle\DataModel\MenuCategoryAndItem;
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
        UrlUtil $urlUtil,
        LanguageServiceInterface $languageService,
        TranslatorInterface $translator
    ) {
        parent::__construct();

        $this->menuItemDataAccess = $menuItemDataAccess;
        $this->requestStack = $requestStack;
        $this->urlUtil = $urlUtil;
        $this->languageService = $languageService;
        $this->translator = $translator;
    }

    // NOTE caching is difficult here because cache delete triggers would need to include virtually every table
    //   as any entry's name might end up in the breadcrumb.

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

        $tdbEntry = null;
        if (null !== $tableConf && null !== $currentTableAndEntryId[1]) {
            $tdbEntry = $this->getTdb($tableConf->fieldName, $currentTableAndEntryId[1]);
        }

        $parentTdb = null;
        if (null !== $tableConf && null !== $tdbEntry) {
            $parentTdb = $this->loadParent($tableConf, $tdbEntry);

            // TODO (at least odd/long) for a product variant this loads the variant parent and displays something like
            //   Home / Products & categories / Products / Ocean Jewelry Set (Necklace & Bracelet) / Products & categories / Products / Ocean Jewelry Set (Necklace & Bracelet) - 80 cm, 17 cm
        }

        $menuItemsByUrl = $this->getMenuItemsByUrl();
        $menuItemsPointingToTables = $this->menuItemDataAccess->getMenuItemsPointingToTable();

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

        // NOTE sidebar.js (markSelected, extractTableId) for something similar

        $currentItems = $this->getBreadcrumbItems($menuItemsPointingToTables, $menuItemsByUrl, $currentTableAndEntryId[0] ?? null, $currentUrl, $tdbEntry);
        $items = \array_merge($items, $currentItems);

        $visitor->SetMappedValue('items', $items);
    }

    private function getMenuItemsByUrl(): array
    {
        $menuCategories = $this->menuItemDataAccess->getMenuCategories();

        $menuItemUrls = [];

        foreach ($menuCategories as $menuCategory) {
            foreach ($menuCategory->getMenuItems() as $menuItem) {
                $menuItemUrls[$menuItem->getUrl()] = new MenuCategoryAndItem($menuCategory, $menuItem);
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
        $isSingleTableEntry = false;

        if (null !== $entry) {
            $isSingleTableEntry = $entry->GetTableConf()->fieldOnlyOneRecordTbl;
        }

        $items = [];

        $foundMenuEntry = false;
        if (null !== $tableId) {
            $menuItem = $this->getMatchingEntryFromMenu($tableId, $menuItemsPointingToTables);

            if (null !== $menuItem) {
                $items[] = new BackendBreadcrumbItem('', $menuItem->getMenuCategory()->getName());
                if (false === $isSingleTableEntry) {
                    $items[] = new BackendBreadcrumbItem(
                        $menuItem->getMenuItem()->getUrl(),
                        $menuItem->getMenuItem()->getName()
                    );
                }
                $foundMenuEntry = true;
            }
        }

        if (false === $foundMenuEntry && null !== $entryUrl) {
            foreach ($menuItemsByUrl as $url => $menuItem) {
                if ($url === $entryUrl){
                    $items[] = new BackendBreadcrumbItem('', $menuItem->getMenuCategory()->getName());
                    if (false === $isSingleTableEntry) {
                        $items[] = new BackendBreadcrumbItem(
                            $menuItem->getMenuItem()->getUrl(),
                            $menuItem->getMenuItem()->getName()
                        );
                    }
                    $foundMenuEntry = true;

                    break;
                }
            }
        }

        if (false === $foundMenuEntry && null !== $tableId) {
            // Simply show the table name if there is no menu entry

            $tableConf = \TdbCmsTblConf::GetNewInstance();

            // NOTE this also (always?) exist as $entry->GetTableConf().

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

    private function getMatchingEntryFromMenu(string $tableId, array $menuItemsPointingToTables): ?MenuCategoryAndItem
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

    private function loadParent(\TdbCmsTblConf $tableConf, \TCMSRecord $tdb): ?\TCMSRecord
    {
        $parentKeyFields = $tableConf->GetFieldDefinitions(['CMSFIELD_PROPERTY_PARENT_ID']);

        if ($parentKeyFields->Length() <= 0) {
            return null;
        }

        // TODO this is wrong? /** @var \TCMSFieldLookup $parentKeyField */
        // TODO this usable? $parentKeyField->GetConnectedTableName();

        $parentKeyField = $parentKeyFields->Next();

        $lookupName = $parentKeyField->fieldName;
        if ('_id' === \substr($lookupName, -3)) {
            $lookupName = \substr($lookupName, 0, -3);
        }

        $fieldAccessor = 'GetField'.\TCMSTableToClass::ConvertToClassString($lookupName);

        return $tdb->$fieldAccessor();

        // TODO this is broken for "connectedTableName" but does more than the above: return $tdb->GetLookup($parentKeyField->fieldName);
        //   and this does not work down there (breaks whole system): ' !== $sTargetTable ? $sTargetTable : substr($sFieldName, 0, -3);
    }
}
