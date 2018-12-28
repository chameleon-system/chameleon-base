<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSFieldLookupFieldTypes extends TCMSFieldLookup
{
    protected $fieldsHelpText = [];

    public function GetHTML()
    {
        $this->GetOptions();
        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('fieldName', $this->name);
        $viewRenderer->AddSourceObject('fieldValue', $this->_GetHTMLValue());
        $viewRenderer->AddSourceObject('options', $this->options);
        $viewRenderer->AddSourceObject('allowEmptySelection', false);
        $viewRenderer->AddSourceObject('connectedRecordId', $this->data);
        $viewRenderer->AddSourceObject('fieldsHelpText', $this->fieldsHelpText);

        return $viewRenderer->Render('TCMSFieldLookup/fieldLookupFieldTypes.html.twig', null, false);
    }

    public function GetOptions()
    {
        $tblName = $this->GetConnectedTableName();
        $listClass = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $tblName).'List';
        $this->options = array();
        $query = $this->GetOptionsQuery();

        /** @var TCMSRecordList $sourceList */
        $sourceList = call_user_func(array($listClass, 'GetList'), $query);

        while ($oRow = $sourceList->Next()) {
            $name = $oRow->GetName();
            if (!empty($name)) {
                $this->options[$oRow->id] = $oRow->GetName();
            }
            $this->fieldsHelpText += [$oRow->id => $oRow->GetTextField('help_text')];
        }
    }

    /**
     * @return ViewRenderer
     */
    private function getViewRenderer()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }
}
