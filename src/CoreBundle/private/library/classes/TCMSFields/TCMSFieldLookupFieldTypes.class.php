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

class TCMSFieldLookupFieldTypes extends TCMSFieldLookup
{
    /**
     * @var string[]
     */
    protected $fieldHelpTexts = [];

    public function GetHTML()
    {
        $this->GetOptions();
        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('fieldName', $this->name);
        $viewRenderer->AddSourceObject('fieldValue', $this->_GetHTMLValue());
        $viewRenderer->AddSourceObject('options', $this->options);
        $viewRenderer->AddSourceObject('allowEmptySelection', false);
        $viewRenderer->AddSourceObject('connectedRecordId', $this->data);
        $viewRenderer->AddSourceObject('fieldsHelpText', $this->fieldHelpTexts);

        return $viewRenderer->Render('TCMSFieldLookup/fieldLookupFieldTypes.html.twig', null, false);
    }

    public function GetOptions()
    {
        $tblName = $this->GetConnectedTableName();
        $listClass = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $tblName).'List';
        $this->options = [];
        $query = $this->GetOptionsQuery();

        /** @var TCMSRecordList $sourceList */
        $sourceList = call_user_func([$listClass, 'GetList'], $query);

        while ($oRow = $sourceList->Next()) {
            $name = $oRow->GetName();
            if (!empty($name)) {
                $this->options[$oRow->id] = $oRow->GetName();
            }
            $this->fieldHelpTexts[$oRow->id] = $oRow->GetTextField('help_text');
        }
    }

    private function getViewRenderer(): ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }
}
