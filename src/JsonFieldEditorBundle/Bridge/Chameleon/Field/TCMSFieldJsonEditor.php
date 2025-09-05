<?php

namespace ChameleonSystem\JsonFieldEditorBundle\Bridge\Chameleon\Field;

use ChameleonSystem\CoreBundle\ServiceLocator;

class TCMSFieldJsonEditor extends \TCMSField
{
    public function GetHTML(): string
    {
        $editorLayout = $this->getFieldTypeConfigKey('layout');
        $viewRenderer = $this->getViewRenderer();

        if (null === $editorLayout) {
            return $viewRenderer->Render('Fields/jsonEditor/jsonEditorInputStandard.html.twig');
        }

        $jsonData = $this->_GetFieldValue();

        if (false === $jsonData) {
            $jsonData = [];
        }

        $jsonData = json_decode($jsonData, true);

        $viewRenderer->AddSourceObject('jsonData', $jsonData);

        return $viewRenderer->Render('Fields/jsonEditor/'.$editorLayout.'.html.twig');
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlFooterIncludes(): array
    {
        $includes = $this->getHtmlHeadIncludes();
        $includes[] = '<script type="text/javascript" src="'.\TGlobal::GetStaticURL(
            '/bundles/chameleonsystemjsonfieldeditor/js/json-editor/jsoneditor.min.js'
        ).'"></script>';

        return $includes;
    }

    /**
     * {@inheritdoc}
     */
    public function GetSQL()
    {
        $sql = $this->ConvertDataToFieldBasedData($this->ConvertPostDataToSQL());

        if (false === json_validate($sql)) {
            return false;
        }

        return $sql;
    }

    protected function getViewRenderer(): \ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }
}
