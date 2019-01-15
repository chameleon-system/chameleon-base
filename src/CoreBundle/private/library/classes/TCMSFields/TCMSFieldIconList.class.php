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
 * field to select an icon based on a directory icon listing.
/**/
class TCMSFieldIconList extends TCMSField
{
    public function GetHTML()
    {
        $image = $this->_GetHTMLValue();
        $modulePageDef = $this->_GetModulePagedef();
        $iconPath = $this->_GetIconPath();

        $html = '<input type="hidden" id="'.TGlobal::OutHTML($this->name).'" name="'.TGlobal::OutHTML($this->name).'" value="'.TGlobal::OutHTML($image).'" />
      <span id="'.TGlobalBase::OutHTML($this->name)."posDummy\"></span>\n";

        $imagePath = URL_CMS.'/images/spacer.gif';
        if (!empty($image)) {
            $imagePath = URL_CMS.$iconPath.$image;
        }

        $html .= '<div class="badge badge-pill" style="margin-right: 10px;"><img src="'.TGlobal::OutHTML($imagePath).'" id="'.TGlobal::OutHTML($this->name).'_img" border="0" /></div>';

        $url = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript(array('pagedef' => $modulePageDef, 'image' => $image, 'fieldName' => $this->name));
        $html .= TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.field_icon_list.select'), "javascript:CreateModalIFrameDialogCloseButton('{$url}',0,0,'".TGlobal::Translate('chameleon_system_core.field_icon_list.select')."');", URL_CMS.'/images/icons/image.gif');

        return $html;
    }

    protected function _GetIconPath()
    {
        return '/images/nav_icons/';
    }

    protected function _GetModulePagedef()
    {
        return 'iconlist';
    }

    public function _GetHTMLValue()
    {
        return $this->data;
    }

    /**
     * checks if field is mandatory and if field content is valid
     * overwrite this method to add your field based validation
     * you need to add a message to TCMSMessageManager for handling error messages
     * <code>
     * <?php
     *   $oMessageManager = TCMSMessageManager::GetInstance();
     *   $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
     *   $oMessageManager->AddMessage($sConsumerName,'TABLEEDITOR_FIELD_IS_MANDATORY');
     * ?>
     * </code>.
     *
     * @return bool - returns false if field is mandatory and field content is empty or data is not valid
     */
    public function DataIsValid()
    {
        $bDataIsValid = parent::DataIsValid();
        if ($bDataIsValid) {
            $pattern = "/^(.+)\.(gif|jpg|tiff|png)$/";
            if ($this->HasContent() && !preg_match($pattern, $this->data)) {
                $bDataIsValid = false;
                $oMessageManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $sFieldTitle = $this->oDefinition->GetName();
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_ICON_NAME_NOT_VALID', array('sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle));
            }
            $iconPath = $this->_GetIconPath();
            $imagePath = PATH_USER_CMS_PUBLIC.'/blackbox'.$iconPath.$this->data;
            if ($bDataIsValid && $this->HasContent() && !file_exists($imagePath)) {
                $bDataIsValid = false;
                $oMessageManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $sFieldTitle = $this->oDefinition->GetName();
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_ICON_NOT_EXISTS', array('sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle));
            }
        }

        return $bDataIsValid;
    }
}
