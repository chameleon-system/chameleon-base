<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\BackwardsCompatibilityShims\NamedConstructorSupport;
use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\Event\BackendLoginEvent;
use ChameleonSystem\CoreBundle\Event\BackendLogoutEvent;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\Security\Password\PasswordHashGeneratorInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * the user manager class for the cms.
/**/
class TCMSUser extends TCMSRecord
{
    /**
     * set to true if the user logged in.
     *
     * @var bool
     */
    public $bLoggedIn = false;

    /**
     * access manager object.
     *
     * @var TAccessManager
     */
    public $oAccessManager = null;

    /**
     * holds the user object singleton.
     *
     * @var TCMSUser
     */
    private static $oActiveUser = null;

    public function __construct($id = null)
    {
        parent::__construct('cms_user', $id);
        if (!is_null($id)) {
            $this->Load($id);
        }
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TCMSUser()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }

    /**
     * return active user
     * in frontend it returns always NULL because of performance reasons
     * you don`t get a WWW-user object!
     *
     * @return TdbCmsUser|null
     */
    public static function GetActiveUser()
    {
        if (is_null(self::$oActiveUser)) {
            if (isset($_SESSION) && array_key_exists(self::GetSessionVarName('_user'), $_SESSION) && !empty($_SESSION[self::GetSessionVarName('_user')])) {
                self::$oActiveUser = TdbCmsUser::GetNewInstance();
                self::$oActiveUser->Load($_SESSION[self::GetSessionVarName('_user')]);
                self::$oActiveUser->bLoggedIn = ($_SESSION[self::GetSessionVarName('_user')] == self::$oActiveUser->id);
            }
        }

        return self::$oActiveUser;
    }

    public static function GetSessionVarName($sName)
    {
        $oGlobal = TGlobal::instance();
        // get the cms webuser if this is a modulechooser request..
        $requestModuleChooser = ($oGlobal->UserDataExists('__modulechooser') && ('true' == $oGlobal->GetUserData('__modulechooser')));
        $bit = 'web';
        if (TGlobal::IsCMSMode() || $requestModuleChooser) {
            $bit = 'cms';
        }

        return $sName.$bit;
    }

    /**
     * return the id of all root nodes for the portals assigned to the user.
     *
     * @return array
     */
    public function GetUserPortalRootNodes()
    {
        $aId = array();
        $oPortals = $this->GetMLT('cms_portal_mlt', 'TCMSPortal');
        while ($oPortal = $oPortals->Next()) {
            if (TCMSTreeNode::TREE_ROOT_ID != $oPortal->sqlData['main_node_tree']) {
                $aId[] = $oPortal->sqlData['main_node_tree'];
            }
        }

        return $aId;
    }

    /**
     * checks for valid cms user session.
     *
     * @return bool
     */
    public static function CMSUserDefined()
    {
        $sUserVar = '_usercms';

        return isset($_SESSION) && array_key_exists($sUserVar, $_SESSION) && !empty($_SESSION[$sUserVar]);
    }

    /**
     * load the user from id.
     *
     * @param int $id
     *
     * @return bool
     */
    public function Load($id)
    {
        $bIsLoaded = false;
        if (!empty($id)) {
            if (parent::Load($id)) {
                $bIsLoaded = true;
                $this->_LoadAccessManager();
            }
        }

        return $bIsLoaded;
    }

    /**
     * login the user using the users username and password.
     *
     * @param string $sUsername
     * @param string $sPassword
     *
     * @return bool
     */
    public function Login($sUsername, $sPassword)
    {
        $query = "SELECT * FROM `cms_user` WHERE `login` = '".MySqlLegacySupport::getInstance()->real_escape_string($sUsername)."' LIMIT 0,1";
        if ($userRow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
            $allowCMSLogin = false;
            if (!array_key_exists('allow_cms_login', $userRow) || '1' == $userRow['allow_cms_login']) {
                $allowCMSLogin = true;
            }

            if (false === TGlobal::IsCMSMode() || $allowCMSLogin) {
                if ('' !== $userRow['crypted_pw'] && $this->getPasswordHashGenerator()->verify($sPassword, $userRow['crypted_pw'])) {
                    $this->LoadFromRow($userRow);
                    $this->SetAsActiveUser();
                }
            }
        }
        if ($this->bLoggedIn && TGlobal::IsCMSMode()) {
            $_SESSION[self::GetSessionVarName('_user')] = $this->id;
            $this->getAuthenticityTokenManager()->refreshToken();
        }

        // release old locks
        $query = 'DELETE FROM `cms_lock` WHERE TIMESTAMPDIFF(MINUTE,`time_stamp`,CURRENT_TIMESTAMP()) >= '.RECORD_LOCK_TIMEOUT.'';
        MySqlLegacySupport::getInstance()->query($query);

        $eventDispatcher = self::getEventDispatcher();
        $event = new BackendLoginEvent($this);
        if ($this->bLoggedIn) {
            $eventDispatcher->dispatch($event, CoreEvents::BACKEND_LOGIN_SUCCESS);
        } else {
            $eventDispatcher->dispatch($event, CoreEvents::BACKEND_LOGIN_FAILURE);
        }

        return $this->bLoggedIn;
    }

    /**
     * return true if the passed password matchs the version in the db.
     *
     * @param string $sSourcePwd
     *
     * @return bool
     */
    public function PasswordIsValid($sSourcePwd)
    {
        return $this->getPasswordHashGenerator()->verify($sSourcePwd, $this->sqlData['crypted_pw']);
    }

    /**
     * login current user as active user.
     *
     * if login simulation is on ($bSimulateLogin) then the user login will not be written to the session
     * the login only exists for during runtime of the script that initiates the user activation
     *
     * @param bool $bSimulateLogin
     */
    public function SetAsActiveUser($bSimulateLogin = false)
    {
        $this->_LoadAccessManager();
        $this->bLoggedIn = true;
        // add hash of user values
        if ($bSimulateLogin) {
            self::$oActiveUser = $this;
        } else {
            $_SESSION[self::GetSessionVarName('_USERSESSIONKEY')] = $this->GetUserSessionKey();
            $_SESSION[self::GetSessionVarName('_user')] = $this->id;
        }
    }

    /**
     * logout the user and kill session cookie.
     */
    public static function Logout()
    {
        $user = static::GetActiveUser();
        $sessionKeys = array_keys($_SESSION);
        foreach ($sessionKeys as $key) {
            if ('_usercms' == $key && !empty($_SESSION['_usercms'])) {
                self::ReleaseOpenLocks($_SESSION['_usercms']);
            }
            unset($_SESSION[$key]);
        }

        unset($_SESSION['_listObjCache']);
        $request = self::getRequest();
        if (null !== $request) {
            if (true === $request->hasSession()) {
                $session = $request->getSession();
                $session->clear();
            }
        }

        self::getEventDispatcher()->dispatch(new BackendLogoutEvent($user), CoreEvents::BACKEND_LOGOUT_SUCCESS);
    }

    /**
     * release all open locks.
     *
     * @param string $cmsUserId
     */
    public static function ReleaseOpenLocks($cmsUserId)
    {
        $query = "DELETE FROM `cms_lock` WHERE `cms_user_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($cmsUserId)."'";
        MySqlLegacySupport::getInstance()->query($query);

        $query = 'DELETE FROM `cms_lock` WHERE TIMESTAMPDIFF(MINUTE,`time_stamp`,CURRENT_TIMESTAMP()) >= '.RECORD_LOCK_TIMEOUT.'';
        MySqlLegacySupport::getInstance()->query($query);
    }

    /**
     * checks for valid session key.
     *
     * @return bool
     */
    public function ValidSessionKey()
    {
        $sActiveKey = $this->GetUserSessionKey();
        $sSessionKey = '';
        if (array_key_exists(self::GetSessionVarName('_USERSESSIONKEY'), $_SESSION)) {
            $sSessionKey = $_SESSION[self::GetSessionVarName('_USERSESSIONKEY')];
        }

        return 0 == strcmp($sActiveKey, $sSessionKey);
    }

    /**
     * generates a user session key (md5).
     *
     * @return string
     */
    public function GetUserSessionKey()
    {
        $aKeyData = '';
        // add browser and ip
        // $aKeyData .= $_SERVER['HTTP_USER_AGENT'];   // we have problems with auto-logouts when uploading files via flash, because of different user-agents
        //$aKeyData .= $_SERVER['REMOTE_ADDR']; // cannot use this because users with proxyserver will otherwise be kicked out
        $aKeyData .= $this->id;
        $aKeyData .= 'saltx';

        $userSessionKey = md5($aKeyData);

        return $userSessionKey;
    }

    /**
     * returns the current edit language in iso6391 format e.g. de,en,fr etc.
     *
     * @return string
     */
    public function GetCurrentEditLanguage($bReset = false)
    {
        if (true === $bReset) {
            $_SESSION['cmsbackend-currenteditlanguage'] = null;
        }

        if (isset($_SESSION['cmsbackend-currenteditlanguage']) && !empty($_SESSION['cmsbackend-currenteditlanguage'])) {
            return $_SESSION['cmsbackend-currenteditlanguage'];
        }

        $sCurrentEditLanguageIso6391Code = null;

        if (isset($this->sqlData['cms_current_edit_language']) && !empty($this->sqlData['cms_current_edit_language'])) {
            $sCurrentEditLanguageIso6391Code = $this->sqlData['cms_current_edit_language'];
        }

        // no language set? select the first from the languages available to the user
        if (is_null($sCurrentEditLanguageIso6391Code) && isset($this->sqlData['cms_language_mlt'])) {
            $oEditLanguages = $this->GetMLT('cms_language_mlt');
            if ($oEditLanguages->Length() > 0) {
                $oEditLanguages->GoToStart();
                $oLanguage = $oEditLanguages->Current();
                $sCurrentEditLanguageIso6391Code = $oLanguage->sqlData['iso_6391'];
            }
        }

        if (is_null($sCurrentEditLanguageIso6391Code)) {
            $config = TdbCmsConfig::GetInstance();
            $sCurrentEditLanguageIso6391Code = $config->GetFieldTranslationBaseLanguage()->fieldIso6391;
        }

        $_SESSION['cmsbackend-currenteditlanguage'] = $sCurrentEditLanguageIso6391Code;

        return $sCurrentEditLanguageIso6391Code;
    }

    /**
     * returns the name of the active edit portal.
     *
     * @return TdbCmsPortal
     */
    public function GetActiveEditPortal()
    {
        $activePortalID = $this->GetActiveEditPortalID();
        $oCmsPortal = null;

        if (!is_null($activePortalID)) {
            $oCmsPortal = TdbCmsPortal::GetNewInstance();
            /** @var $oCmsPortal TdbCmsPortal */
            $oCmsPortal->Load($activePortalID);
        }

        return $oCmsPortal;
    }

    /**
     * returns the current active edit portal id.
     *
     * @return string
     */
    public function GetActiveEditPortalID()
    {
        $activePortalID = null;

        if (isset($_SESSION) && array_key_exists('_cms_ActiveEditPortalID', $_SESSION) && !is_null($_SESSION['_cms_ActiveEditPortalID']) && !empty($_SESSION['_cms_ActiveEditPortalID'])) {
            $activePortalID = $_SESSION['_cms_ActiveEditPortalID'];
        }

        return $activePortalID;
    }

    /**
     * saves the current active edit portal id in session.
     *
     * @param string $sPortalID
     */
    public function SetActiveEditPortalID($sPortalID = null)
    {
        if (!is_null($sPortalID)) {
            $_SESSION['_cms_ActiveEditPortalID'] = $sPortalID;
        }
    }

    /**
     * returns the current edit language ID.
     *
     * @return int
     */
    public function GetCurrentEditLanguageID()
    {
        return $this->GetCurrentEditLanguageObject()->id;
    }

    /*
    * @return TdbCmsLanguage
    */
    public function GetCurrentEditLanguageObject()
    {
        $language = self::getLanguageService()->getLanguageFromIsoCode($this->GetCurrentEditLanguage());

        return $language;
    }

    /**
     * Sets the user's edit language both in the current session and permanently in the database.
     *
     * @param string|null $language the two-letter ISO-639-1 language code to set. Defaults to the system's base language.
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    public function SetCurrentEditLanguage($language = null)
    {
        if (null === $language) {
            $config = TdbCmsConfig::GetInstance();
            $language = $config->GetFieldTranslationBaseLanguage()->fieldIso6391;
        }
        $databaseConnection = $this->getDatabaseConnection();
        $updateQuery = 'UPDATE `cms_user` SET `cms_current_edit_language` = :language WHERE `id` = :userId';
        $databaseConnection->executeQuery($updateQuery, array(
            'language' => $language,
            'userId' => $this->sqlData['id'],
        ));
        $this->sqlData['cms_current_edit_language'] = $language;

        // reset language object cache
        $this->SetInternalCache('oCurrentEditLanguage', null);

        $_SESSION['cmsbackend-currenteditlanguage'] = $language;
        $this->GetCurrentEditLanguage(true);
    }

    /**
     * Loads the access manager for the user (controls access to tables and modules).
     */
    public function _LoadAccessManager()
    {
        if (!is_null($this->id)) {
            $this->oAccessManager = new TAccessManager();
            $this->oAccessManager->InitFromObject($this);
        }
    }

    /**
     * returns the default user icon or the custom user image as square thumbnail.
     *
     * @param bool $bWithZoom         - adds the thickbox/lightbox zoom <a> tag
     * @param int  $iThumbWidthHeight -  width/height if square thumbnail
     *
     * @return string
     */
    public function GetUserIcon($bWithZoom = true, $iThumbWidthHeight = 32)
    {
        $imageTag = '<i class="fas fa-user"></i>';
        $sImageID = $this->sqlData['images'];
        if ($sImageID >= 1000 || !is_numeric($sImageID)) {
            $oImage = new TCMSImage();
            /** @var $oImage TCMSImage */
            $oImage->Load($sImageID);
            $oThumbnail = $oImage->GetSquareThumbnail($iThumbWidthHeight);
            /** @var $oThumbnail TCMSImage */
            if ($bWithZoom) {
                $oBigThumbnail = $oImage->GetThumbnail(400, 400);
            }
            $sName = $this->GetName();

            if ($bWithZoom) {
                $imageTag = '<img src="'.TGlobal::OutHTML($oThumbnail->GetFullURL()).'" width="'.TGlobal::OutHTML($oThumbnail->aData['width']).'" height="'.TGlobal::OutHTML($oThumbnail->aData['height']).'" hspace="0" vspace="0" border="0" style="margin-right:10px" align="left" alt="'.TGlobal::OutHTML($sName).'" title="'.TGlobal::OutHTML($sName)."\" onclick=\"CreateMediaZoomDialogFromImageURL('".$oBigThumbnail->GetFullURL()."','".$oBigThumbnail->aData['width']."','".$oBigThumbnail->aData['height']."');event.cancelBubble=true;return false;\" />";
            } else {
                $imageTag = '<img src="'.TGlobal::OutHTML($oThumbnail->GetFullURL()).'" width="'.TGlobal::OutHTML($oThumbnail->aData['width']).'" height="'.TGlobal::OutHTML($oThumbnail->aData['height']).'" hspace="0" vspace="0" border="0" style="margin-right:10px" align="left" alt="'.TGlobal::OutHTML($sName).'" title="'.TGlobal::OutHTML($sName).'" />';
            }
        }

        return $imageTag;
    }

    /**
     * @return AuthenticityTokenManagerInterface
     */
    private function getAuthenticityTokenManager()
    {
        return ServiceLocator::get('chameleon_system_core.security.authenticity_token.authenticity_token_manager');
    }

    /**
     * @return PasswordHashGeneratorInterface
     */
    private function getPasswordHashGenerator()
    {
        return ServiceLocator::get('chameleon_system_core.security.password.password_hash_generator');
    }

    /**
     * @return EventDispatcherInterface
     */
    private static function getEventDispatcher()
    {
        return ServiceLocator::get('event_dispatcher');
    }

    private static function getRequest(): ?Request
    {
        return ServiceLocator::get('request_stack')->getCurrentRequest();
    }
}
