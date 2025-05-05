<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;

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

        $parameter = ['pagedef' => $this->global->GetUserData('pagedef'), 'tableid' => $this->oTableManager->sTableId, 'id' => $this->oTableManager->sId];
        if ('true' == $this->global->GetUserData('bOnlyOneRecord')) {
            $parameter['bOnlyOneRecord'] = 'true';
        }

        $aAdditionalParams = $this->GetHiddenFieldsHook();
        if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
            $parameter = array_merge($parameter, $aAdditionalParams);
        }

        $this->getRedirectService()->redirectToActivePage($parameter);
    }

    private function getRedirectService(): ICmsCoreRedirect
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }
}
