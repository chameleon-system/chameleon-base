<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Controller\ChameleonControllerInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

/**
 * Handles global lists that are edited via the main menu (not connected to a module - hence global).
 * In order to use it you will need to overwrite at least GetTableName and SetDetailpageTemplate.
 */
class MTGlobalListCore extends TUserCustomModelBase
{
    /**
     * all items in the list.
     *
     * @var TCMSRecordList
     */
    protected $oItemList;

    /**
     * active item.
     *
     * @var TCMSRecord
     */
    protected $oItem;

    /**
     * current page count.
     *
     * @var int
     */
    protected $iPage = 0;

    /**
     * holds the "active page" or "page" object.
     *
     * @var TCMSPage
     */
    protected $oPage;

    /**
     * active language ID.
     *
     * @var int
     */
    protected $languageID;

    /**
     * URL parameter to set current page.
     *
     * @var string
     */
    protected $ipageURLParam = 'ipage';

    protected $bAllowHTMLDivWrapping = true;

    public function Init()
    {
        parent::Init();
        $this->LoadPageObject();

        if ($this->global->UserDataExists($this->ipageURLParam)) {
            $this->iPage = $this->global->GetUserData($this->ipageURLParam);
        } else {
            $this->iPage = 0;
        }
        if ($this->global->UserDataExists('itemid')) {
            $this->LoadItem();
            if ($this->AllowItemView()) {
                // find breadcrumb module...
                $this->AddActiveItemToBreadcrumb();
            } else {
                $this->LoadItemList();
            }
        } else {
            $this->LoadItemList();
        }
    }

    /**
     * called when an itemid is passed to load the item.
     */
    protected function LoadItem()
    {
        $this->oItem = $this->CreateItemObject();
        $this->oItem->table = $this->GetTableName();
        $this->oItem->SetLanguage($this->languageID);
        $this->oItem->Load($this->global->GetUserData('itemid'));
    }

    protected function LoadPageObject()
    {
        // load active page or page given by ID via GET parameter
        if ($this->global->UserDataExists('mode') && $this->global->UserDataExists('pageID') && $this->global->UserDataExists('langID')) {
            // load active language
            $this->languageID = $this->global->GetUserData('langID');

            $this->oPage = new TdbCmsTplPage();
            $this->oPage->SetLanguage($this->languageID);
            $this->oPage->Load($this->global->GetUserData('pageID'));
        } else {
            $this->oPage = $this->getActivePageService()->getActivePage();
            // load active language
            $this->languageID = $this->getLanguageService()->getActiveLanguageId();
        }
    }

    protected function AllowItemView()
    {
        return $this->oItem && false !== $this->oItem->sqlData;
    }

    public function Execute()
    {
        $this->data = parent::Execute();

        $this->data['iPage'] = $this->iPage;
        if ($this->global->UserDataExists('itemid') && $this->AllowItemView()) {
            $this->data['oItem'] = $this->oItem;
            $this->SetDetailpageTemplate();
        } else {
            $this->data['oItemList'] = $this->oItemList;
            // check for RSS Mode
            if ($this->global->UserDataExists('mode') && $this->global->UserDataExists('pageID') && $this->global->UserDataExists('langID')) {
                $this->LoadRSSData();
            } else {
                $this->GetListNavigation();
            }
        }

        return $this->data;
    }

    protected function LoadRSSData()
    {
        $this->data['oPortal'] = $this->oPage->GetPortal();
        $this->data['pageURL'] = $this->data['oPortal']->GetPrimaryDomain().'/'.$this->getPageService()->getLinkToPageObjectRelative($this->oPage);
        $this->data['oPage'] = $this->oPage;
    }

    /**
     * creates an array in $this->aPaging containing iPage (0 = first page) as key and page Link URL as val
     * This helps you building previous/next page links in your view as well as direct-via-page navigations.
     */
    protected function GetListNavigation()
    {
        $sNextPageLink = '';
        $sPageURL = $this->getPageService()->getLinkToPageObjectRelative($this->oPage);
        if ($this->GetPageSize() > 0 && (($this->iPage + 1) * $this->GetPageSize() < $this->oItemList->Length() - 1)) {
            $sNextPageLink = $sPageURL.'?'.$this->ipageURLParam.'='.($this->iPage + 1);
        }
        $sPreviousPageLink = '';
        if ($this->iPage > 0) {
            $sPreviousPageLink = $sPageURL.'?'.$this->ipageURLParam.'='.($this->iPage - 1);
        }

        $this->data['sNextPageLink'] = $sNextPageLink;
        $this->data['sPreviousPageLink'] = $sPreviousPageLink;

        $this->aPaging = [];
        $iNumberOfPages = 0;
        if (!is_null($this->oItemList)) {
            $iNumberOfPages = ceil($this->oItemList->Length() / $this->GetPageSize());
        }
        $activePageService = $this->getActivePageService();
        for ($iPage = 0; $iPage < $iNumberOfPages; ++$iPage) {
            $this->aPaging[$iPage] = $activePageService->getLinkToActivePageRelative([$this->ipageURLParam => $iPage]);
        }
    }

    /**
     * returns the breadcrumb link info.
     *
     * @return array
     */
    protected function GetBreadcrumbDetailLink()
    {
        $aLink = false;
        if (!is_null($this->oItem)) {
            $aLink = ['link' => $this->oItem->sDetailLink, 'name' => $this->oItem->GetName()];
        }

        return $aLink;
    }

    /**
     * added the link and title of the current list item to the breadcrumb module instance
     * we assume that the instance runs under the name "breadcrumb". if that is not the
     * case, then you will need to overwrite this function.
     */
    protected function AddActiveItemToBreadcrumb(): void
    {
        /** @var TModuleLoader $moduleLoader */
        $moduleLoader = $this->getController()->getModuleLoader();
        if (array_key_exists('breadcrumb', $moduleLoader->modules)) {
            $moduleLoader->modules['breadcrumb']->aAdditionalBreadcrumbNodes[] = $this->GetBreadcrumbDetailLink();
        }
        if (array_key_exists('metadata', $moduleLoader->modules)) {
            $moduleLoader->modules['metadata']->aAdditionalBreadcrumbNodes[] = $this->GetBreadcrumbDetailLink();
        }
    }

    /**
     * return name of the table in the database that holds the list
     * you MUST overwrite this function to supply the correct table name.
     *
     * @return string
     */
    protected function GetTableName()
    {
        return 'sometable';
    }

    /**
     * returns an instance of the class used to hold one Item in the List
     * overwrite the function if you want to use a custom class for this.
     *
     * @return TCMSRecord
     */
    protected function CreateItemObject()
    {
        $sClassName = $this->GetItemObjectConfig();

        return new $sClassName();
    }

    /**
     * overwrite this to supply the correct module name and the template
     * to use when displaying one item from the list. you MUST overwrite this
     * template.
     */
    protected function SetDetailpageTemplate()
    {
        $this->SetTemplate('MTGlobalListCore', 'includes/item');
    }

    /**
     * defines what class to use for each item.
     *
     * @return array
     */
    protected function GetItemObjectConfig()
    {
        return 'MTGlobalListItem';
    }

    /**
     * loads the list. overwrite this to change the query, and to set the
     * correct item handler.
     */
    protected function LoadItemList()
    {
        $this->oItemList = new TCMSRecordList();
        $this->oItemList->sTableName = $this->GetTableName();
        $this->oItemList->SetLanguage($this->languageID);

        $this->oItemList->sTableObject = $this->GetItemObjectConfig();

        $pageSize = $this->GetPageSize();
        $startRecord = $this->iPage * $pageSize;
        $itemQuery = $this->GetItemListQuery();
        $this->oItemList->Load($itemQuery);
        $this->oItemList->SetPagingInfo($startRecord, $pageSize);
    }

    /**
     * config the item list (what object to use for each item, and what query to use.
     *
     * @return string - the list query
     */
    protected function GetItemListQuery()
    {
        return 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->GetTableName()).'` ORDER BY '.$this->GetOrderBy();
    }

    /**
     * sets the ORDER BY query part.
     */
    protected function GetOrderBy()
    {
        $orderBy = ' `position`, `datum` DESC, `name` ';

        return $orderBy;
    }

    /**
     * return true if the list should show an rss feed, else return false.
     *
     * @return bool
     */
    protected function HasRSSView()
    {
        return false;
    }

    /**
     * header includes.
     *
     * @return array
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = [];
        $aIncludes[] = '<link href="'.TGlobal::GetStaticURLToWebLib().'/web_modules/MTGlobalListCore/css/MTGlobalListCore.css" rel="stylesheet" type="text/css" />';

        // load optional custm css from /chameleon/web_modules/ directory
        if (file_exists(PATH_USER_CMS_PUBLIC.'/web_modules/MTGlobalListCore/css/MTGlobalListCore.css')) {
            $aIncludes[] = '<link href="'.TGlobal::GetStaticURL('/chameleon/web_modules/MTGlobalListCore/css/MTGlobalListCore.css').'" rel="stylesheet" type="text/css" media="screen" />';
        }

        if ($this->HasRSSView()) {
            if (!$this->global->UserDataExists('mode') || 'rss' != $this->global->GetUserData('mode')) {
                $url = URL_WEB_CONTROLLER.'?pagedef='.$this->GetRSSPagedefName().'&amp;mode=rss&amp;pageID='.$this->oPage->sqlData['id'].'&amp;langID='.$this->languageID.'&amp;instID='.$this->instanceID.'&amp;'.$this->ipageURLParam.'='.$this->iPage;
                if ($this->global->UserDataExists('itemid')) {
                    $url .= '&amp;itemid='.$this->global->GetUserData('itemid');
                }
                $aIncludes[] = '<link rel="alternate" type="application/rss+xml" title="RSS" href="'.$url.'" />';
            }
        }

        return $aIncludes;
    }

    protected function GetRSSPagedefName()
    {
        return get_class($this).'RSS2xml';
    }

    /**
     * returns the pagesize. if set to -1, then all records will be shown.
     *
     * @return int
     */
    protected function GetPageSize()
    {
        return -1;
    }

    /**
     * if this function returns true, then the result of the execute function will be cached.
     *
     * @return bool
     */
    public function _AllowCache()
    {
        return true;
    }

    /**
     * return an assoc array of parameters that describe the state of the module.
     *
     * @return array
     */
    public function _GetCacheParameters()
    {
        $parameters = parent::_GetCacheParameters();
        $parameters['itemid'] = $this->global->GetUserData('itemid');
        $parameters[$this->ipageURLParam] = $this->iPage;

        // add rss specific cache paramaters
        if ('rss' == $this->global->GetUserData('mode')) {
            $parameters['pageid'] = $this->global->GetUserData('pageID');
        }

        return $parameters;
    }

    /**
     * if the content that is to be cached comes from the database (as ist most often the case)
     * then this function should return an array of assoc arrays that point to the
     * tables and records that are associated with the content. one table entry has
     * two fields:
     *   - table - the name of the table
     *   - id    - the record in question. if this is empty, then any record change in that
     *             table will result in a cache clear.
     *
     * @return array
     */
    public function _GetCacheTableInfos()
    {
        return [['table' => $this->GetTableName(), 'id' => '']];
    }

    private function getActivePageService(): ActivePageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    private function getLanguageService(): LanguageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.language_service');
    }

    private function getPageService(): PageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.page_service');
    }

    private function getController(): ChameleonControllerInterface
    {
        return ServiceLocator::get('chameleon_system_core.chameleon_controller');
    }
}
