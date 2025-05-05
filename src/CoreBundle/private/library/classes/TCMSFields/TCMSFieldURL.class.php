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
 * URL.
 * /**/
class TCMSFieldURL extends TCMSFieldVarchar
{
    public function GetHTML()
    {
        $html = parent::GetHTML();
        $html .= "<div style=\"padding-top: 3px;\">\n";

        $buttonTitle = ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_url.open');
        $html .= TCMSRender::DrawButton($buttonTitle, "javascript:CreateModalIFrameDialogCloseButton(document.getElementById('".TGlobal::OutHTML($this->name)."').value,0,0,'".$buttonTitle."')", 'fas fa-globe-americas', 'urlZoom');

        $html .= "</div>\n";

        return $html;
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
            $sURL = $this->data;
            if ('/' == mb_substr($sURL, 0, 1)) {
                // we asume a relative url
                $sURL = 'http://'.$_SERVER['HTTP_HOST'].$sURL;
            }

            $pattern = '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i';
            if ($this->HasContent() && !preg_match($pattern, $sURL)) {
                $bDataIsValid = false;
                $oMessageManager = TCMSMessageManager::GetInstance();
                $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
                $sFieldTitle = $this->oDefinition->GetName();
                $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_URL_NOT_VALID', ['sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle]);
            }
        }

        return $bDataIsValid;
    }
}
