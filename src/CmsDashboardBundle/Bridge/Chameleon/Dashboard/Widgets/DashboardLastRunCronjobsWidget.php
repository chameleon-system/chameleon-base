<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Attribute\ExposeAsApi;
use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Service\DashboardCacheService;
use ChameleonSystem\CmsDashboardBundle\DataModel\WidgetDropdownItemDataModel;
use ChameleonSystem\CoreBundle\CronJob\CronjobStateServiceInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardLastRunCronjobsWidget extends DashboardWidget
{
    private const CRON_JOB_TABLE_NAME = 'cms_cronjobs';
    private const WIDGET_ID = 'widget-last-run-cronjobs';

    public function __construct(
        protected readonly DashboardCacheService $dashboardCacheService,
        protected readonly TranslatorInterface $translator,
        protected readonly CronjobStateServiceInterface $cronjobStateService,
        protected readonly \ViewRenderer $renderer,
        protected readonly SecurityHelperAccess $securityHelperAccess,
    ) {
        parent::__construct($dashboardCacheService, $translator);
    }

    public function getTitle(): string
    {
        return $this->translator->trans('chameleon_system_cms_dashboard.widget.last_run_cronjobs_title');
    }

    public function showWidget(): bool
    {
        if (false === $this->securityHelperAccess->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, self::CRON_JOB_TABLE_NAME)) {
            return false;
        }

        return $this->securityHelperAccess->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, self::CRON_JOB_TABLE_NAME);
    }

    public function getDropdownItems(): array
    {
        $button = new WidgetDropdownItemDataModel(
            'reload-'.$this->getChartId(),
            $this->translator->trans('chameleon_system_ecommerce_stats.widgets.reload_button_label'),
            ''
        );

        $button->addDataAttribute('data-service-alias', $this->getChartId());

        return [$button];
    }

    #[ExposeAsApi(description: 'Call this method dynamically via API:/cms/api/dashboard/widget/{widgetServiceId}/getStatsDataAsJson')]
    public function getWidgetHtmlAsJson(): JsonResponse
    {
        $data = [
            'htmlTable' => $this->getBodyHtml(true),
            'dateTime' => date('d.m.Y H:i'),
        ];

        return new JsonResponse(json_encode($data));
    }

    protected function generateBodyHtml(): string
    {
        $this->renderer->AddSourceObject('runningCronjobDataModels', $this->cronjobStateService->getRunningRunCronJobs());
        $this->renderer->AddSourceObject('lastCronjobDataModels', $this->cronjobStateService->getLastRunCronJobs());
        $this->renderer->AddSourceObject('reloadEventButtonId', 'reload-'.$this->getChartId());

        $renderedTable = $this->renderer->Render('CmsDashboard/cronjob-widget.html.twig');

        return "<div>
                    <div class='bg-white'>
                        ".$renderedTable.'
                    </div>
                </div>';
    }

    public function getChartId(): string
    {
        return self::WIDGET_ID;
    }

    public function getFooterIncludes(): array
    {
        $includes = parent::getFooterIncludes();
        $includes[] = '<script type="text/javascript" src="/bundles/chameleonsystemshop/js/dashboard.js"></script>';

        return $includes;
    }
}
