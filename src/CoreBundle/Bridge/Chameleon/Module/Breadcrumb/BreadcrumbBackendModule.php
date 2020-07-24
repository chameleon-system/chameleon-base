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
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use IMapperCacheTriggerRestricted;
use IMapperVisitorRestricted;
use Symfony\Component\Debug\Exception\UndefinedMethodException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

class BreadcrumbBackendModule extends \MTPkgViewRendererAbstractModuleMapper
{
    /**
     * @var MenuItemDataAccessInterface
     */
    private $menuItemDataAccess;

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

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $currentUrl = $request->getRequestUri();
        $urlParameters = $this->urlUtil->getUrlParametersAsArray($currentUrl);
        $currentTableAndEntryId = $this->extractTableAndEntryId($urlParameters);
        $tableConf = $this->getTableConf($currentTableAndEntryId[0] ?? null);

        // NOTE see MTTableManager for potentially missing features (ie special cases for 'field' in url, one record redirect, bOnlyOneRecord, ...)

        $tdbEntry = null;
        if (null !== $tableConf && null !== $currentTableAndEntryId[1]) {
            $tdbEntry = $tableConf->GetTableObjectInstance($currentTableAndEntryId[1]);
        }

        $parentTdb = null;
        if (null !== $tableConf && null !== $tdbEntry) {
            $parentTdb = $this->loadParent($tableConf, $tdbEntry, $urlParameters['sRestrictionField'] ?? null, $urlParameters['sRestriction'] ?? null);
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

            $parentItems = $this->getBreadcrumbItems($parentTableConf->id, $parentEntryUrl, $parentTdb, $menuItemsPointingToTables, $menuItemsByUrl);
            $items = \array_merge($items, $parentItems);
        }

        // NOTE sidebar.js (markSelected, extractTableId) for something similar

        $currentItems = $this->getBreadcrumbItems($currentTableAndEntryId[0] ?? null, $currentUrl, $tdbEntry, $menuItemsPointingToTables, $menuItemsByUrl);
        $items = \array_merge($items, $currentItems);

        $items = $this->removeDuplicatePaths($items);

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
    private function getBreadcrumbItems(?string $tableId, ?string $entryUrl, ?\TCMSRecord $entry, array $menuItemsPointingToTables, array $menuItemsByUrl): array
    {
        // TODO use recursive "breadcrumb handler" approach here?
        //   breadcrumb node: name + url

        $items = [];

        $foundMenuEntry = false;
        if (null !== $tableId) {
            $menuItem = $this->getMatchingEntryFromMenu($tableId, $menuItemsPointingToTables);

            if (null !== $menuItem) {
                $items[] = new BackendBreadcrumbItem('', $menuItem->getMenuCategory()->getName());
                $items[] = new BackendBreadcrumbItem($menuItem->getMenuItem()->getUrl(), $menuItem->getMenuItem()->getName());
                $foundMenuEntry = true;
            }
        }

        if (false === $foundMenuEntry && null !== $entryUrl) {
            foreach ($menuItemsByUrl as $url => $menuItem) {
                if ($url === $entryUrl){
                    $items[] = new BackendBreadcrumbItem('', $menuItem->getMenuCategory()->getName());
                    $items[] = new BackendBreadcrumbItem($menuItem->getMenuItem()->getUrl(), $menuItem->getMenuItem()->getName());
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

    /**
     * @param string[] $urlParameters
     * @return array|null - tuple array of two values
     */
    private function extractTableAndEntryId(array $urlParameters): ?array
    {
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

    private function loadParent(\TdbCmsTblConf $tableConf, \TCMSRecord $tdb, ?string $restrictionField, ?string $restriction): ?\TCMSRecord
    {
        $parentKeyFields = $tableConf->GetFieldDefinitions(['CMSFIELD_PROPERTY_PARENT_ID']);

        if ($parentKeyFields->Length() <= 0) {
            // TODO consider sRestrictionField=cms_tbl_conf_id&sRestriction=75 - for example for a module text
            //   but only if no other parent is found?

            if (null !== $restrictionField && '' !== $restrictionField && null !== $restriction && '' !== $restriction) {
                $parentRestricition = $tdb->GetLookup($restrictionField);

                // TODO however the logic for module configs is complex: you'd additionally need to find the page
                //   from the cms_tpl_module_instance entry - across cms_tpl_page_cms_master_pagedef_spot

                return $parentRestricition;
            }

            return null;
        }

        // TODO this is wrong? /** @var \TCMSFieldLookup $parentKeyField */
        // TODO this usable? $parentKeyField->GetConnectedTableName();

        /** @var \TdbCmsFieldConf $parentKeyField */
        $parentKeyField = $parentKeyFields->Next();

        /** @var \TCMSFieldLookupParentID $fieldObject */
        $fieldObject = $parentKeyField->GetFieldObject();

        // TODO $fieldObject is not loaded here
        // $x = $fieldObject->GetConnectedTableName();

        $lookupName = $parentKeyField->fieldName;
        if ('_id' === \substr($lookupName, -3)) {
            $lookupName = \substr($lookupName, 0, -3);
        }

        $fieldAccessor = 'GetField'.\TCMSTableToClass::ConvertToClassString($lookupName);

        try {
            // NOTE this works - with only the fieldName - as the TAdb does have an accessor method with that name.
            return $tdb->$fieldAccessor();
        } catch (UndefinedMethodException $exception) {
            return null;
        }

        // TODO this is broken for "connectedTableName" but does more than the above: return $tdb->GetLookup($parentKeyField->fieldName);
        //   and this does not work down there (breaks whole system): ' !== $sTargetTable ? $sTargetTable : substr($sFieldName, 0, -3);
    }

    /**
     * @param BackendBreadcrumbItem[] $items
     * @return BackendBreadcrumbItem[]
     */
    private function removeDuplicatePaths(array $items): array
    {
        // NOTE for a product variant without this method the breadcrumb would look something like this
        //   Home / Products & categories / Products / Ocean Jewelry Set (Necklace & Bracelet) / Products & categories / Products / Ocean Jewelry Set (Necklace & Bracelet) - 80 cm, 17 cm

        $itemCount = \count($items);
        if ($itemCount < 2) {
            return $items;
        }

        $shortenedItems = [$items[0]];

        // TODO the same name (when url is empty) might not be sufficient for duplicate detection

        for ($i = 1; $i < $itemCount; $i++) {
            $duplicateFound = false;
            for ($j = $i-1; $j > -1; $j--) {
                if (true === $items[$j]->equals($items[$i])) {
                    $duplicateFound = true;
                    break;
                }
            }

            if (false === $duplicateFound) {
                $shortenedItems[] = $items[$i];
            }
        }

        return $shortenedItems;
    }
}
