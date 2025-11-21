<?php

namespace ChameleonSystem\NewsletterBundle\Bridge\Chameleon\TCMSFields;

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\NewsletterBundle\Interfaces\NewsletterImportServiceInterface;

class FieldWysiwygNewsletterHtmlImport extends \TCMSFieldWYSIWYG
{
    public function GetHTML()
    {
        $viewRenderer = new \ViewRenderer();

        if (false === $this->getNewsletterWithZipImportEnabled()) {
            return $viewRenderer->Render('TCMSFieldWYSIWYG/EditorWithNewsletterImport/editor.html.twig', null, false);
        }

        $viewRenderer->AddSourceObject('sEditorName', 'fieldcontent_'.$this->sTableName.'_'.$this->name);
        $viewRenderer->AddSourceObject('sFieldName', $this->name);
        $viewRenderer->AddSourceObject('extraPluginsConfiguration', $this->getExtraPluginsConfiguration());
        $viewRenderer->AddSourceObject('aEditorSettings', $this->getEditorSettings());
        $sUserCssUrl = $this->getEditorCSSUrl();
        if ('' !== $sUserCssUrl) {
            $cssStyles = [];
            try {
                $cssStyles = $this->getJSStylesSet($sUserCssUrl);
            } catch (\Exception $e) {
                $viewRenderer->AddSourceObject('couldNotLoadCustomCss', true);
                $viewRenderer->AddSourceObject('customCssUrl', $sUserCssUrl);
            }

            $viewRenderer->AddSourceObject('cssStyles', $cssStyles);
        }
        $viewRenderer->AddSourceObject('data', $this->data);
        $viewRenderer->AddSourceObject('editorHeight', (int) str_replace('px', '', $this->getEditorHeight()));

        $viewRenderer->AddSourceObject('isCalledInModal', $this->isCalledInModal());
        $viewRenderer->AddSourceObject('ajaxUrl', $this->GenerateAjaxURL(['_fnc' => 'importZipFile', '_fieldName' => $this->name]));

        return $viewRenderer->Render('TCMSFieldWYSIWYG/EditorWithNewsletterImport/editor.html.twig', null, false);
    }

    public function importZipFile(): void
    {
        $this->getNewsletterImportService()->importZipFile($this->recordId);
    }

    protected function DefineInterface(): void
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'importZipFile';
    }

    protected function getNewsletterWithZipImportEnabled(): bool
    {
        return (bool) ServiceLocator::getParameter('chameleon_system_newsletter.import_newsletter_from_zip.enabled');
    }

    protected function getNewsletterImportService(): NewsletterImportServiceInterface
    {
        return ServiceLocator::get('chameleon_system_newsletter.service.newsletter_import_service');
    }
}
