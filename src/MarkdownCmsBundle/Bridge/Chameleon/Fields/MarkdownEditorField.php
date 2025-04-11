<?php

namespace ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Fields;

use ChameleonSystem\CmsStringUtilitiesBundle\Interfaces\UrlUtilityServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Symfony\Component\Security\Core\User\UserInterface;

class MarkdownEditorField extends \TCMSFieldText
{
    public function GetHTML()
    {
        $html = $this->renderInputField();
        $html .= $this->renderMarkdownEditor(false);

        return $html;
    }

    /**
     * {@inheritDoc}
     */
    public function GetReadOnly()
    {
        $html = $this->renderInputField();
        $html .= $this->renderMarkdownEditor(true);

        return $html;
    }

    /**
     * {@inheritDoc}
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $includes = parent::GetCMSHtmlHeadIncludes();
        $includes[] = '<link href="'.$this->getGlobal()->GetStaticURL('/bundles/chameleonsystemmarkdowncms/css/toastuimarkdowneditor/toastui-editor.min.css').'" rel="stylesheet" />';
        $includes[] = '<link href="'.$this->getGlobal()->GetStaticURL('/bundles/chameleonsystemmarkdowncms/css/toastuimarkdowneditor/toastui-editor-plugin-table-merged-cell.min.css').'" rel="stylesheet" />';

        return $includes;
    }

    /**
     * {@inheritDoc}
     */
    public function GetCMSHtmlFooterIncludes()
    {
        $includes = parent::GetCMSHtmlFooterIncludes();
        $includes[] = '<script src="'.$this->getGlobal()->GetStaticURL('/bundles/chameleonsystemmarkdowncms/js/toastuimarkdowneditor/toastui-editor-all.min.js').'"></script>';
        $includes[] = '<script src="'.$this->getGlobal()->GetStaticURL('/bundles/chameleonsystemmarkdowncms/js/toastuimarkdowneditor/i18n/de-de.min.js').'"></script>';
        $includes[] = '<script src="'.$this->getGlobal()->GetStaticURL('/bundles/chameleonsystemmarkdowncms/js/toastuimarkdowneditor/toastui-editor-plugin-table-merged-cell.min.js').'"></script>';
        $includes[] = '<script src="'.$this->getGlobal()->GetStaticURL('/bundles/chameleonsystemmarkdowncms/js/toastuimarkdowneditor/toastui-init.js').'"></script>';

        return $includes;
    }

    protected function renderMarkdownEditor(bool $readonly): string
    {
        $editorId = 'markdownEditor'.$this->name;
        $name = $this->name;

        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('editorId', $editorId);
        $viewRenderer->AddSourceObject('name', $name);
        $viewRenderer->AddSourceObject('readonly', $readonly);
        $viewRenderer->AddSourceObject('toastUiConfiguration', $this->getToastUIConfiguration($readonly));
        $viewRenderer->AddSourceObject('modalLink', $this->generateModalLink($editorId));
        $viewRenderer->AddSourceObject('linkSourceTableData', $this->getLinkSourceTableData());

        return $viewRenderer->Render('markdown-editor.html.twig', null, false);
    }

    public function generateModalLink(string $editorId): string
    {
        return $this->getUrlUtilService()->addParameterToUrl(PATH_CMS_CONTROLLER.'?',
            [
                'pagedef' => 'recordListLookupWithMarkdownCallback',
                '_pagedefType' => '@ChameleonSystemMarkdownCmsBundle',
                'editorId' => $editorId,
            ]
        );
    }

    protected function getToastUIConfiguration(bool $readonly)
    {
        return [
            'el' => '',
            'viewer' => $readonly,
            'height' => '500px',
            'initialEditType' => 'markdown',
            'previewStyle' => 'tab',
            'hideModeSwitch' => true,
            'toolbarItems' => $this->getToolbarItems(),
            'language' => $this->getBackendLanguageCode6391(),
            'plugins' => [],
        ];
    }

    protected function renderInputField(): string
    {
        return sprintf('<input type="hidden" id="%s" name="%s" value="%s">',
            \TGlobal::OutHTML($this->name),
            \TGlobal::OutHTML($this->name),
            \TGlobal::OutHTML($this->data)
        );
    }

    protected function getToolbarItems(): array
    {
        return [
            ['heading', 'bold', 'italic', 'strike'],
            ['hr', 'quote'],
            ['ul', 'ol', 'task', 'indent', 'outdent'],
            ['table', 'link'],
            ['codeblock'],
        ];
    }

    protected function getLinkSourceTableNames(): array
    {
        return [
            'pkg_article',
            'cms_tpl_page',
            'shop_article',
            'cms_document',
        ];
    }

    protected function getLinkSourceTableData(): array
    {
        $linkableTables = $this->getLinkSourceTableNames();

        $tableData = [];
        foreach ($linkableTables as $tableName) {
            $tableConf = \TdbCmsTblConf::GetNewInstance();
            if (false !== $tableConf->LoadFromField('name', $tableName)) {
                $tableData[$tableConf->id] = $tableConf->GetName();
            }
        }

        return $tableData;
    }

    private function getBackendLanguageCode6391(): string
    {
        $backendUser = $this->getCmsUser();

        if (null === $backendUser) {
            return 'en';
        }

        $backendLanguageId = $backendUser->getCmsLanguageId();

        if ('' === $backendLanguageId) {
            return 'en';
        }

        $backendLanguage = \TdbCmsLanguage::GetNewInstance();
        if (false === $backendLanguage->Load($backendLanguageId)) {
            return 'en';
        }

        $backendLanguageIsoCode = $backendLanguage->fieldIso6391;

        $supportedBackendLanguages = $this->getSupportedEditorLanguages();

        if (!\in_array($backendLanguageIsoCode, $supportedBackendLanguages, true)) {
            return 'en';
        }

        return $backendLanguageIsoCode;
    }

    private function getSupportedEditorLanguages(): array
    {
        return ['en', 'de'];
    }

    private function getGlobal(): \TGlobal
    {
        return ServiceLocator::get('chameleon_system_core.global');
    }

    private function getUrlUtilService(): UrlUtilityServiceInterface
    {
        return ServiceLocator::get('chameleon_system_cms_string_utilities.url_utility_service');
    }

    private function getViewRenderer(): \ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }

    private function getCmsUser(): UserInterface|CmsUserModel|null
    {
        return ServiceLocator::get(SecurityHelperAccess::class)->getUser();
    }
}
