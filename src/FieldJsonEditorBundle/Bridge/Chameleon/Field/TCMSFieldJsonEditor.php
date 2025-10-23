<?php

namespace ChameleonSystem\FieldJsonEditorBundle\Bridge\Chameleon\Field;

use ChameleonSystem\CoreBundle\ServiceLocator;

class TCMSFieldJsonEditor extends \TCMSField
{
    public function GetHTML(): string
    {
        $editorLayout = $this->getFieldTypeConfigKey('layout');
        $viewRenderer = $this->getViewRenderer();

        $jsonData = $this->_GetFieldValue();

        if (false === $jsonData) {
            $jsonData = null;
        }

        $decodedJsonData = [];
        if (null !== $jsonData && '' !== $jsonData) {
            $decodedJsonData = json_decode((string) $jsonData, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                $decodedJsonData = [];
            }
        }

        $viewRenderer->AddSourceObject('jsonData', $decodedJsonData);
        $viewRenderer->AddSourceObject('fieldName', $this->name);

        if (null === $editorLayout) {
            return $viewRenderer->Render('Fields/FieldJsonEditor/jsonEditorInputStandard.html.twig');
        }

        return $viewRenderer->Render('Fields/FieldJsonEditor/'.$editorLayout.'.html.twig');
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlHeadIncludes(): array
    {
        $includes = $this->getHtmlHeadIncludes();
        $includes[] = '<script type="text/javascript" src="'.\TGlobal::GetStaticURL(
            '/bundles/chameleonsystemfieldjsoneditor/js/json-editor/NanoJSON.js'
        ).'"></script>';
        $includes[] = '<link rel="stylesheet" href="'.\TGlobal::GetStaticURL(
                '/bundles/chameleonsystemfieldjsoneditor/css/NanoJSON.css'
            ).'"></link>';

        return $includes;
    }

    public function DataIsValid()
    {
        $sqlData = $this->ConvertPostDataToSQL();

        if ('' === $sqlData) {
            return true;
        }

        return json_validate($sqlData);
    }

    public function ConvertPostDataToSQL()
    {
        if ('{}' === $this->data) {
            return '';
        }

        return $this->data;
    }

    protected function getViewRenderer(): \ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }
}
