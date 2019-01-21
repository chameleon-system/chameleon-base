<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MTTableEditorComponent extends MTTableEditor
{
    /**
     * does nothing.
     */
    protected function AddURLHistory()
    {
    }

    public function Insert()
    {
        $this->oTableManager->Insert();

        $parameter = array('pagedef' => $this->global->GetUserData('pagedef'), 'tableid' => $this->oTableManager->sTableId, 'id' => $this->oTableManager->sId);
        if ('true' == $this->global->GetUserData('bOnlyOneRecord')) {
            $parameter['bOnlyOneRecord'] = 'true';
        }

        $aAdditionalParams = $this->GetHiddenFieldsHook();
        if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
            $parameter = array_merge($parameter, $aAdditionalParams);
        }

        $this->controller->HeaderRedirect($parameter);
    }
}
