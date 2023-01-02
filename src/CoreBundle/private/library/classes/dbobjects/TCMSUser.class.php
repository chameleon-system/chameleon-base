<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\Security\Password\PasswordHashGeneratorInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * the user manager class for the cms.
 *
/**/
class TCMSUser extends TCMSRecord
{
    /**
     * set to true if the user logged in.
     *
     * @var bool
     * @deprecated since 7.2.0 - no longer used
     */
    public $bLoggedIn = false;

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

    public function isAdmin(): bool
    {
        $groups = $this->GetMLT('cms_role_mlt');
        if (null === $groups) {
            return false;
        }
        while($group = $groups->next()) {
            if ($group->fieldName === 'cms_admin') {
                return true;
            }
        }

        return false;
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
     * @deprecated 7.2 - use symfony security service
     */
    public static function GetActiveUser()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $user = $securityHelper->getUser();
        if (null === $user || false === $securityHelper->isGranted('ROLE_CMS_USER') || false ===($user instanceof \ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel)) {
            self::$oActiveUser = null;
            return null;
        }

        if (null !== self::$oActiveUser && self::$oActiveUser->id === $user->getId()) {
            return self::$oActiveUser;
        }

        /** @var \ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel $user */
        self::$oActiveUser = TdbCmsUser::GetNewInstance();
        self::$oActiveUser->Load($user->getId());

        self::$oActiveUser->bLoggedIn = true;

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
            }
        }

        return $bIsLoaded;
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
     * returns the current edit language in iso6391 format e.g. de,en,fr etc.
     *
     * @return string
     * @deprecated use service \ChameleonSystem\CmsBackendBundle\BackendSession\BackendSession
     */
    public function GetCurrentEditLanguage($bReset = false)
    {
        /** @var BackendSessionInterface $sessionService */
        $sessionService = ServiceLocator::get('chameleon_system_cms_backend.backend_session');
        if (true === $bReset) {
            $sessionService->resetCurrentEditLanguage();
        }
        return $sessionService->getCurrentEditLanguageIso6391();
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
     * @deprecated 7.2 use \ChameleonSystem\CmsBackendBundle\BackendSession\BackendSession
     */
    public function SetCurrentEditLanguage($language = null)
    {
        /** @var BackendSessionInterface $sessionService */
        $sessionService = ServiceLocator::get('chameleon_system_cms_backend.backend_session');
        if (null === $language) {
            $sessionService->resetCurrentEditLanguage();
            return;
        }

        $sessionService->setCurrentEditLanguageIso6391($language);
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
