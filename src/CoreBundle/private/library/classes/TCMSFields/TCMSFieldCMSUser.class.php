<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * cms user.
/**/
class TCMSFieldCMSUser extends TCMSFieldExtendedLookup
{
    public function GetHTML()
    {
        $this->allowEmptySelection = false;
        if (empty($this->data)) {
            $oGlobal = TGlobal::instance();
            $this->data = $oGlobal->oUser->id;
        }

        /** @var $oUser TdbCmsUser */
        $oUser = TdbCmsUser::GetNewInstance();
        if ($oUser->Load($this->data)) {
            if ($oUser->oAccessManager->user->IsAdmin()) {
                $html = parent::GetHTML();
            } else {
                $html = $this->GetReadOnly();
            }
        } else {
            $html = parent::GetHTML();
        }

        return $html;
    }

    protected function GetExtendedListButtons()
    {
        $tblName = mb_substr($this->name, 0, -3);
        $oTableConf = new TCMSTableConf();
        $oTableConf->LoadFromField('name', $tblName);

        $sButtonText = TGlobal::Translate('chameleon_system_core.field_cms_user.select_user');
        $html = TCMSRender::DrawButton($sButtonText, 'javascript:'.$this->_GetOpenWindowJS($oTableConf).';', URL_CMS.'/images/icons/box.gif');

        return $html;
    }

    public function GetReadOnly()
    {
        if (empty($this->data)) {
            $oGlobal = TGlobal::instance();
            $this->data = $oGlobal->oUser->id;
        }

        /** @var $oUser TdbCmsUser */
        $oUser = TdbCmsUser::GetNewInstance();
        $oUser->Load($this->data);

        $imageTag = $oUser->GetUserIcon();

        $name = $oUser->sqlData['firstname'].' '.$oUser->sqlData['name'];
        if (!empty($oUser->sqlData['company'])) {
            $name .= ' ['.$oUser->sqlData['company'].']';
        }

        $returnVal = $this->_GetHiddenField()."<div>{$imageTag}".TGlobal::OutHTML($name).'<div class="cleardiv">&nbsp;</div></div>';

        return $returnVal;
    }

    /**
     * return the new charset latin1 so that we get more memory
     * size for a record.
     *
     * @return string
     */
    public function _GetSQLCharset()
    {
        return ' CHARACTER SET latin1 COLLATE latin1_general_ci';
    }
}
