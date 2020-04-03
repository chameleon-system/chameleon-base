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
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use IMapperCacheTriggerRestricted;
use IMapperVisitorRestricted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use TCMSTableToClass;

// TODO there is some code (for history; #101) in BackendBreadcrumbService for example

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

        // TODO entfernen/deprecaten? \TCMSURLHistory::AddItem <- \MTTableEditor::AddURLHistory

        $menuItemUrls = $this->getMenuItemUrls();

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

        $items = [];

        if (null !== $parentTdb) {
            $parentTableConf = $parentTdb->GetTableConf();

            // TODO oida! - where is such an url normally generated? (also see below "link to table") - see for example MTTableManager::HandleOneRecordTables
            $parentEntryUrl = PATH_CMS_CONTROLLER . '?' . 'pagedef=tableeditor&tableid='.$parentTableConf->id.'&id='.$parentTdb->id;

            $parentItems = $this->getBreadcrumbItems($menuItemUrls, $parentTableConf->id, $parentEntryUrl, $parentTdb);
            $items = \array_merge($items, $parentItems);
        }

        // TODO this is heuristic - note sidebar.js (markSelected, extractTableId) for equal code
        // TODO tableIdsMatch should/can use the "link to table" information from the menu directly

        $currentItems = $this->getBreadcrumbItems($menuItemUrls, $currentTableId, $currentUrl, $tdb);
        $items = \array_merge($items, $currentItems);

        $visitor->SetMappedValue('pathCmsRoot', PATH_CMS_CONTROLLER);
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
    private function getBreadcrumbItems(array $menuItemUrls, ?string $tableId, ?string $entryUrl, ?\TCMSRecord $entry): array
    {
        $items = [];

        $foundMenuEntry = false;
        foreach ($menuItemUrls as $url => $name) {
            if ((null !== $entryUrl && $url === $entryUrl) || (null !== $tableId && true === $this->tableIdsMatch($url, $tableId))) {
                $items[] = new BackendBreadcrumbItem($url, $name);
                $foundMenuEntry = true;
                break;
            }
        }

        // TODO similar code should be used to show "menu entry" for a table conf - and remove field "view in category window"

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

    private function tableIdsMatch(string $url, string $currentTableId): bool
    {
        $tableId = $this->extractTableId($url);

        return $tableId === $currentTableId;
    }

    private function extractTableId(string $url): ?string
    {
        if (false === \strpos($url, '?')) {
            return null;
        }

        $urlParameters = $this->urlUtil->getUrlParametersAsArray($url);
        $pagedef = true === \array_key_exists('pagedef', $urlParameters) ? $urlParameters['pagedef'] : null;
        // TODO? $pagedef = $this->requestStack->getCurrentRequest()->attributes->get('pagedef');

        if (null === $pagedef) {
            return null;
        }

        if ($pagedef === 'tablemanager' && true === \array_key_exists('id', $urlParameters)) {
            return $urlParameters['id'];
        }

        if (($pagedef === 'tableeditor' || $pagedef === 'templateengine') && true === \array_key_exists('tableid', $urlParameters)) {
            return $urlParameters['tableid'];
        }

        // TODO named constants

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

/*    private function getTdbName(string $fieldName): string
    {
        return 'Tdb' . $this->underscoreToCamelCase($fieldName, true);
    }

    private function underscoreToCamelCase($string, $capitalizeFirstCharacter = false)
    {
        $str = str_replace('_', '', ucwords($string, '_'));

        if (false === $capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }*/

    private function loadParent(\TdbCmsTblConf $tableConf, \TCMSRecord $tdb): ?\TCMSRecord
    {
        // TODO there are also other tables which simply have a "belongs to" - esp Order status codes ?

        /** @var \TdbCmsFieldConfList $parentKeyField */
        $parentKeyFields = $tableConf->GetFieldDefinitions(['CMSFIELD_PROPERTY_PARENT_ID']);

        if ($parentKeyFields->Length() <= 0) {
            return null;
        }

        // TODO also this parent loading from field should be solved somewhere?

        $parentKeyField = $parentKeyFields->Next();

        $parentTableName = $parentKeyField->fieldName;

        // TODO can be different name!
        if ('_id' === \substr($parentTableName, -3)) {
            $parentTableName = \substr($parentTableName, 0, -3);
        }

        $parentFieldName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_PROPERTY, $parentKeyField->fieldName); //'field'.$this->underscoreToCamelCase($parentKeyField->fieldName, true);
        $parentId = $tdb->$parentFieldName;

        return $this->getTdb($parentTableName, $parentId);
    }
}
