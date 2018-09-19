<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

/**
 * NOTE: requires sessions to work!
/**/
class THTMLFileBrowser
{
    const SESSION_PARAM_NAME = 'THTMLFileBrowser';
    public $iNumberOfRecsPerPage = 10;
    public $iCurrentPage = 0;
    public $sFilter = '';
    public $sPath = '';
    public $sListId = '';
    public $sControllingModuleSpotName = null;

    protected $oDirObject = null;
    public $iCurrentRecord = 0;
    public $iNumberOfRecords = null;
    protected $aActions = array();

    /**
     * a custom method to filter the list.
     *
     * @var string
     */
    protected $sCustomFilterCallback = null;

    public function __sleep()
    {
        return array('iNumberOfRecsPerPage', 'iCurrentPage', 'sFilter', 'sPath', 'sListId', 'sCustomFilterCallback');
    }

    /**
     * return instance for a path.
     *
     * @param string $sPath
     * @param string $sControllingModuleSpotName
     *
     * @return THTMLFileBrowser
     */
    public static function &GetInstance($sPath, $sCustomFilterCallback = null, $sControllingModuleSpotName = null)
    {
        static $aObjectList = array();
        $sSessionName = self::GetSessionName($sPath, $sCustomFilterCallback);

        if (!array_key_exists($sSessionName, $aObjectList)) {
            if (array_key_exists($sSessionName, $_SESSION)) {
                $aObjectList[$sSessionName] = unserialize($_SESSION[$sSessionName]);
            } else {
                $aObjectList[$sSessionName] = new self();
                $aObjectList[$sSessionName]->sPath = $sPath;
                $aObjectList[$sSessionName]->sListId = $sSessionName;
                $aObjectList[$sSessionName]->SetCustomFilterCallback($sCustomFilterCallback);
            }
            self::GetHtmlHeadIncludes();
        }

        $aObjectList[$sSessionName]->OpenDirectory();
        if (is_null($sControllingModuleSpotName)) {
            // try to fetch from controller
            $oGlobal = TGlobal::instance();
            $oModule = &$oGlobal->GetExecutingModulePointer();
            if (!is_null($oModule)) {
                $sControllingModuleSpotName = $oModule->sModuleSpotName;
            }
        }
        $aObjectList[$sSessionName]->sControllingModuleSpotName = $sControllingModuleSpotName;
        $aObjectList[$sSessionName]->iCurrentRecord = $aObjectList[$sSessionName]->iCurrentPage * $aObjectList[$sSessionName]->iNumberOfRecsPerPage;

        // catch any commands/state changes
        $aObjectList[$sSessionName]->HandleRequests();

        return $aObjectList[$sSessionName];
    }

    /**
     * set a custom filter callback function. the method will be passed a file object (TCMSFile)
     * and should return true or false.
     *
     * @param string $sCustomFilterCallback
     */
    public function SetCustomFilterCallback($sCustomFilterCallback)
    {
        $this->sCustomFilterCallback = $sCustomFilterCallback;
    }

    public function HandleRequests()
    {
        $oGlobal = TGlobal::instance();
        $sBaseParam = self::GetSessionName($this->sPath, $this->sCustomFilterCallback);

        $sPageParam = 'sPage'.$sBaseParam;
        if ($oGlobal->UserDataExists($sPageParam)) {
            $iPage = $oGlobal->GetUserData($sPageParam);
            $this->ChangePage($iPage);
        }

        $sPageSizeParam = 'iNumberOfRecsPerPage'.$sBaseParam;
        if ($oGlobal->UserDataExists($sPageSizeParam)) {
            $this->iNumberOfRecsPerPage = $oGlobal->GetUserData($sPageSizeParam);
            if ($this->iNumberOfRecsPerPage > 0) {
                $this->iCurrentPage = ceil($this->iCurrentRecord / $this->iNumberOfRecsPerPage) - 1;
            } else {
                $this->iCurrentPage = 0;
                $this->iCurrentRecord = 0;
            }
            if ($this->iCurrentPage < 0) {
                $this->iCurrentPage = 0;
            }
            $this->iCurrentRecord = $this->iCurrentPage * $this->iNumberOfRecsPerPage;
        }

        $sFilterParam = 'sFilter'.$sBaseParam;
        if ($oGlobal->UserDataExists($sFilterParam)) {
            $this->sFilter = $oGlobal->GetUserData($sFilterParam);
            $this->iNumberOfRecords = null;
            $this->GoToStart();
        }
    }

    public function ChangePage($iPage)
    {
        if ($this->iNumberOfRecsPerPage > 0 && $iPage > -1 && $iPage <= ceil($this->Length() / $this->iNumberOfRecsPerPage)) {
            $this->iCurrentPage = $iPage;
            $iRec = $this->iCurrentPage * $this->iNumberOfRecsPerPage;
            $this->MoveToRecord($iRec);
        }
    }

    /**
     * add an action to the list.
     *
     * @param string $sMethod      - method to call on the modul holding the list
     * @param string $sDisplayName - display name of the method
     */
    public function AddAction($sMethod, $sDisplayName)
    {
        $this->aActions[$sMethod] = $sDisplayName;
    }

    /**
     * return link to next page (if there is one).
     *
     * @return string
     */
    public function GetNextPageURL()
    {
        $sLink = '';
        if ($this->iCurrentPage < (ceil($this->Length() / $this->iNumberOfRecsPerPage) - 1)) {
            $sLink = $this->getActivePageService()->getLinkToActivePageRelative(array(
                'sPage'.self::GetSessionName($this->sPath, $this->sCustomFilterCallback) => $this->iCurrentPage + 1,
            ));
        }

        return $sLink;
    }

    /**
     * return link to next page (if there is one).
     *
     * @return string
     */
    public function GetPreviousPageURL()
    {
        $sLink = '';
        if ($this->iCurrentPage > 0) {
            $sLink = $this->getActivePageService()->getLinkToActivePageRelative(array(
                'sPage'.self::GetSessionName($this->sPath, $this->sCustomFilterCallback) => $this->iCurrentPage - 1,
            ));
        }

        return $sLink;
    }

    /**
     * get link for page #.
     *
     * @param int $iPage
     *
     * @return string
     */
    public function GetPageURL($iPage)
    {
        return $this->getActivePageService()->getLinkToActivePageRelative(array(
            'sPage'.self::GetSessionName($this->sPath, $this->sCustomFilterCallback) => $iPage,
        ));
    }

    /**
     * go to the next record in the dir... return false if none are found.
     *
     * @return TCMSFile
     */
    public function Next()
    {
        $oEntry = false;
        if (-1 == $this->iNumberOfRecsPerPage || ($this->iCurrentRecord < ($this->iCurrentPage + 1) * $this->iNumberOfRecsPerPage)) {
            $oEntry = $this->ReadNext();
        }

        return $oEntry;
    }

    /**
     * internal read - ignores page restrictions but respects filter.
     *
     * @return string
     */
    protected function ReadNext()
    {
        $oEntry = false;

        do {
            $oEntry = false;
            $bSkip = false;
            $sEntry = $this->oDirObject->read();
            //echo $sEntry."<br>\n";
            if (false !== $sEntry) {
                $bSkip = ('.' == $sEntry || '..' == $sEntry || is_dir($sEntry));
                if (!$bSkip && !empty($this->sFilter) && false === strpos($sEntry, $this->sFilter)) {
                    $bSkip = true;
                }
                if (false !== $sEntry && !$bSkip) {
                    $oEntry = TCMSFile::GetInstance($this->oDirObject->path.'/'.$sEntry);
                    if (!is_null($this->sCustomFilterCallback) && false !== $oEntry) {
                        $sTmpFNC = $this->sCustomFilterCallback;
                        $bSkip = !$sTmpFNC($oEntry);
                    }
                }

                if (!$bSkip && false !== $oEntry) {
                    ++$this->iCurrentRecord;
                    $bSkip = false;
                }
                // elseif ($bSkip) $oEntry = $this->ReadNext();
            }
        } while ($bSkip);

        return $oEntry;
    }

    public function MoveToRecord($iRec)
    {
        $this->GoToStart();

        while (($this->iCurrentRecord < $iRec) && $this->ReadNext()) {
        }
    }

    protected function GoToStart()
    {
        $this->oDirObject->rewind();
        $this->iCurrentRecord = 0;
    }

    /**
     * get number of records in list.
     *
     * @return int
     */
    public function Length()
    {
        if (is_null($this->iNumberOfRecords)) {
            $iRec = $this->iCurrentRecord;

            $this->GoToStart();
            $this->iNumberOfRecords = 0;
            while ($oItem = $this->ReadNext()) {
                ++$this->iNumberOfRecords;
            }

            $this->MoveToRecord($iRec);
        }

        return $this->iNumberOfRecords;
    }

    /**
     * open the directory pointer.
     */
    public function OpenDirectory()
    {
        $this->oDirObject = dir($this->sPath);
        $this->MoveToRecord($this->iCurrentRecord);
    }

    public function __destruct()
    {
        if (!is_null($this->oDirObject)) {
            $this->oDirObject->close();
            $this->oDirObject = null;
        }
        $_SESSION[self::GetSessionName($this->sPath, $this->sCustomFilterCallback)] = serialize($this);
    }

    /**
     * session name.
     *
     * @return string
     */
    protected static function GetSessionName($sPath, $sCustomFilterCallback)
    {
        return self::SESSION_PARAM_NAME.md5($sPath.$sCustomFilterCallback);
    }

    public static function GetMessageManagerName($sPath)
    {
        return self::GetSessionName($sPath, '');
    }

    protected function GetViewPath()
    {
        return 'THTMLFileBrowser';
    }

    /**
     * used to display an article.
     *
     * @param string $sViewName     - the view to use
     * @param string $sViewType     - where the view is located (Core, Custom-Core, Customer)
     * @param array  $aCallTimeVars - place any custom vars that you want to pass through the call here
     *
     * @return string
     */
    public function Render($sViewName = 'standard', $sViewType = 'Core', $aCallTimeVars = array())
    {
        $oView = new TViewParser();

        $oMsg = TCMSMessageManager::GetInstance();
        $sMsg = $oMsg->RenderMessages(static::GetMessageManagerName($this->sPath));

        // add view variables
        $oView->AddVar('sMsg', $sMsg);
        $oView->AddVar('oTable', $this);
        $oView->AddVar('aActions', $this->aActions);
        $oView->AddVar('sControllingModuleSpotName', $this->sControllingModuleSpotName);

        $oView->AddVar('aCallTimeVars', $aCallTimeVars);
        $aOtherParameters = $this->GetAdditionalViewVariables($sViewName, $sViewType);
        $oView->AddVarArray($aOtherParameters);

        return $oView->RenderObjectView($sViewName, $this->GetViewPath(), $sViewType);
    }

    /**
     * use this method to add any variables to the render method that you may
     * require for some view.
     *
     * @param string $sViewName - the view being requested
     * @param string $sViewType - the location of the view (Core, Custom-Core, Customer)
     *
     * @return array
     */
    protected function GetAdditionalViewVariables($sViewName, $sViewType)
    {
        return array();
    }

    protected static function GetHtmlHeadIncludes()
    {
        $sLine = '<script src="'.URL_USER_CMS_PUBLIC.'/blackbox/classes/THTMLFileBrowser/THTMLFileBrowser.js" type="text/javascript"></script>';
        TGlobal::GetController()->AddHTMLHeaderLine($sLine);
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
