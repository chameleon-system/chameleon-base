<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TDataExtranetUserAddressList extends TDataExtranetUserAddressListAutoParent
{
    public const VIEW_PATH = 'pkgExtranet/views/db/TdbDataExtranetUserAddressList';

    /**
     * user id for the current address list. Set by GetUserAddressList.
     *
     * @var string
     */
    protected $iUserId;

    /**
     * return user addresses.
     *
     * @param string $iUserId
     *
     * @return TdbDataExtranetUserAddressList
     */
    public static function GetUserAddressList($iUserId)
    {
        $query = "SELECT `data_extranet_user_address`.*
                  FROM `data_extranet_user_address`
                 WHERE `data_extranet_user_address`.`data_extranet_user_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($iUserId)."'
              ORDER BY `data_extranet_user_address`.`company`, `data_extranet_user_address`.`lastname`, `data_extranet_user_address`.`city`
               ";
        $oList = TdbDataExtranetUserAddressList::GetList($query);
        $oList->SetUserIdForList($iUserId);

        return $oList;
    }

    /**
     * set the user id for the current list. should only be called by GetUserAddressList.
     *
     * @param string $iUserId
     *
     * @return void
     */
    public function SetUserIdForList($iUserId)
    {
        $this->iUserId = $iUserId;
    }

    /**
     * used to display the user address list.
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

        $oView->AddVar('oUserAddresses', $this);
        $oView->AddVar('aCallTimeVars', $aCallTimeVars);

        // get user... if set
        if (!is_null($this->iUserId)) {
            $oUser = TdbDataExtranetUser::GetNewInstance();
            if (!$oUser->Load($this->iUserId)) {
                $oUser = null;
            }
            $oView->AddVar('oUser', $oUser);
        }
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
        $aViewVariables = [];

        return $aViewVariables;
    }
}
