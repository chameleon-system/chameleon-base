<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets\GoogleAnalytics;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets\DashboardWidget;
use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Service\DashboardCacheService;
use ChameleonSystem\CmsDashboardBundle\DataModel\WidgetDropdownItemDataModel;
use ChameleonSystem\CmsDashboardBundle\Library\Constants\CmsGroup;
use ChameleonSystem\CmsDashboardBundle\Service\GoogleAnalyticsDashboardService;
use ChameleonSystem\SecurityBundle\DataAccess\RightsDataAccessInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use ChameleonSystem\SecurityBundle\Voter\RestrictedByCmsGroupInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EngagementRateWidget extends DashboardWidget implements RestrictedByCmsGroupInterface
{
    private const WIDGET_NAME = 'widget-google-analytics-engagement-rate';

    public function __construct(
        private readonly DashboardCacheService $dashboardCacheService,
        private readonly \ViewRenderer $renderer,
        private readonly TranslatorInterface $translator,
        private readonly SecurityHelperAccess $securityHelperAccess,
        private readonly GoogleAnalyticsDashboardService $googleAnalyticsService,
        private readonly string $googleSearchConsoleAuthJson,
        private readonly string $googleAnalyticsPropertyId,
        private readonly int $googleAnalyticsPeriodDays,
        private readonly RightsDataAccessInterface $rightsDataAccess
    ) {
        parent::__construct($dashboardCacheService, $translator);
    }

    public function getTitle(): string
    {
        return $this->translator->trans(
            'chameleon_system_cms_dashboard.widget.google_analytics.engagement_rate_title',
            [
                '%property%' => $this->googleAnalyticsPropertyId,
                '%days%' => $this->googleAnalyticsPeriodDays,
            ]
        );
    }

    public function getBodyHtml(bool $forceCacheReload = false): string
    {
        $body = $this->generateBodyHtml();

        return $body;
    }

    public function showWidget(): bool
    {
        if ('' === $this->googleSearchConsoleAuthJson || '' === $this->googleAnalyticsPropertyId) {
            return false;
        }

        if (true === $this->securityHelperAccess->isGranted(CmsPermissionAttributeConstants::DASHBOARD_ACCESS, $this)) {
            return true;
        }

        return false;
    }

    public function getDropdownItems(): array
    {
        $dropDownMenuItem = new WidgetDropdownItemDataModel(
            'googleAnalyticsWidget',
            $this->translator->trans('chameleon_system_cms_dashboard.widget.google_analytics_dashboard_link_title'),
            'https://analytics.google.com/analytics/web/#/p'.$this->googleAnalyticsPropertyId
            .'/reports/dashboard?params=_u..nav%3Dmaui%26_r.3..selmet%3D%5B%22newUsers%22%5D&r=business-objectives-examine-user-behavior-overview&ruid=business-objectives-examine-user-behavior-overview,business-objectives,examine-user-behavior&collectionId=business-objectives',
        );

        $dropDownMenuItem->setTarget('_blank');

        return [$dropDownMenuItem];
    }

    public function getWidgetId(): string
    {
        return self::WIDGET_NAME;
    }

    public function getFooterIncludes(): array
    {
        $includes = parent::getFooterIncludes();
        $includes[] = '<script type="text/javascript" src="/bundles/chameleonsystemcmsdashboard/js/chart.4.4.7.js"></script>';
        $includes[] = '<script type="text/javascript" src="/bundles/chameleonsystemcmsdashboard/js/chartjs-adapter-date-fns.3.0.0.js"></script>';
        $includes[] = '<script type="text/javascript" src="/bundles/chameleonsystemcmsdashboard/js/chart-init.4.4.7.js"></script>';

        return $includes;
    }

    protected function generateBodyHtml(): string
    {
        $currentEnd = (new \DateTime('- 1 days'))->format('Y-m-d');
        $currentStart = (new \DateTime('-'.$this->googleAnalyticsPeriodDays.' days'))->format('Y-m-d');
        $previousEnd = (new \DateTime('-'.($this->googleAnalyticsPeriodDays + 1).' days'))->format('Y-m-d');
        $previousStart = (new \DateTime('-'.($this->googleAnalyticsPeriodDays * 2 + 1).' days'))->format('Y-m-d');

        $engagementRate = $this->googleAnalyticsService->getEngagementRate(
            $this->googleAnalyticsPropertyId,
            $currentStart,
            $currentEnd,
            $previousStart,
            $previousEnd
        );

        $sessionDuration = $this->googleAnalyticsService->getSessionDuration(
            $this->googleAnalyticsPropertyId,
            $currentStart,
            $currentEnd,
            $previousStart,
            $previousEnd
        );

        // Pass the data to the view renderer
        $this->renderer->AddSourceObject('dayPeriod', $this->googleAnalyticsPeriodDays);
        $this->renderer->AddSourceObject('engagementRate', $engagementRate);
        $this->renderer->AddSourceObject('sessionDuration', $sessionDuration);

        $renderedTable = $this->renderer->Render('CmsDashboard/google-analytics/engagement-rate-widget.html.twig');

        return "<div>
                    <div class='bg-white'>
                        ".$renderedTable.'
                    </div>
                </div>';
    }

    public function getPermittedGroups(?string $qualifier = null): array
    {
        $groupSystemNames = $this->getPermittedGroupSystemNames($qualifier);

        $groupIds = [];
        foreach ($groupSystemNames as $groupSystemName) {
            $groupId = $this->rightsDataAccess->getGroupIdBySystemName($groupSystemName);
            if (null !== $groupId) {
                $groupIds[] = $groupId;
            }
        }

        return $groupIds;
    }

    protected function getPermittedGroupSystemNames(?string $qualifier): array
    {
        $groups = [
            CmsPermissionAttributeConstants::DASHBOARD_ACCESS => [
                CmsGroup::CMS_ADMIN,
                CmsGroup::CMS_MANAGEMENT,
            ],
        ];

        return $groups[$qualifier] ?? [];
    }
}
