<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\DatabaseAccessLayer\DatabaseAccessLayerFieldConfig;
use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\Security\Password\PasswordHashGeneratorInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\ExtranetBundle\Exception\PasswordGenerationFailedException;
use ChameleonSystem\ExtranetBundle\ExtranetEvents;
use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;
use ChameleonSystem\ExtranetBundle\objects\ExtranetUserConstants;
use ChameleonSystem\ExtranetBundle\objects\ExtranetUserEvent;
use ChameleonSystem\ShopBundle\Interfaces\ShopServiceInterface;
use Doctrine\DBAL\DBALException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

if (!defined('PKG_EXTRANET_USE_CASE_INSENSITIVE_LOGIN_NAMES')) {
    define('PKG_EXTRANET_USE_CASE_INSENSITIVE_LOGIN_NAMES', false);
}

class TDataExtranetUser extends TDataExtranetUserAutoParent
{
    public const VIEW_PATH = 'pkgExtranet/views/db/TDataExtranetUser';

    public const SESSION_KEY_NAME = 'esono/pkgExtranet/frontendUser';

    public const MSG_FORM_FIELD = 'tdataextranetuserform';

    public const TICKETVARNAME = '_MTExtranetCoreUserTicketNewSystem';

    public const FORM_DATA_NAME_USER = 'aUser';

    /**
     * the session data of the extranet user. used to validate the login session.
     *
     * @var array
     */
    protected $aSessionData = [];

    /**
     * set to true if the user is logged in.
     *
     * @var bool
     */
    protected $isLoggedIn = false;

    /**
     * @var bool
     */
    private static $bValidTicket = false;

    /**
     * the current billing address data.
     *
     * @var TdbDataExtranetUserAddress
     */
    protected $oBillingAddress;

    /**
     * the current shipping address data.
     *
     * @var TdbDataExtranetUserAddress
     */
    protected $oShippingAddress;

    /**
     * all observers that the class should send notifications to.
     *
     * @var IDataExtranetUserObserver[]
     */
    protected $aObservers = [];

    /**
     * used internally to cache the session validation of a user.
     *
     * @var bool
     */
    protected $bSessionValidateResultCache;

    /**
     * @var array
     */
    protected $pageAccessCache = [];

    protected bool $callPostLoginHook = true;

    /**
     * @param string|null $id
     * @param string|null $sLanguageId
     */
    public function __construct($id = null, $sLanguageId = null)
    {
        $this->SetChangeTriggerCacheChangeOnParentTable(false);
        parent::__construct($id, $sLanguageId);
    }

    /**
     * return $this on oUserData -> we need this so the old extranet user calls still work.
     *
     * @param string $sPropertyName
     *
     * @return TDataExtranetUser
     */
    public function __get($sPropertyName)
    {
        if ('oUserData' === $sPropertyName) {
            return clone $this;
        }

        trigger_error('no such property ('.$sPropertyName.') in TDataExtranetUser', E_USER_ERROR);
    }

    public function setCallPostLoginHook(bool $callPostLoginHook): void
    {
        $this->callPostLoginHook = $callPostLoginHook;
    }

    /**
     * return the users email address... use this method instead of the field
     * since shop users tend to have the user name as the email..
     *
     * @return string
     */
    public function GetUserEMail()
    {
        $sEMail = trim($this->fieldEmail);
        if (empty($sEMail)) {
            $sEMail = trim($this->fieldName);
        }

        return $sEMail;
    }

    /**
     * reset the pageAccessCache - needs to be called anytime the users access level changes (login/logout).
     *
     * @return void
     */
    protected function resetPageAccessCache()
    {
        $this->pageAccessCache = [];
    }

    /**
     * register an observer with the user.
     *
     * @param string $sObserverName
     * @param IDataExtranetUserObserver $oObserver
     *
     * @return void
     */
    public function ObserverRegister($sObserverName, $oObserver)
    {
        $this->aObservers[$sObserverName] = $oObserver;
    }

    /**
     * remove an observer from the list.
     *
     * @param string $sObserverName
     *
     * @return void
     */
    public function ObserverUnregister($sObserverName)
    {
        if (array_key_exists($sObserverName, $this->aObservers)) {
            unset($this->aObservers[$sObserverName]);
        }
    }

    /**
     * returns the users customer number, if set.
     *
     * @return string
     */
    public function GetCustomerNumber()
    {
        $sCustNr = '';
        if (is_array($this->sqlData) && array_key_exists('customer_number', $this->sqlData)) {
            $sCustNr = $this->sqlData['customer_number'];
        }

        return $sCustNr;
    }

    /**
     * NOTE: if you are calling save in order to perform a registration, you SHOULD CALL Register instead
     * saves the user... if $bForceUserConfirm is set, then the $oExtranetConf->fieldUserMustConfirmRegistration will
     * be ovewritten and the user will NOT be marked as confirmed - even if $oExtranetConf->fieldUserMustConfirmRegistration = false.
     *
     * {@inheritdoc}
     *
     * @param bool $bForceUserConfirm
     */
    public function Save($bForceUserConfirm = false)
    {
        if (!isset($this->sqlData['cms_portal_id'])) {
            $oPortal = $this->getPortalDomainService()->getActivePortal();
            if ($oPortal) {
                $this->sqlData['cms_portal_id'] = $oPortal->id;
            }
        }
        if (is_null($this->id) || empty($this->id)) {
            $this->sqlData['tmpconfirmkey'] = md5(uniqid((string) rand(), true));
            $oExtranetConf = TdbDataExtranet::GetInstance();
            if ($oExtranetConf->fieldUserMustConfirmRegistration || $bForceUserConfirm) {
                $this->sqlData['confirmed'] = '0';
                $this->sqlData['confirmedon'] = '0';
            } else {
                $this->sqlData['confirmed'] = '1';
                $this->sqlData['confirmedon'] = date('Y-m-d H:i:s');
            }
        }
        $returnVal = parent::Save();
        reset($this->aObservers);
        foreach (array_keys($this->aObservers) as $sObserverName) {
            $this->aObservers[$sObserverName]->OnUserUpdatedHook();
        }

        return $returnVal;
    }

    /**
     * call the method if you want to register the current data in the user as a new user
     * method will
     *   a) save the data under a new id
     *   b) call the PostRegistrationHook (which will send the registration info & auto login the user IF the extranet setting
     *      allows that.
     *
     * @param bool $bForceUserConfirmMail
     * @param bool $bAutoLoginAfterRegistration
     *
     * @return bool|string the id or false for an error
     */
    public function Register($bForceUserConfirmMail = false, $bAutoLoginAfterRegistration = true)
    {
        $bRegistered = $this->Save($bForceUserConfirmMail);
        if ($bRegistered) {
            if ($bAutoLoginAfterRegistration) {
                $this->DirectLogin($this->fieldName, $this->fieldPassword);
                $this->GetShippingAddress(true, true);
            }
            $this->PostRegistrationHook();
        }

        return $bRegistered;
    }

    /**
     * returns true if the current login name exists in the db.
     *
     * @return bool
     */
    public function LoginExists()
    {
        $query = 'SELECT *
                  FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->table)."`
                 WHERE `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->fieldName)."'
                   AND `id` <> '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
               ";
        if (CHAMELEON_EXTRANET_USER_IS_PORTAL_DEPENDANT) {
            if (!empty($this->fieldCmsPortalId)) {
                $query .= " AND `cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->fieldCmsPortalId)."'";
            } else {
                trigger_error('user should have portal id!', E_USER_ERROR);
            }
        }
        $tres = MySqlLegacySupport::getInstance()->query($query);

        return MySqlLegacySupport::getInstance()->num_rows($tres) > 0;
    }

    /**
     * returns an instance of the user object.
     *
     * @param bool $bReset - set to true to reset object
     *
     * @return TdbDataExtranetUser|null
     *
     * @deprecated - use service chameleon_system_extranet.extranet_user_provider instead
     */
    public static function GetInstance($bReset = false)
    {
        /** @var ExtranetUserProviderInterface $extranetUserProvider */
        $extranetUserProvider = ServiceLocator::get('chameleon_system_extranet.extranet_user_provider');
        if ($bReset) {
            $extranetUserProvider->reset();
        }

        return $extranetUserProvider->getActiveUser();
    }

    /**
     * returns true by default
     * you may overwrite this method if you need to leave the user logged in
     * even if he is loaded already on GetInstance calls under special circumstances
     * e.g. wizard where in the first step the user is inserted but not yet confirmed
     * a loading in the second step would not be possible if email confirmation is on.
     *
     * @return bool
     */
    protected function ForceLogoutOnInstanceLoading()
    {
        return true;
    }

    /**
     * If is allowed to login not confirmed user (edit in extranet config)
     * function returns true if the user is logged in but not if the user is confirmed.
     * If you want to check both (login and confirmed use IsLoggedInAndConfirmed)
     * It will activate the user session if it has not been done so already.
     *
     * @return bool
     */
    public function IsLoggedIn()
    {
        if (!$this->isLoggedIn) {
            $this->isLoggedIn = $this->ValidateSessionData();
        }

        return $this->isLoggedIn;
    }

    /**
     * If is allowed to login not confirmed user (edit in extranet config)
     * function checks if user is logged in and confirmed.
     * So you can restrict extranet applications only to confirmed users.
     *
     * @return bool
     */
    public function IsLoggedInAndConfirmed()
    {
        $oExtranetConfig = TdbDataExtranet::GetInstance();
        if ($oExtranetConfig->fieldLoginAllowedNotConfirmedUser) {
            if ($this->IsConfirmedUser()) {
                return $this->IsLoggedIn();
            }

            return false;
        }

        return $this->IsLoggedIn();
    }

    /**
     * return true if some data has been entered for the user (HasData may be true when IsLoggedIn is false).
     *
     * @return bool
     */
    public function HasData()
    {
        $bHasData = is_array($this->sqlData);
        $bHasData = ($bHasData && array_key_exists('lastname', $this->sqlData) && !empty($this->sqlData['lastname']));
        $bHasData = ($bHasData && array_key_exists('city', $this->sqlData) && !empty($this->sqlData['city']));

        return $bHasData;
    }

    /**
     * Login user with encrypted password (only when password in db is encrypted).
     *
     * @param string $sPlainPassword
     * @param string $sLoginName
     *
     * @return bool
     */
    protected function LoginCryptedPassword($sPlainPassword, $sLoginName)
    {
        $sIsloggedIn = false;
        $oUser = $this->GetValidatedLoginUser($sLoginName);
        if (null !== $oUser) {
            if ($this->IsLoginCryptedPasswordCorrect($oUser->fieldPassword, $sPlainPassword)) {
                $oExtranetConfig = TdbDataExtranet::GetInstance();
                if ((!$oUser->IsConfirmedUser() && $oExtranetConfig->fieldLoginAllowedNotConfirmedUser) || $oUser->IsConfirmedUser()) {
                    $this->LoadFromRow($oUser->sqlData);
                    $sIsloggedIn = $this->SaveUserLogindata($sLoginName, $oUser->id);
                }
            }
        }

        return $sIsloggedIn;
    }

    /**
     * Encrypt plain password with salt from crypted password and then compare with crypted password.
     *
     * @param string $sCryptedPassword
     * @param string $sLoginPlainPassword
     *
     * @return bool
     */
    protected function IsLoginCryptedPasswordCorrect($sCryptedPassword, $sLoginPlainPassword)
    {
        return $this->getPasswordHashGenerator()->verify($sLoginPlainPassword, $sCryptedPassword);
    }

    /**
     * Get validated user from db with given login name.
     *
     * @param string $sLoginName
     *
     * @return TdbDataExtranetUser|null
     */
    public function GetValidatedLoginUser($sLoginName)
    {
        $validatedLoginUser = null;

        if (PKG_EXTRANET_USE_CASE_INSENSITIVE_LOGIN_NAMES) {
            $namePart = '`name`';
        } else {
            $namePart = 'CAST(`name` AS BINARY)';
        }
        $query = "SELECT * FROM `data_extranet_user` WHERE $namePart = :loginName AND `name` <> ''";

        $parameters = [];
        $parameters['loginName'] = $sLoginName;

        if (true === CHAMELEON_EXTRANET_USER_IS_PORTAL_DEPENDANT) {
            $portal = $this->getPortalDomainService()->getActivePortal();
            if (null === $portal) {
                return null;
            }
            $query .= ' AND `cms_portal_id` = :portalId';
            $parameters['portalId'] = $portal->id;
        }
        if ($aUser = $this->getDatabaseConnection()->fetchAssociative($query, $parameters)) {
            $oUser = TdbDataExtranetUser::GetNewInstance();
            $oUser->LoadFromRow($aUser);
            if ($this->LoginUserDataValid($oUser->sqlData)) {
                $validatedLoginUser = $oUser;
            }
        }

        return $validatedLoginUser;
    }

    /**
     * Save session data to user and check session data.
     *
     * @param string $sLoginName
     * @param string $sUserId
     *
     * @return bool
     */
    protected function SaveUserLogindata($sLoginName, $sUserId)
    {
        $aKeyData = ['key' => '', 'salt' => '', 'logintime' => time()];
        $aKeyData['salt'] = md5(microtime().'.'.rand());
        $aKeyData['key'] = md5(rand().'-'.$sLoginName.rand().microtime());
        $this->aSessionData = $aKeyData;
        $dbKey = $this->GenerateKey($aKeyData);
        $query = "UPDATE `data_extranet_user`
                   SET `session_key` = '".MySqlLegacySupport::getInstance()->real_escape_string($dbKey)."',
                       `login_timestamp` = '".MySqlLegacySupport::getInstance()->real_escape_string((string) $aKeyData['logintime'])."'
                 WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sUserId)."'";
        MySqlLegacySupport::getInstance()->query($query);
        $this->isLoggedIn = false;
        $this->bSessionValidateResultCache = null; // reset session validation cache
        $this->sqlData['session_key'] = $dbKey;
        $this->sqlData['login_timestamp'] = $aKeyData['logintime'];
        $this->fieldSessionKey = $this->sqlData['session_key'];
        /*
         * @psalm-suppress InvalidPropertyAssignmentValue - Tdb type annotation is incorrect
         */
        $this->fieldLoginTimestamp = $this->sqlData['login_timestamp'];

        return $this->ValidateSessionData();
    }

    /**
     * attempts to login the user (date is taken from post/get)
     * returns true if successfull.
     *
     * @param string $sLoginName
     * @param string $sPlainPassword
     *
     * @return bool
     */
    public function Login($sLoginName = null, $sPlainPassword = null)
    {
        $request = $this->getCurrentRequest();
        $inputFilterUtil = $this->getInputFilterUtil();
        $this->isLoggedIn = false;
        $this->bSessionValidateResultCache = null;
        if (null === $sLoginName && $request->request->has(ExtranetUserConstants::LOGIN_FORM_FIELD_LOGIN_NAME)) {
            $sLoginName = $inputFilterUtil->getFilteredPostInput(ExtranetUserConstants::LOGIN_FORM_FIELD_LOGIN_NAME);
        }

        if (null === $sPlainPassword && $request->request->has('password')) {
            $sPlainPassword = trim($inputFilterUtil->getFilteredPostInput('password', '', false, TCMSUserInput::FILTER_PASSWORD));
        }

        if (!empty($sPlainPassword)) {
            $this->isLoggedIn = $this->PerformLogin($sPlainPassword, $sLoginName);
            if ($this->isLoggedIn && true === $this->callPostLoginHook) {
                $this->PostLoginHook();
            }
        }
        if (false === $this->isLoggedIn) {
            $this->getEventDispatcher()->dispatch(new ExtranetUserEvent($this), ExtranetEvents::USER_LOGIN_FAILURE);
        }

        return $this->isLoggedIn;
    }

    /**
     * Perform login with crypted password fields or plain password fields.
     *
     * @param string $sPlainPassword
     * @param string $sLoginName
     *
     * @return bool
     */
    protected function PerformLogin($sPlainPassword, $sLoginName)
    {
        return $this->LoginCryptedPassword($sPlainPassword, $sLoginName);
    }

    /**
     * validate the table row loaded for the user name (BEFORE THE PASSWORD IS CHECKED).
     *
     * @param array $aUserData
     *
     * @return bool
     */
    protected function LoginUserDataValid($aUserData)
    {
        $bIsValid = true;
        $oExtranetConfig = TdbDataExtranet::GetInstance();
        if (!$oExtranetConfig->fieldLoginAllowedNotConfirmedUser) {
            if (!is_array($aUserData) || count($aUserData) < 1) {
                $aUserData = $this->sqlData;
            }
            if (is_array($aUserData) && count($aUserData) > 0 && !$this->IsConfirmedUser($aUserData)) {
                $bIsValid = false;
            }
        }

        return $bIsValid;
    }

    /**
     * @return void
     */
    protected function PostLoginHook()
    {
        $this->resetPageAccessCache();
        $this->bSessionValidateResultCache = null;
        $this->oBillingAddress = null;
        $this->oShippingAddress = null;
        $this->GetBillingAddress();
        $this->GetShippingAddress();
        reset($this->aObservers);
        foreach (array_keys($this->aObservers) as $sObserverName) {
            $this->aObservers[$sObserverName]->OnUserLoginHook();
        }
        $this->CreateLoginHistoryEntry();

        $this->getEventDispatcher()->dispatch(new ExtranetUserEvent($this), ExtranetEvents::USER_LOGIN_SUCCESS);
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return ServiceLocator::get('event_dispatcher');
    }

    /**
     * creates a new login history record with current timestamp and user ip for the logged in user.
     *
     * @return void
     */
    protected function CreateLoginHistoryEntry()
    {
        $oLoginHistory = TdbDataExtranetUserLoginHistory::GetNewInstance();
        $oLoginHistory->AllowEditByAll(true);
        $oLoginHistory->sqlData['data_extranet_user_id'] = $this->id;
        $oLoginHistory->sqlData['user_ip'] = $this->getCurrentRequest()->getClientIp();
        $oLoginHistory->Save();
    }

    /**
     * login using plain password.
     *
     * @param string $sLoginName
     * @param string $sPassword
     * @param bool $callPostLoginHook
     *
     * @return bool
     */
    public function DirectLogin($sLoginName, $sPassword, $callPostLoginHook = false)
    {
        $this->setCallPostLoginHook($callPostLoginHook);

        $this->AllowEditByAll(true);
        $bWasLoggedIn = $this->Login($sLoginName, $sPassword);
        $this->AllowEditByAll(false);

        return $bWasLoggedIn;
    }

    /**
     * allows a login using the user name only (ie call this if you want to force a login via php).
     *
     * @param string $sLoginName
     * @param string $portalId
     *
     * @return bool
     */
    public function DirectLoginWithoutPassword($sLoginName, $portalId = null)
    {
        $this->isLoggedIn = false;
        $oUser = static::getExtranetUserProvider()->getActiveUser();
        $fields = ['name' => $sLoginName];
        if (null !== $portalId) {
            $fields['cms_portal_id'] = $portalId;
        }
        if ($oUser->LoadFromFields($fields, true)) {
            $this->isLoggedIn = $this->SaveUserLogindata($sLoginName, $oUser->id);
            if ($this->isLoggedIn && true === $this->callPostLoginHook) {
                $this->PostLoginHook();
            }
        }

        return $this->isLoggedIn;
    }

    /**
     * logs the user out.
     *
     * @return void
     */
    public function Logout()
    {
        $this->getEventDispatcher()->dispatch(new ExtranetUserEvent($this), ExtranetEvents::USER_BEFORE_LOGOUT);
        $wasLoggedOut = false;
        if ($this->IsLoggedIn() && $this->ValidateSessionData()) {
            // invalidate user record
            $query = "UPDATE `data_extranet_user` SET `login_timestamp` = '0' WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
            MySqlLegacySupport::getInstance()->query($query);
            $wasLoggedOut = true;
        }
        $this->bSessionValidateResultCache = null;
        $this->id = null;
        $this->isLoggedIn = false;
        $this->aSessionData = [];
        reset($this->aObservers);
        foreach (array_keys($this->aObservers) as $sObserverName) {
            $this->aObservers[$sObserverName]->OnUserLogoutHook();
        }

        $aProtectedVariables = $this->GetProtectedSessionVariables();
        $request = $this->getCurrentRequest();
        if (null !== $request) {
            if (true === $request->hasSession()) {
                $session = $request->getSession();
                $all = $session->all();
                $new = [];
                foreach ($aProtectedVariables as $key) {
                    if (isset($all[$key])) {
                        $new[$key] = $all[$key];
                    }
                }

                $session->invalidate(); // also clears $_SESSION
                if (\count($new) > 0) {
                    $session->replace($new);
                }
            }
        }

        $this->resetPageAccessCache();
        $this->sqlData = false;
        $this->oBillingAddress = null;
        $this->oShippingAddress = null;
        static::getExtranetUserProvider()->reset();

        if ($wasLoggedOut) {
            $this->getEventDispatcher()->dispatch(new ExtranetUserEvent($this), ExtranetEvents::USER_LOGOUT_SUCCESS);
        }
    }

    /**
     * return an array of variable names that are flagged as protected and
     * will be saved to the new session on logout.
     *
     * @return array
     */
    protected function GetProtectedSessionVariables()
    {
        return [];
    }

    /**
     * resets the session to an empty array but preserves all variables from
     * $aProtectedVariables and moves them to the new session.
     *
     * @param array $aProtectedVariables
     *
     * @return void
     */
    protected function CleanUpSession($aProtectedVariables = [])
    {
        $aNewSession = [];
        if (is_array($aProtectedVariables) && count($aProtectedVariables) > 0) {
            foreach ($aProtectedVariables as $sKey) {
                $aNewSession[$sKey] = $_SESSION[$sKey];
            }
        }

        $_SESSION = $aNewSession;
    }

    /**
     * @param array $aParameter
     *
     * @return string
     */
    public function CreateTransactionTicket($aParameter = [])
    {
        $sSalt = md5(uniqid((string) rand(), true));
        $_SESSION[self::TICKETVARNAME] = $sSalt;

        return $this->CalculateTicketValue($sSalt, $aParameter);
    }

    /**
     * @return bool
     */
    public static function HasValidTicket()
    {
        return true;
    }

    /**
     * @param string $sTicket
     * @param array $aParameter
     *
     * @return void
     */
    public function UseTicket($sTicket, $aParameter = [])
    {
        $sActiveTicket = '';
        if (array_key_exists(self::TICKETVARNAME, $_SESSION)) {
            $sActiveTicket = $_SESSION[self::TICKETVARNAME];
        }

        $sActiveTicketValue = $this->CalculateTicketValue($sActiveTicket, $aParameter);
        self::$bValidTicket = (0 == strcmp($sActiveTicketValue, $sTicket));
        $_SESSION[self::TICKETVARNAME] = null;
    }

    /**
     * @param string $sSalt
     * @param array $aParameter
     *
     * @return string
     */
    private function CalculateTicketValue($sSalt, $aParameter)
    {
        $aParameter['data_extranet_user_id'] = $this->id;
        ksort($aParameter);
        $sParamString = $sSalt.'|';
        foreach ($aParameter as $key => $val) {
            $sParamString .= $key.'='.$val;
        }

        return md5($sParamString);
    }

    /**
     * returns true if the user is in at least one of the groups passed.
     *
     * @param array $aUserGroups - group ids
     *
     * @return bool
     */
    public function InUserGroups($aUserGroups)
    {
        if (false === is_array($this->sqlData)) {
            return false;
        }

        $aActiveGroups = $this->GetUserGroupIds();
        if (0 === count($aActiveGroups)) {
            return false;
        }

        reset($aUserGroups);
        foreach ($aUserGroups as $groupId) {
            if (in_array($groupId, $aActiveGroups)) {
                return true;
            }
        }

        return false;
    }

    /**
     * returns all user group ids of the user.
     *
     * @param bool $bForceReload
     *
     * @return string[]
     */
    public function GetUserGroupIds($bForceReload = false)
    {
        /** @var string[]|null $aActiveGroups */
        $aActiveGroups = $this->GetFromInternalCache('_user_group_ids');

        if (null === $aActiveGroups || $bForceReload) {
            $aActiveGroups = $this->GetMLTIdList('data_extranet_group');
            sort($aActiveGroups);

            $this->SetInternalCache('_user_group_ids', $aActiveGroups);
        }

        return $aActiveGroups;
    }

    /**
     * sets an extranet user group.
     *
     * @param int|string|null $groupID
     *
     * @return bool
     */
    public function SetUserGroup($groupID = null)
    {
        if (null === $groupID || null === $this->id) {
            return false;
        }
        $this->resetPageAccessCache();
        $databaseConnection = $this->getDatabaseConnection();
        $parameters = [
            'sourceId' => $this->id,
            'targetId' => $groupID,
        ];
        try {
            $cleanUpQuery = 'DELETE FROM `data_extranet_user_data_extranet_group_mlt` WHERE `source_id` = :sourceId AND `target_id` = :targetId';
            $databaseConnection->executeQuery($cleanUpQuery, $parameters);

            $insertQuery = 'INSERT INTO `data_extranet_user_data_extranet_group_mlt` SET `source_id` = :sourceId, `target_id` = :targetId';
            $databaseConnection->executeQuery($insertQuery, $parameters);

            $bSuccess = true;
        } catch (DBALException $e) {
            $bSuccess = false;
        }

        $this->GetUserGroupIds(true);

        return $bSuccess;
    }

    /**
     * @return bool
     */
    public function AllowActivePageAccess()
    {
        if (TTools::CMSEditRequest()) {
            return true;
        }
        $oActivePage = $this->getActivePageService()->getActivePage();

        return $this->AllowPageAccess($oActivePage->id);
    }

    /**
     * @param string $iPage
     *
     * @return bool
     */
    public function AllowPageAccess($iPage)
    {
        if (TTools::CMSEditRequest()) {
            return true;
        }

        if (isset($this->pageAccessCache[$iPage])) {
            return $this->pageAccessCache[$iPage];
        }

        $this->pageAccessCache[$iPage] = false;
        $aActiveGroups = $this->GetUserGroupIds();
        $oPage = new TCMSPage($iPage);
        if (($oPage->sqlData['access_not_confirmed_user'] && !$this->IsConfirmedUser()) || $this->IsLoggedInAndConfirmed()) {
            // allow access if the page does not require any groups
            $databaseConnection = $this->getDatabaseConnection();
            $quotedPageId = $databaseConnection->quote($iPage);
            $query = "SELECT * FROM `cms_tpl_page_data_extranet_group_mlt`
                      WHERE `source_id` = $quotedPageId";
            $tres = MySqlLegacySupport::getInstance()->query($query);
            if (MySqlLegacySupport::getInstance()->num_rows($tres) < 1) {
                $this->pageAccessCache[$iPage] = true;
            } else {
                if (is_array($aActiveGroups) && count($aActiveGroups) > 0) {
                    $quotedActiveGroups = implode(',', array_map([$databaseConnection, 'quote'], $aActiveGroups));
                    $query = "SELECT * FROM `cms_tpl_page_data_extranet_group_mlt`
                              WHERE `source_id` = $quotedPageId
                              AND `target_id` IN ($quotedActiveGroups)";
                    $matchGroups = MySqlLegacySupport::getInstance()->query($query);
                    if (MySqlLegacySupport::getInstance()->num_rows($matchGroups) > 0) {
                        $this->pageAccessCache[$iPage] = true;
                    }
                }
            }
        }

        return $this->pageAccessCache[$iPage];
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup()
    {
        parent::__wakeup();
        if (false !== $this->sqlData) {
            $this->LoadFromRow($this->sqlData);
        }
        $this->bSessionValidateResultCache = null;
    }

    /**
     * @return string[]
     */
    public function __sleep()
    {
        return ['table', 'oBillingAddress', 'oShippingAddress', 'aSessionData', 'sqlData', 'pageAccessCache'];
    }

    /**
     * validates the user session data.
     *
     * @return bool
     */
    public function ValidateSessionData()
    {
        if (null !== $this->bSessionValidateResultCache) {
            return $this->bSessionValidateResultCache;
        }

        $oExtranetConfig = TdbDataExtranet::GetInstance();
        $dataValid = false;
        if (false === $oExtranetConfig) {
            return false;
        }
        $aLoginData = $this->aSessionData;
        // check 1: all parameters present?
        if (array_key_exists('key', $aLoginData) && array_key_exists('salt', $aLoginData) && array_key_exists('logintime', $aLoginData)) {
            $sSessionKey = $this->getSessionKey($aLoginData);
            $aUserData = $this->getUserDataFromSessionKey($sSessionKey);
            if ($aUserData) {
                $iCurrentTime = time();
                $dataValid = (($iCurrentTime - $aUserData['login_timestamp']) < $oExtranetConfig->fieldSessionlife);
                if ($dataValid && CHAMELEON_EXTRANET_USER_IS_PORTAL_DEPENDANT && !TGlobal::IsCMSMode()) {
                    $dataValid = $this->checkForActivePortal($aUserData);
                }
                if ($dataValid) {
                    $this->updateLoginTimeStamp($sSessionKey, $aUserData);
                }
            }
        }
        $this->bSessionValidateResultCache = $dataValid;

        return $dataValid;
    }

    /**
     * @param string $sSessionKey
     * @param array $aUserData
     *
     * @return void
     */
    private function updateLoginTimeStamp($sSessionKey, $aUserData)
    {
        if (CHAMELEON_DEBUG_SESSION_DISABLE_SESSION_CHECK === true) {
            return;
        }
        // we are using the last used data from the meta data in the session so that the setting, that avoids writing unchanged session data during a given threshold works
        // If we update the login_timestamp at every request, we will change the session data every request as well and the threshold will never be reached, resulting in a session write
        // at every request.
        $request = $this->getCurrentRequest();
        $session = null !== $request && $request->hasSession() ? $request->getSession() : null;
        if (null === $session) {
            return;
        }
        $lastUsed = $session->getMetadataBag()->getLastUsed();

        if ((int) $aUserData['login_timestamp'] !== (int) $lastUsed) {
            $query = 'UPDATE `data_extranet_user` SET `login_timestamp` = :lastUsed WHERE id = :userId and `session_key` = :sessionKey';
            $this->getDatabaseConnection()->executeQuery($query, [
                'lastUsed' => $lastUsed,
                'userId' => $aUserData['id'],
                'sessionKey' => $sSessionKey,
            ]);
            $this->LoadFromRow($aUserData);
        }
    }

    /**
     * @param array $aUser
     *
     * @return bool
     */
    private function checkForActivePortal($aUser)
    {
        $oPortal = $this->getPortalDomainService()->getActivePortal();
        if (!$oPortal || $aUser['cms_portal_id'] != $oPortal->id) {
            $sLogMessage = 'Logging out user '.$aUser['id'].' because his portal id ('.
                $aUser['cms_portal_id'].') does not belong to active portal ('.$oPortal->id.')';
            TTools::WriteLogEntry($sLogMessage, 1, __FILE__, __LINE__);

            return false;
        }

        return true;
    }

    /**
     * @param array $aLoginData
     *
     * @return string
     */
    private function getSessionKey($aLoginData)
    {
        return $this->GenerateKey([
            'key' => $aLoginData['key'],
            'salt' => $aLoginData['salt'],
            'logintime' => $aLoginData['logintime'],
        ]);
    }

    /**
     * @param string $sSessionKey
     *
     * @return array
     *
     * @psalm-suppress FalsableReturnStatement
     */
    private function getUserDataFromSessionKey($sSessionKey)
    {
        $parameters = [
            'id' => $this->id,
        ];
        if (CHAMELEON_DEBUG_SESSION_DISABLE_SESSION_CHECK === true) {
            $query = 'SELECT * FROM `data_extranet_user` WHERE `id` = :id';
        } else {
            $query = 'SELECT * FROM `data_extranet_user` WHERE `session_key` = :sessionKey AND `id` = :id';
            $parameters['sessionKey'] = $sSessionKey;
        }

        return $this->getDatabaseConnection()->fetchAssociative($query, $parameters);
    }

    /**
     * generates a key using the assoc array as input (order is not important -
     * the function will sort the data by key first).
     *
     * @param array $aParameters - assoc array of parameters
     *
     * @return string - md5 key
     */
    protected function GenerateKey($aParameters)
    {
        if (CHAMELEON_SECURITY_EXTRANET_SESSION_USE_USER_AGENT_IN_KEY) {
            $aParameters['user_agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        }
        if (CHAMELEON_SECURITY_EXTRANET_SESSION_USE_IP_IN_KEY) {
            $aParameters['user_ip'] = $this->getCurrentRequest()->getClientIp();
        }

        ksort($aParameters);
        $sString = '';
        foreach ($aParameters as $key => $value) {
            $sString .= $key.$value;
        }

        return md5($sString);
    }

    /**
     * return country if connected.
     *
     * @return TdbDataCountry|null
     */
    public function GetCountry()
    {
        $oCountry = null;
        if (!empty($this->fieldDataCountryId)) {
            $oCountry = TdbDataCountry::GetNewInstance();
            if (!$oCountry->Load($this->fieldDataCountryId)) {
                $oCountry = null;
            }
        }

        return $oCountry;
    }

    /**
     * returns true if the billing and shipping address are the same.
     *
     * @param bool $bSetShippingToBillingAddress
     *
     * @return bool
     */
    public function ShipToBillingAddress($bSetShippingToBillingAddress = false)
    {
        if ($bSetShippingToBillingAddress) {
            $this->fieldDefaultShippingAddressId = null;
            $this->sqlData['default_shipping_address_id'] = null;
            if (null !== $this->id && $this->IsLoggedIn()) {
                $this->Save();
            }
            $this->GetShippingAddress(true);
            $bIsSame = true;
        } else {
            $oBilling = $this->GetBillingAddress();
            $oShipping = $this->GetShippingAddress();
            $bIsSame = (null === $oShipping || !$oShipping->ContainsData() || $oBilling->IsSameAs($oShipping));
        }

        return $bIsSame;
    }

    /**
     * changes the users shipping address to the first address that is not the billing address
     * if there is no other address, then a new empty address will
     * be created.
     *
     * @return bool
     */
    public function ShipToAddressOtherThanBillingAddress()
    {
        $bNewAdrSet = false;
        if (null !== $this->id && $this->IsLoggedIn()) {
            $oAdrList = $this->GetUserAddresses();
            $oBillingAdr = $this->GetBillingAddress();
            if ($oAdrList->Length() > 1) {
                $iNewShippingAdrId = null;
                $oAdrList->GoToStart();
                while ($oAdr = $oAdrList->Next()) {
                    if (!$oAdr->IsSameAs($oBillingAdr)) {
                        $iNewShippingAdrId = $oAdr->id;
                    }
                }
                if (null !== $iNewShippingAdrId) {
                    $aData = $this->sqlData;
                    $aData['default_shipping_address_id'] = $iNewShippingAdrId;
                    $this->LoadFromRow($aData);
                    $this->Save();
                    $this->GetShippingAddress(true);
                    $bNewAdrSet = true;
                }
            }
        }
        if (!$bNewAdrSet) {
            // set shipping addres to a new empty address
            $aAdrData = ['name' => 'Neue Adresse'];
            $this->oShippingAddress = TdbDataExtranetUserAddress::GetNewInstance();
            $this->oShippingAddress->LoadFromRowProtected($aAdrData);
            $this->sqlData['default_shipping_address_id'] = null;
            $this->fieldDefaultShippingAddressId = null;
            if (null !== $this->id && $this->IsLoggedIn()) {
                $this->oShippingAddress->Save();
                $this->sqlData['default_shipping_address_id'] = $this->oShippingAddress->id;
                $this->fieldDefaultShippingAddressId = $this->oShippingAddress->id;
                $this->Save();
                $this->GetBillingAddress(true);
                $this->GetShippingAddress(true);
            }
        }

        return $bNewAdrSet;
    }

    /**
     * return the users shipping address. if the user has no shipping address, then the last billing address
     * will be used. if a billing addres can not be found either, then the system will try to generate an
     * address based on the current user data. if that fails to, an empty shipping address will be returned
     * the default shipping address id will be updated by the method as well.
     *
     * @param bool $bReset - set to true if you want to reload the info
     * @param bool $bGetFromInput - set to true if you want to fetch the data from the GET/POST
     *
     * @return TdbDataExtranetUserAddress|false|null
     */
    public function GetShippingAddress($bReset = false, $bGetFromInput = false)
    {
        if ($bReset) {
            $this->oShippingAddress = null;
        }
        if ($bGetFromInput) {
            $oGlobal = TGlobal::instance();
            $aShipping = $oGlobal->GetUserData(TdbDataExtranetUserAddress::FORM_DATA_NAME_SHIPPING);
            if (is_array($aShipping) && count($aShipping) > 0) {
                $this->oShippingAddress = TdbDataExtranetUserAddress::GetNewInstance();
                $this->oShippingAddress->LoadFromRowProtected($aShipping);
            }
        }
        if (null === $this->oShippingAddress && property_exists($this, 'fieldDefaultShippingAddressId')) {
            $this->oShippingAddress = null;
            if (!empty($this->fieldDefaultShippingAddressId)) {
                $this->oShippingAddress = TdbDataExtranetUserAddress::GetNewInstance();
                if (!$this->oShippingAddress->Load($this->fieldDefaultShippingAddressId)) {
                    $this->oShippingAddress = null;
                }
            }
            if (null === $this->oShippingAddress) {
                $this->oShippingAddress = $this->GetBillingAddress();

                // still nothing? create an address entry based on the current user data
                if (null === $this->oShippingAddress) {
                    $this->oShippingAddress = $this->CreateAddressBasedOnRegistrationData();
                }

                // update user to point to the new address
                if (null !== $this->oShippingAddress && null !== $this->oShippingAddress->id) {
                    $this->sqlData['default_shipping_address_id'] = $this->oShippingAddress->id;
                    $this->fieldDefaultShippingAddressId = $this->oShippingAddress->id;
                    if (!empty($this->id) && null !== $this->id && $this->IsLoggedIn()) {
                        $this->Save();
                    }
                }
            }

            if (null === $this->oShippingAddress) {
                // need to create an empty address
                $this->oShippingAddress = TdbDataExtranetUserAddress::GetNewInstance();
            }
        }

        return $this->oShippingAddress;
    }

    /**
     * return the users billing address - if no address has been set, the system tries to create one based on the
     * current user data. if that fails, an empty address will be returned
     * the user will be updated to point to the new address (if it changed).
     *
     * @param bool $bReset
     *
     * @return TdbDataExtranetUserAddress|false|null
     */
    public function GetBillingAddress($bReset = false)
    {
        if ($bReset) {
            $this->oBillingAddress = null;
        }
        if (null === $this->oBillingAddress && property_exists($this, 'fieldDefaultBillingAddressId')) {
            $this->oBillingAddress = null;
            if (!empty($this->fieldDefaultBillingAddressId)) {
                // use billing address
                $this->oBillingAddress = TdbDataExtranetUserAddress::GetNewInstance();
                if (!$this->oBillingAddress->Load($this->fieldDefaultBillingAddressId)) {
                    $this->oBillingAddress = null;
                }
            }

            // still nothing? create an address entry based on the current user data
            if (null === $this->oBillingAddress) {
                $this->oBillingAddress = $this->CreateAddressBasedOnRegistrationData(false);

                if (null !== $this->oBillingAddress && null !== $this->oBillingAddress->id) {
                    $this->sqlData['default_billing_address_id'] = $this->oBillingAddress->id;
                    $this->fieldDefaultBillingAddressId = $this->oBillingAddress->id;
                    if (!empty($this->id) && null !== $this->id && $this->IsLoggedIn()) {
                        $this->Save();
                    }
                }
            }

            if (null === $this->oBillingAddress) {
                // need to create an empty address
                $this->oBillingAddress = TdbDataExtranetUserAddress::GetNewInstance();
            }
        }

        return $this->oBillingAddress;
    }

    /**
     * set address as new billing address... will check if the address belongs to the user.
     *
     * @param string $sAddressId
     *
     * @return TdbDataExtranetUserAddress|false|null
     */
    public function SetAddressAsBillingAddress($sAddressId)
    {
        $oNewBillingAdr = null;
        if (0 != strcmp($this->fieldDefaultBillingAddressId, $sAddressId)) {
            $oAdr = TdbDataExtranetUserAddress::GetNewInstance();
            if ($oAdr->LoadFromFields(['id' => $sAddressId, 'data_extranet_user_id' => $this->id])) {
                $this->SaveFieldsFast(['default_billing_address_id' => $sAddressId]);
                $oNewBillingAdr = $this->GetBillingAddress(true);
            }
        } else {
            $oNewBillingAdr = $this->GetBillingAddress();
        }

        return $oNewBillingAdr;
    }

    /**
     * set address as new shipping address... will check if the address belongs to the user.
     *
     * @param string $sAddressId
     *
     * @return TdbDataExtranetUserAddress|false|null
     */
    public function SetAddressAsShippingAddress($sAddressId)
    {
        $oNewShippingAdr = null;
        if (0 != strcmp($this->fieldDefaultShippingAddressId, $sAddressId)) {
            $oAdr = TdbDataExtranetUserAddress::GetNewInstance();
            if ($oAdr->LoadFromFields(['id' => $sAddressId, 'data_extranet_user_id' => $this->id])) {
                $this->SaveFieldsFast(['default_shipping_address_id' => $sAddressId]);
                $oNewShippingAdr = $this->GetShippingAddress(true);
            }
        } else {
            $oNewShippingAdr = $this->GetShippingAddress();
        }

        return $oNewShippingAdr;
    }

    /**
     * loads data from row, but removes protected fields.
     *
     * @param array $aRow
     * @param bool $bClearUndefinedFields - set to false if you want to keep data in the object not passed by aRow (or
     *                                    data that is in the object and marked as protected()
     *
     * @return void
     */
    public function LoadFromRowProtected($aRow, $bClearUndefinedFields = true)
    {
        $aProtected = [
            'id',
            'shop_id',
            'cms_portal_id',
            'data_extranet_user_address',
            'default_billing_address_id',
            'default_shipping_address_id',
            'data_extranet_group_mlt',
            'datecreated',
            'shop_user_purchased_voucher',
            'shop_user_notice_list',
            'tmpconfirmkey',
            'confirmed',
            'confirmedon',
            'reg_email_send',
        ];
        if ($bClearUndefinedFields) {
            foreach ($aProtected as $sFieldName) {
                if (array_key_exists($sFieldName, $aRow)) {
                    unset($aRow[$sFieldName]);
                }
            }
        } else {
            $aFullData = $this->sqlData;
            if (!is_array($aFullData)) {
                $aFullData = [];
            }
            foreach ($aRow as $sFieldName => $sValue) {
                if (false == in_array($sFieldName, $aProtected)) {
                    $aFullData[$sFieldName] = $sValue;
                }
            }
            $aRow = $aFullData;
        }
        // now set default fields
        $oShopConf = $this->getShopService()->getActiveShop();
        $aRow['shop_id'] = $oShopConf->id;
        $oPortal = $this->getPortalDomainService()->getActivePortal();
        if ($oPortal) {
            $aRow['cms_portal_id'] = $oPortal->id;
        }
        $this->LoadFromRow($aRow);
    }

    /**
     * return list of addresses for the user.
     *
     * @return TdbDataExtranetUserAddressList
     */
    public function GetUserAddresses()
    {
        return TdbDataExtranetUserAddressList::GetUserAddressList($this->id);
    }

    /**
     * updates the current billing address. returns true if the address was updated.
     *
     * @param array $aAddressData
     *
     * @return bool
     */
    public function UpdateBillingAddress($aAddressData)
    {
        $bAddressUpdated = false;
        if (!array_key_exists('id', $aAddressData) || empty($aAddressData['id'])) {
            // create a new shipping address
            $oAddress = TdbDataExtranetUserAddress::GetNewInstance();
            $oAddress->LoadFromRowProtected($aAddressData);
            $oExistingAddress = $this->FindMatchingAddress($oAddress);
            if ($oExistingAddress) {
                $aData = $oAddress->sqlData;
                $aData['id'] = $oExistingAddress->id;
                $aData['cmsident'] = $oExistingAddress->sqlData['cmsident'];
                $oAddress->LoadFromRow($aData);
            }
        } else {
            $oAddress = $this->GetBillingAddress();
            if (0 == strcmp($oAddress->id, $aAddressData['id'])) {
                $oAddress->LoadFromRowProtected($aAddressData);
            } else {
                $oAddress->LoadFromFields(['data_extranet_user_id' => $this->id, 'id' => $aAddressData['id']]);
                $oAddress->LoadFromRowProtected($aAddressData);
            }
        }
        if (!$oAddress->ContainsData()) {
            $this->GetBillingAddress(true);
        } else {
            $bAddressUpdated = true;
            if (null !== $this->id) {
                if (null === $oAddress->id) {
                    $oAddress->AllowEditByAll(true);
                }
                $oAddress->Save();
                $oAddress->AllowEditByAll(false);
                if (0 != strcmp($oAddress->id, $this->fieldDefaultBillingAddressId)) {
                    $this->SetAddressAsBillingAddress($oAddress->id);
                }
            }
            if ($this->getShopService()->getActiveShop()->fieldSyncProfileDataWithBillingData) {
                $this->SetUserBaseDataUsingAddress($oAddress);
            }
            $this->oBillingAddress = $oAddress;
        }

        return $bAddressUpdated;
    }

    /**
     * updates the current shipping address. returns true if the address was updated.
     *
     * @param array $aAddressData
     *
     * @return bool
     */
    public function UpdateShippingAddress($aAddressData)
    {
        $bAddressUpdated = false;
        if (!array_key_exists('id', $aAddressData) || empty($aAddressData['id'])) {
            // create a new shipping address
            $oAddress = TdbDataExtranetUserAddress::GetNewInstance();
            $oAddress->LoadFromRowProtected($aAddressData);
            $oExistingAddress = $this->FindMatchingAddress($oAddress);
            if ($oExistingAddress) {
                $aData = $oAddress->sqlData;
                $aData['id'] = $oExistingAddress->id;
                $aData['cmsident'] = $oExistingAddress->sqlData['cmsident'];
                $oAddress->LoadFromRow($aData);
            }
        } else {
            $oAddress = $this->GetShippingAddress();
            if (0 == strcmp($oAddress->id, $aAddressData['id'])) {
                $oAddress->LoadFromRowProtected($aAddressData);
            } else {
                $oAddress->LoadFromFields(['data_extranet_user_id' => $this->id, 'id' => $aAddressData['id']]);
                $oAddress->LoadFromRowProtected($aAddressData);
            }
        }
        $oBilling = $this->GetBillingAddress(true);
        if (!$oAddress->ContainsData() || $oAddress->IsSameAs($oBilling)) {
            // if they are the same only because we have the same id, then we need to update the billing address... and then set them to the same
            if (!empty($oBilling->id) && 0 == strcmp($oBilling->id, $oAddress->id)) {
                $bAddressUpdated = $this->UpdateBillingAddress($aAddressData);
            }
            $this->ShipToBillingAddress(true);
        } else {
            $bAddressUpdated = true;
            if (!is_null($this->id)) {
                if (null === $oAddress->id) {
                    $oAddress->AllowEditByAll(true);
                }
                $oAddress->Save();
                $oAddress->AllowEditByAll(false);
                if (0 != strcmp($oAddress->id, $this->fieldDefaultShippingAddressId)) {
                    $this->SetAddressAsShippingAddress($oAddress->id);
                }
            }
            $this->oShippingAddress = $oAddress;
        }

        return $bAddressUpdated;
    }

    /**
     * set the users registration data to the data held in $oAddresse.
     *
     * @return void
     */
    public function SetUserBaseDataUsingAddress(TdbDataExtranetUserAddress $oAddress)
    {
        $aData = [
            'data_extranet_salutation_id' => $oAddress->fieldDataExtranetSalutationId,
            'address_additional_info' => $oAddress->fieldAddressAdditionalInfo,
            'company' => $oAddress->fieldCompany,
            'firstname' => $oAddress->fieldFirstname,
            'lastname' => $oAddress->fieldLastname,
            'street' => $oAddress->fieldStreet,
            'streetnr' => $oAddress->fieldStreetnr,
            'city' => $oAddress->fieldCity,
            'postalcode' => $oAddress->fieldPostalcode,
            'telefon' => $oAddress->fieldTelefon,
            'fax' => $oAddress->fieldFax,
            'data_country_id' => $oAddress->fieldDataCountryId,
        ];
        $aUserData = $this->sqlData;
        foreach ($aData as $key => $val) {
            $aUserData[$key] = $val;
        }
        $this->LoadFromRow($aUserData);
        if (!empty($this->id)) {
            $this->Save();
        }
    }

    /**
     * create a user address for the user based on the current registration data.
     *
     * @param bool $bUseExistingAddress - set to false if you want to create an address even if an address exists in the address list
     *
     * @return TdbDataExtranetUserAddress|false|null
     */
    public function CreateAddressBasedOnRegistrationData($bUseExistingAddress = true)
    {
        $oAddress = null;
        // we do this only if this user has no addresses
        $oAddressList = TdbDataExtranetUserAddressList::GetUserAddressList($this->id);
        if ($oAddressList->Length() < 1 || !$bUseExistingAddress) {
            $oAddress = $this->getAddressFromRegistrationData();

            // save record only if the user has an id
            if (null !== $this->id) {
                $oAddress->AllowEditByAll(true);
                $oAddress->Save();
                $oAddress->AllowEditByAll(false);
            }
        } else {
            $oAddress = $oAddressList->Current();
        }

        return $oAddress;
    }

    /**
     * convert registration data to an address object.
     *
     * @return TdbDataExtranetUserAddress
     */
    public function getAddressFromRegistrationData()
    {
        $oAddress = TdbDataExtranetUserAddress::GetNewInstance();
        // get all fields from the address table
        $oAdrTable = TdbCmsTblConf::GetNewInstance();
        $oAdrTable->LoadFromField('name', 'data_extranet_user_address');
        $oFieldList = TdbCmsFieldConfList::GetListForCmsTblConfId($oAdrTable->id);
        $aData = [];
        if (true === is_array($this->sqlData) && count($this->sqlData) > 0) {
            $aNotAllowed = ['id', 'name', 'cmsident', 'password', 'session_key',
                'login_timestamp', 'login_salt', 'shop_id', 'datecreated', 'tmpconfirmkey',
                'confirmed', 'confirmedon', 'reg_email_send', ];
            while ($oField = $oFieldList->Next()) {
                if (!in_array($oField->fieldName, $aNotAllowed) && isset($this->sqlData[$oField->fieldName])) {
                    $aData[$oField->fieldName] = $this->sqlData[$oField->fieldName];
                }
            }
        }
        $aData['data_extranet_user_id'] = $this->id;
        $oAddress->LoadFromRow($aData);

        return $oAddress;
    }

    /**
     * send a registration notification mail to the user.
     *
     * @param string $sMailProfileToSend - allows you to overwrite the standard mail profile
     *
     * @return void
     */
    public function SendRegistrationNotification($sMailProfileToSend = null)
    {
        $oExtranetConfig = TdbDataExtranet::GetInstance();
        $oMail = null;
        if (null === $sMailProfileToSend) {
            $oMail = $this->GetRegistrationEmailProfile();
        } else {
            $oMail = TdbDataMailProfile::GetProfile($sMailProfileToSend);
        }
        if (null === $oMail) {
            $this->getFlashMessageService()->addMessage(MTExtranetCore::MSG_CONSUMER_NAME, 'ERROR-MAIL-PROFILE-NOT-DEFINED', ['name' => 'registration']);
        } else {
            $oMail->ChangeToAddress($this->GetUserEMail(), $this->fieldFirstname.' '.$this->fieldLastname);

            $oMail->AddDataArray($this->sqlData);
            // convert address data
            $oSal = $this->GetFieldDataExtranetSalutation();
            $salName = '';
            if (null !== $oSal) {
                $salName = $oSal->GetName();
            }
            $oMail->AddData('data_extranet_salutation_name', $salName);
            $oCountry = $this->GetFieldDataCountry();
            $countryName = '';
            if (null !== $oCountry) {
                $countryName = $oCountry->GetName();
            }
            $oMail->AddData('data_country_name', $countryName);

            $sConfirmLink = $oExtranetConfig->GetConfirmRegistrationURL($this->sqlData['tmpconfirmkey']);
            $confirmlinkHTML = "<a href=\"{$sConfirmLink}\">{$sConfirmLink}</a>";

            $oMail->AddData('confirmlink', $sConfirmLink);
            $oMail->AddData('confirmlinkHTML', $confirmlinkHTML);

            $aAdditionalRegistrationData = $this->GetRegistrationNotificationAdditionalMailData();
            if (is_array($aAdditionalRegistrationData) && count($aAdditionalRegistrationData) > 0) {
                $oMail->AddDataArray($aAdditionalRegistrationData);
            }

            $oMail->SendUsingObjectView('emails', 'Customer');
            $this->fieldRegEmailSend = true;
            $oExtranetUsertableEditorManager = TTools::GetTableEditorManager($this->table, $this->id);
            $oExtranetUsertableEditorManager->AllowEditByAll(true);
            $oExtranetUsertableEditorManager->SaveField('reg_email_send', '1');
        }
    }

    /**
     * Return data to be available in registration email profile.
     *
     * @return array
     */
    protected function GetRegistrationNotificationAdditionalMailData()
    {
        return [];
    }

    /**
     * returns per default an instance of mail profile "registration".
     *
     * @return TdbDataMailProfile|null
     */
    protected function GetRegistrationEmailProfile()
    {
        $oMail = null;
        $oExtranetConfig = TdbDataExtranet::GetInstance();
        if ($oExtranetConfig->fieldUserMustConfirmRegistration) {
            $oMail = TdbDataMailProfile::GetProfile('registration-with-confirmation');
        } else {
            $oMail = TdbDataMailProfile::GetProfile('registration');
        }

        return $oMail;
    }

    /**
     * used to display the user (including edit forms for user data).
     *
     * @param string $sViewName - the view to use
     * @param string $sViewType - where the view is located (Core, Custom-Core, Customer)
     * @param array $aCallTimeVars - place any custom vars that you want to pass through the call here
     *
     * @return string
     */
    public function Render($sViewName = 'standard', $sViewType = 'Core', $aCallTimeVars = [])
    {
        $oView = new TViewParser();

        $oExtranetConfig = TdbDataExtranet::GetInstance();
        $oView->AddVar('oUser', $this);
        $oView->AddVar('oExtranetConfig', $oExtranetConfig);
        $oView->AddVar('aCallTimeVars', $aCallTimeVars);
        $aOtherParameters = $this->GetAdditionalViewVariables($sViewName, $sViewType);
        $oView->AddVarArray($aOtherParameters);

        return $oView->RenderObjectPackageView($sViewName, self::VIEW_PATH, $sViewType);
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
        return [];
    }

    /**
     * return true if the current extranet user owns the record
     * if the record has no owner, then the owner will be set.
     *
     * @return bool
     */
    public function IsOwner()
    {
        $oUser = static::getExtranetUserProvider()->getActiveUser();

        return null !== $oUser && $this->id === $oUser->id;
    }

    /**
     * validates the user data.
     *
     * @param string $sFormDataName - the array name used for the form. send error messages here
     *
     * @return bool
     */
    public function ValidateData($sFormDataName = null)
    {
        if (null === $sFormDataName) {
            $sFormDataName = TdbDataExtranetUser::MSG_FORM_FIELD;
        }
        $bIsValid = true;
        $oMsgManager = $this->getFlashMessageService();

        $aRequiredFields = $this->GetRequiredFields();
        foreach ($aRequiredFields as $sFieldName) {
            $bFieldValid = true;
            if (!array_key_exists($sFieldName, $this->sqlData)) {
                $bFieldValid = false;
            } else {
                $this->sqlData[$sFieldName] = trim($this->sqlData[$sFieldName]);
            }
            if ($bFieldValid && empty($this->sqlData[$sFieldName])) {
                $bFieldValid = false;
            }
            if (!$bFieldValid) {
                $oMsgManager->addMessage($sFormDataName.'-'.$sFieldName, 'ERROR-USER-REQUIRED-FIELD-MISSING');
                $bIsValid = false;
            }
        }

        if (array_key_exists('vat_id', $this->sqlData) && !empty($this->sqlData['vat_id'])) {
            if (false == TTools::IsVatIdValid($this->sqlData['vat_id'], null, $this->sqlData['data_country_id'])) {
                $oMsgManager->addMessage($sFormDataName.'-vat_id', 'ERROR-USER-VAT-ID-INVALID');
                $bIsValid = false;
            }
        }

        return $bIsValid;
    }

    /**
     * Validates login relevant data (username & password). Error messages are sent to $sFormDataName.
     *
     * @param array $aData
     * @param string $sFormDataName
     *
     * @return bool
     */
    public function ValidateLoginData($aData, $sFormDataName = TdbDataExtranetUser::MSG_FORM_FIELD)
    {
        $bIsValid = true;
        $oMessages = TCMSMessageManager::GetInstance();
        if (CHAMELEON_EXTRANET_USER_IS_PORTAL_DEPENDANT) {
            if (!isset($aData['cms_portal_id'])) {
                $oPortal = $this->getPortalDomainService()->getActivePortal();
                if ($oPortal) {
                    $aData['cms_portal_id'] = $oPortal->id;
                }
            }
        }
        $oUser = TdbDataExtranetUser::GetNewInstance($aData);
        $oExtranetConfig = TdbDataExtranet::GetInstance();
        if (!array_key_exists('name', $aData) || empty($aData['name'])) {
            $bIsValid = false;
            $oMessages->AddMessage($sFormDataName.'-name', 'ERROR-USER-LOGIN-NAME-REQUIRED');
        } elseif ($oUser->LoginExists()) {
            $bIsValid = false;
            $aParams = ['forgotPwdLinkStart' => '<a href="'.$oExtranetConfig->GetLinkForgotPasswordPage().'">', 'forgotPwdLinkEnd' => '</a>'];
            $oMessages->AddMessage($sFormDataName.'-name', 'ERROR-USER-EXISTS', $aParams);
        } else {
            if ($oExtranetConfig->fieldLoginIsEmail && false == TTools::IsValidEMail($aData['name'])) {
                $bIsValid = false;
                $oMessages->AddMessage($sFormDataName.'-name', 'ERROR-E-MAIL-INVALID-INPUT');
            }
        }

        $pwd1 = $aData['password'] ?? '';
        $pwd2 = $aData['password2'] ?? '';

        // check pwd field
        if (empty($pwd1)) {
            $oMessages->AddMessage($sFormDataName.'-password1', 'ERROR-USER-REQUIRED-FIELD-MISSING');
        }

        if (empty($pwd2)) {
            $oMessages->AddMessage($sFormDataName.'-password2', 'ERROR-USER-REQUIRED-FIELD-MISSING');
        }

        $sPasswordOk = $this->ValidatePassword($pwd1, $pwd2);
        if (true !== $sPasswordOk) {
            $bIsValid = false;
            $oMessages->AddMessage($sFormDataName.'-password', $sPasswordOk);
        }

        return $bIsValid;
    }

    /**
     * validates new password - returns true on success or message manager code on failure.
     *
     * @param string $sPassword
     * @param string $sPasswordCopy
     *
     * @return bool|string
     */
    public function ValidatePassword($sPassword, $sPasswordCopy)
    {
        if (empty($sPassword)) {
            return 'ERROR-USER-REGISTER-PWD-REQUIRED';
        }

        if (0 != strcmp($sPassword, $sPasswordCopy)) {
            return 'ERROR-USER-REGISTER-PWD-NO-MATCH';
        }

        $passwordLength = \mb_strlen($sPassword);
        if ($passwordLength < $this->getMinimumPasswordLength()) {
            return 'ERROR-USER-REGISTER-PWD-TO-SHORT';
        }

        if ($passwordLength > $this->getMaximumPasswordLength()) {
            return 'ERROR-USER-REGISTER-PWD-TO-LONG';
        }

        return true;
    }

    private function getMinimumPasswordLength(): int
    {
        $definition = $this->getDatabaseAccessLayerFieldConfig()->GetFieldDefinition('data_extranet_user', 'password');
        $minimumLength = $definition->GetFieldtypeConfigKey('minimumLength');
        if (false === \is_numeric($minimumLength)) {
            return TCMSFieldPassword::DEFAULT_MINIMUM_PASSWORD_LENGTH;
        }

        return (int) $minimumLength;
    }

    private function getMaximumPasswordLength(): int
    {
        return PasswordHashGeneratorInterface::MAXIMUM_PASSWORD_LENGTH;
    }

    /**
     * return the newsletter signup entries to which this user is assigned.
     *
     * @return TdbPkgNewsletterUserList
     */
    public function GetNewsletterList()
    {
        $oNewsletterList = $this->GetFromInternalCache('GetNewsletterList');
        if (null === $oNewsletterList) {
            $oNewsletterList = TdbPkgNewsletterUserList::GetListForDataExtranetUserId($this->id);
            $this->SetInternalCache('GetNewsletterList', $oNewsletterList);
        }

        return $oNewsletterList;
    }

    /**
     * we use the post insert hook to set the default customer groups.
     *
     * @return void
     */
    protected function PostInsertHook()
    {
        parent::PostInsertHook();
        $oExtranet = TdbDataExtranet::GetInstance();
        $aDefaultUser = $oExtranet->GetMLTIdList('data_extranet_group', 'data_extranet_group_mlt');
        if (count($aDefaultUser) > 0) {
            $this->UpdateMLT('data_extranet_group_mlt', $aDefaultUser);
        }
    }

    /**
     * check if the password passed is the same as the users password.
     *
     * @param string $sPassword
     *
     * @return bool
     */
    public function PasswordIsUserPassword($sPassword)
    {
        return $this->IsLoginCryptedPasswordCorrect($this->fieldPassword, $sPassword);
    }

    /**
     * validate given alias if nothing is passed $this->sqlData['alias_name'] will be used.
     *
     * @param string|null $sAliasName if not given the value from sqlData will be used
     *
     * @return bool
     */
    public function validateUserAlias($sAliasName = null)
    {
        $bValid = true;
        if (null === $sAliasName) {
            $sAliasName = $this->sqlData['alias_name'];
        }

        $sQuery = "SELECT COUNT(*) as matches
                     FROM `data_extranet_user`
                    WHERE `data_extranet_user`.`alias_name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sAliasName)."'
        ";
        if (null !== $this->id) {
            $sQuery .= " AND `data_extranet_user`.`id` != '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
        }
        if (CHAMELEON_EXTRANET_USER_IS_PORTAL_DEPENDANT) {
            $sQuery .= " AND `data_extranet_user`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sqlData['cms_portal_id'])."'";
        }

        $rResult = MySqlLegacySupport::getInstance()->query($sQuery);
        if (false !== $rResult) {
            $aRow = MySqlLegacySupport::getInstance()->fetch_assoc($rResult);
            if (is_array($aRow) && isset($aRow['matches']) && $aRow['matches'] > 0) {
                // already taken, add error
                $bValid = false;
            }
        }

        return $bValid;
    }

    /**
     * returns the users alias. if no alias is present, then we either return
     * the full name of the user or the initials (depending on the $bProtectedIdentity setting).
     *
     * @param bool $bProtectedIdentity - set to false if you want to allow the full name to be returned when no alias has been set
     *
     * @return string
     */
    public function GetAliasName($bProtectedIdentity = true)
    {
        $sAlias = '';
        if (property_exists($this, 'fieldAliasName')) {
            $sAlias = trim($this->fieldAliasName);
        }
        if (empty($sAlias)) {
            if ($bProtectedIdentity) {
                $sAlias = $this->fieldFirstname;
            } else {
                $sAlias = $this->fieldFirstname.' '.$this->fieldLastname;
            }
        }

        return $sAlias;
    }

    /**
     * Returns true if the user is confirmed.
     *
     * @param array $aUserData - used for the confirmed check
     *                         - if now given or empty the function will use $this->sqlData instead
     *
     * @return bool
     */
    public function IsConfirmedUser($aUserData = [])
    {
        $bIsConfirmedUser = false;
        if (count($aUserData) < 1) {
            $aUserData = $this->sqlData;
        }
        if (array_key_exists('confirmed', $aUserData) && !empty($aUserData['confirmed'])) {
            $bIsConfirmedUser = true;
        }

        return $bIsConfirmedUser;
    }

    /**
     * Handle email change on update user data.
     * Function checks if password is required for email change
     * and set user to unconfirmed if user must confirm registration in extranet config was set to yes.
     *
     * @deprecated please use ChangeUserEmail() in MTExtranet or ChangeEmail() in TdbDataExtranetUser to change email
     * logic moved to PostEmailChange()
     *
     * @param string $sOldEmail
     *
     * @return void
     */
    public function HandleEmailChangeOnUpdateUser($sOldEmail)
    {
        $sNewEmail = $this->GetUserEMail();
        if (!empty($sNewEmail) && $sNewEmail != $sOldEmail) {
            if ($this->IsAllowUserChangeEmail()) {
                $oExtranetConfig = TdbDataExtranet::GetInstance();
                $oMessageManager = $this->getFlashMessageService();
                $sMessageField = 'email';
                if (empty($this->fieldEmail)) {
                    $sMessageField = 'name';
                }
                if ($oExtranetConfig->fieldUserMustConfirmRegistration) {
                    $oMessageManager->addMessage(TdbDataExtranetUser::MSG_FORM_FIELD.'-'.$sMessageField, 'EXTRANET-CHANGED-EMAIL-NEW-CONFIRM');
                } else {
                    $oMessageManager->addMessage(TdbDataExtranetUser::MSG_FORM_FIELD.'-'.$sMessageField, 'EXTRANET-CHANGED-EMAIL');
                }
                $this->PostEmailChange($sNewEmail, $sOldEmail);
            }
        }
    }

    /**
     * This function will be called on email change BEFORE user is saved.
     *
     * @param string $sNewEmail
     * @param string $sOldEmail
     *
     * @return void
     */
    protected function PostEmailChange($sNewEmail, $sOldEmail)
    {
        $oExtranetConfig = TdbDataExtranet::GetInstance();
        if ($oExtranetConfig->fieldUserMustConfirmRegistration) {
            $this->sqlData['confirmed'] = '0';
            $this->sqlData['confirmedon'] = '0000-00-00 00:00:00';
            $this->sqlData['reg_email_send'] = '1';
            $this->sqlData['tmpconfirmkey'] = md5(uniqid((string) rand(), true));
            $this->SendRegistrationNotification();
        }
    }

    /**
     * Returns true if user is allowed to change email
     * overwrite this if you want to add more restrictions. For Example need password to change email.
     *
     * @deprecated see HandleEmailChangeOnUpdateUser() for more information why this is deprecated
     *
     * @return bool
     */
    protected function IsAllowUserChangeEmail()
    {
        $bAllowEmailChange = $this->ChangeEmailIsAllowed();
        if (!$bAllowEmailChange) {
            $oMessageManager = $this->getFlashMessageService();
            $oMessageManager->addMessage(MTExtranetCore::MSG_CONSUMER_NAME, 'ERROR-EXTRANET-LOGIN-REQUIRED');
        }

        return $bAllowEmailChange;
    }

    /**
     * Checks if password change key is valid so user can change his password.
     *
     * @return bool
     */
    public function IsPasswordChangeKeyValid()
    {
        $bIsPasswordChangeKeyValid = true;
        $oExtranetConfig = TdbDataExtranet::GetInstance();
        if ($oExtranetConfig->fieldPasswordChangeKeyTimeValidity > 0) {
            if (!empty($this->fieldPasswordChangeKey)) {
                $sPasswordChangeTimeStamp = strtotime($this->fieldPasswordChangeTimeStamp);
                $sTimeValidityInSeconds = (int) $oExtranetConfig->fieldPasswordChangeKeyTimeValidity * 60 * 60;
                if (($sPasswordChangeTimeStamp + $sTimeValidityInSeconds) < time()) {
                    $bIsPasswordChangeKeyValid = false;
                }
            } else {
                $bIsPasswordChangeKeyValid = false;
            }
        }

        return $bIsPasswordChangeKeyValid;
    }

    /**
     * @return TdbDataExtranetUserProfile|false
     *
     * @psalm-suppress UndefinedDocblockClass - Tdb is used dynamically here: If it exists then it is used, otherwise `false` is returned.
     */
    public function GetFieldDataExtranetUserProfile()
    {
        if (false === class_exists('TdbDataExtranetUserProfile')) {
            return false;
        }
        $oProfile = $this->GetFromInternalCache('oProfile');
        if (is_null($oProfile)) {
            $oProfile = TdbDataExtranetUserProfile::GetNewInstance();
            if (!$oProfile->LoadFromField('data_extranet_user_id', $this->id)) {
                $oProfile = false;
            }
            $this->SetInternalCache('oProfile', $oProfile);
        }

        return $oProfile;
    }

    /**
     * if you want the user have to put in his password on changing field values,
     * overwrite this method and add the field you want to protect by password input to array.
     *
     * @return array
     */
    public static function GetFieldsNeededPasswordChange()
    {
        return [];
    }

    /**
     * method should be called after a new user is registered. NOTE: the method must be
     * called by the controlling class!
     *
     * @return void
     */
    protected function PostRegistrationHook()
    {
        $this->SendRegistrationNotification();
    }

    /**
     * Change a users email-address
     * - uses default validation provided in ValidateLoginData() and ValidateData().
     *
     * @param string $sNewEmail
     * @param string $sPassword - unencrypted password
     * @param string $sCustomConsumer
     *
     * @return bool $bSuccess
     */
    public function ChangeEmail($sNewEmail, $sPassword, $sCustomConsumer = TdbDataExtranetUser::MSG_FORM_FIELD)
    {
        $bSuccess = false;
        $sOldEmail = $this->GetUserEMail();
        if ($sOldEmail !== $sNewEmail && $this->ChangeEmailIsAllowed()) {
            $aData = $this->sqlData;
            $oExtranetConfig = TdbDataExtranet::GetInstance();
            if (!$oExtranetConfig->fieldLoginIsEmail) {
                $aData['email'] = $sNewEmail;
            } else {
                $aData['name'] = $sNewEmail;
            }
            $aData['password'] = $sPassword;
            $aData['password2'] = $sPassword;
            $this->LoadFromRow($aData);
            $bIsValid = $this->ValidateLoginData($aData, $sCustomConsumer);
            $bIsValid = $bIsValid && $this->ValidateData();
            if ($bIsValid) {
                $this->PostEmailChange($sNewEmail, $sOldEmail);
                if (false !== $this->Save()) {
                    $bSuccess = true;
                }
            }
        }

        return $bSuccess;
    }

    /**
     * Returns true if user is allowed to change email.
     *
     * @return bool
     */
    protected function ChangeEmailIsAllowed()
    {
        return $this->IsLoggedIn() || $this->IsOwner();
    }

    /**
     * Set new password for user
     * - uses default validation provided in ValidateLoginData() and ValidateData().
     *
     * @param string $sNewPassword - unencrypted password
     * @param string $sNewPasswordRepeat - we need to verify the new password
     * @param bool $bCheckIfAllowed
     * @param string $sCustomConsumer - overwrites the message consumer name
     *
     * @return bool $bSuccess
     */
    public function ChangePassword($sNewPassword, $sNewPasswordRepeat, $bCheckIfAllowed = true, $sCustomConsumer = TdbDataExtranetUser::MSG_FORM_FIELD)
    {
        $bSuccess = false;
        if (!$bCheckIfAllowed || $this->ChangePasswordIsAllowed()) {
            $aData = $this->sqlData;
            $aData['password'] = $sNewPassword;
            $aData['password2'] = $sNewPasswordRepeat;
            $this->LoadFromRow($aData);
            $bIsValid = $this->ValidateLoginData($aData, $sCustomConsumer);
            $bIsValid = $bIsValid && $this->ValidateData();
            if ($bIsValid) {
                if (!$bCheckIfAllowed) {
                    $this->AllowEditByAll(true);
                }
                if (false !== $this->Save()) {
                    $bSuccess = true;
                }
                if (!$bCheckIfAllowed) {
                    $this->AllowEditByAll(false);
                }
            }
        }

        return $bSuccess;
    }

    /**
     * Returns true if user is allowed to change his password.
     *
     * @return bool
     */
    protected function ChangePasswordIsAllowed()
    {
        return $this->IsLoggedIn() || $this->IsOwner();
    }

    /**
     * checks if a user has the address passed. returns false if not, else returns the address found
     * note: when searching for a match we ignore ID if the address passed has no id.
     *
     * @param TdbDataExtranetUserAddress $oAddress
     *
     * @return TdbDataExtranetUserAddress|bool
     */
    public function FindMatchingAddress($oAddress)
    {
        $oMatchingAddress = false;
        $oAddressList = $this->GetFieldDataExtranetUserAddressList();
        $oAddressList->GoToStart();
        $bIncomingAddressFiltered = false;
        $oIncomingAddress = clone $oAddress;
        while (false === $oMatchingAddress && ($oTmp = $oAddressList->Next())) {
            if (false == $bIncomingAddressFiltered) {
                foreach (array_keys($oIncomingAddress->sqlData) as $sKey) {
                    if (!array_key_exists($sKey, $oTmp->sqlData)) {
                        unset($oIncomingAddress->sqlData[$sKey]);
                    }
                }
                $bIncomingAddressFiltered = true;
            }
            // we ignore
            $oCompare = clone $oTmp;
            if (empty($oIncomingAddress->id)) {
                unset($oCompare->sqlData['id']);
                unset($oCompare->sqlData['cmsident']);
                $oCompare->id = null;
            }
            if ($oCompare->IsSameAs($oIncomingAddress)) {
                $oMatchingAddress = $oTmp;
            }
        }

        return $oMatchingAddress;
    }

    /**
     * send password-email to user with a link where password can be changed.
     *
     * @param string $sTargetModuleSpotName
     *
     * @return bool
     *
     * @throws PasswordGenerationFailedException
     */
    public function SendPasswordUsingSaveMode($sTargetModuleSpotName)
    {
        try {
            $sForgotPasswordKey = \bin2hex(random_bytes(20));
        } catch (Exception $e) {
            throw new PasswordGenerationFailedException($e->getMessage(), (int) $e->getCode(), $e);
        }
        $this->AllowEditByAll(true);

        /**
         * `$bSuccess` is actually an id or false. But through the if-block it'll always be a boolean.
         *
         * @var bool $bSuccess
         */
        $bSuccess = $this->SaveFieldsFast(['password_change_key' => $this->getPasswordHashGenerator()->hash($sForgotPasswordKey), 'password_change_time_stamp' => date('Y-m-d H:i:s')]);
        $this->AllowEditByAll(false);
        if ($bSuccess) {
            $oExtranetConfig = TdbDataExtranet::GetInstance();
            $sLink = $oExtranetConfig->GetPasswordChangeURL($this->fieldName, $sForgotPasswordKey, $sTargetModuleSpotName);
            $bSuccess = $this->SendPasswordEmail('send-password-save-mode', ['link' => $sLink]);
        }

        return $bSuccess;
    }

    /**
     * add userdata and send password-mail using the provided mail profile.
     *
     * @param string $sMailProfileSystemName - systemname of the mail profile to use
     * @param array $aAdditionalMailData - if you want to provide more data in the mail profile
     *
     * @return bool
     */
    public function SendPasswordEmail($sMailProfileSystemName, $aAdditionalMailData = [])
    {
        $bSuccess = false;
        $oMail = TdbDataMailProfile::GetProfile($sMailProfileSystemName);
        if ($oMail) {
            $oMail->AddData('firstname', $this->fieldFirstname);
            $oMail->AddData('lastname', $this->fieldLastname);
            $oMail->AddData('loginname', $this->fieldName);
            $oMail->AddData('login', $this->fieldName);
            $oSalutation = $this->GetFieldDataExtranetSalutation();
            $sTitle = '';
            if ($oSalutation) {
                $sTitle = $oSalutation->GetName();
            }
            $oMail->AddData('title', $sTitle);
            if (count($aAdditionalMailData) > 0) {
                $oMail->AddDataArray($aAdditionalMailData);
            }
            $oMail->AddToAddress($this->GetUserEMail(), $this->fieldFirstname.' '.$this->fieldLastname);
            $bSuccess = $oMail->SendUsingObjectView('emails', 'Customer');
        }

        return $bSuccess;
    }

    /**
     * {@inheritdoc}
     */
    public function LoadFromField($sField, $sValue)
    {
        $bSuccess = false;
        if (CHAMELEON_EXTRANET_USER_IS_PORTAL_DEPENDANT) {
            $oPortal = $this->getPortalDomainService()->getActivePortal();
            if ($oPortal) {
                $aData = [$sField => $sValue];
                $aData['cms_portal_id'] = $oPortal->id;
                $bSuccess = $this->LoadFromFields($aData);
            }
        } else {
            $bSuccess = parent::LoadFromField($sField, $sValue);
        }

        return $bSuccess;
    }

    /**
     * {@inheritdoc}
     */
    public function LoadFromFields($aFieldData, $bBinary = false)
    {
        $bSuccess = false;
        if (CHAMELEON_EXTRANET_USER_IS_PORTAL_DEPENDANT && !isset($aFieldData['cms_portal_id'])) {
            $oPortal = $this->getPortalDomainService()->getActivePortal();
            if ($oPortal) {
                $aFieldData['cms_portal_id'] = $oPortal->id;
                $bSuccess = parent::LoadFromFields($aFieldData, $bBinary);
            }
        } else {
            $bSuccess = parent::LoadFromFields($aFieldData, $bBinary);
        }

        return $bSuccess;
    }

    /**
     * Set a shipping address for the user, no validation of any kind is applied
     * ATTENTION: This should only be used for simulation and does not work if user or address have an id.
     *
     * @param TdbDataExtranetUserAddress $oShippingAddress
     *
     * @return void
     */
    public function setFakedShippingAddressForUser($oShippingAddress)
    {
        if (empty($this->id) && empty($oShippingAddress->id)) {
            $this->oShippingAddress = $oShippingAddress;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function sessionWakeupHook()
    {
        parent::sessionWakeupHook();
        if (true === $this->ValidateSessionData()) {
            $this->isLoggedIn = true;
        } elseif (null !== $this->id && $this->ForceLogoutOnInstanceLoading()) {
            // invalid session but user has id... force logout
            $this->Logout();
        }
    }

    /**
     * @return Request|null
     */
    private function getCurrentRequest()
    {
        return ServiceLocator::get('request_stack')->getCurrentRequest();
    }

    private function getDatabaseAccessLayerFieldConfig(): DatabaseAccessLayerFieldConfig
    {
        return ServiceLocator::get('chameleon_system_core.database_access_layer_field_config');
    }

    /**
     * @return PasswordHashGeneratorInterface
     */
    private function getPasswordHashGenerator()
    {
        return ServiceLocator::get('chameleon_system_core.security.password.password_hash_generator');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return FlashMessageServiceInterface
     */
    private function getFlashMessageService()
    {
        return ServiceLocator::get('chameleon_system_core.flash_messages');
    }

    /**
     * @return ExtranetUserProviderInterface
     */
    private static function getExtranetUserProvider()
    {
        return ServiceLocator::get('chameleon_system_extranet.extranet_user_provider');
    }

    /**
     * @return ShopServiceInterface
     */
    private function getShopService()
    {
        return ServiceLocator::get('chameleon_system_shop.shop_service');
    }
}
