<?php

namespace ChameleonSystem\NewsletterBundle\Bridge\Chameleon\TableEditor;

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\NewsletterBundle\Service\NewsletterGroupSubscriberExportService;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Contracts\Translation\TranslatorInterface;

class TableEditorNewsletterGroup extends \TCMSTableEditor
{
    public function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'exportSubscriber';
    }

    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        $menuItem = new \TCMSTableEditorMenuItem();
        $menuItem->sItemKey = 'exportSubscriber';
        $menuItem->setTitle($this->getTranslatorService()->trans('chameleon_system_newsletter_group.action.export_subscriber'));
        $menuItem->sIcon = 'fas fa-file-export';
        $menuItem->sOnClick = 'CHAMELEON.CORE.NewsletterBundleExportSubscriberCsv();';
        $this->oMenuItems->AddItem($menuItem);
    }

    /**
     * @psalm-suppress UndefinedAttributeClass
     */
    #[NoReturn]
    public function exportSubscriber(): void
    {
        $newsletterSubscriberList = $this->getNewsletterGroupSubscriberExportService()->exportSubscriberAsCsv($this->sId);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="newsletter_subscriber.csv"');
        $output = fopen('php://output', 'w');
        foreach ($newsletterSubscriberList as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    public function GetHtmlFooterIncludes()
    {
        $footerIncludes = parent::GetHtmlFooterIncludes();

        $pagDef = $this->getInputFilterUtilService()->getFilteredInput('pagedef', '');

        $urlData = [
            'module_fnc' => ['contentmodule' => 'ExecuteAjaxCall'],
            '_fnc' => 'exportSubscriber', '_noModuleFunction' => 'true',
            'pagedef' => $pagDef,
            'id' => $this->sId,
            'tableid' => $this->oTableConf->id,
        ];
        $ajaxUrl = PATH_CMS_CONTROLLER.'?'.$this->getUrlUtilService()->getArrayAsUrl($urlData, '', '&');

        $footerIncludes[] = "
        <script>
        if (typeof CHAMELEON === 'undefined' || !CHAMELEON) {
            var CHAMELEON = {};
        }
        CHAMELEON.CORE = CHAMELEON.CORE || {};
        CHAMELEON.CORE.NewsletterBundleExportSubscriberCsv = CHAMELEON.CORE.NewsletterBundleExportSubscriberCsv || {};

        CHAMELEON.CORE.NewsletterBundleExportSubscriberCsv = function () {
            fetch('".$ajaxUrl."', {
                method: 'GET'
            })
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'newsletter_subscriber.csv';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => console.error('Error:', error));
        };
        </script>
        ";

        return $footerIncludes;
    }

    private function getTranslatorService(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }

    private function getUrlUtilService(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }

    private function getGlobalService(): \TGlobal
    {
        return ServiceLocator::get('chameleon_system_core.global');
    }

    private function getInputFilterUtilService(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    private function getNewsletterGroupSubscriberExportService(): NewsletterGroupSubscriberExportService
    {
        return ServiceLocator::get('chameleon_system_newsletter.service.newsletter_group_subscriber_export_service');
    }
}
