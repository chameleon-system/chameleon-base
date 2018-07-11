<?php

function gcf_ExtranetUserProfileUser($name, $row)
{
    $sUserId = $row['data_extranet_user_id'];
    /**
     * @var TDataExtranetUser $oUser
     */
    $oUser = TDataExtranetUser::GetNewInstance();
    $oUser->Load($sUserId);
    $oUserTableConf = $oUser->GetTableConf();

    return $oUser->sqlData[$oUserTableConf->GetNameColumn()];
}
