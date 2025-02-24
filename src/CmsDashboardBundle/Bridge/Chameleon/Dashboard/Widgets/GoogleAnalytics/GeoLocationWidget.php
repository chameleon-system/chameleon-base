<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets\GoogleAnalytics;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Service\DashboardCacheService;
use ChameleonSystem\CmsDashboardBundle\DataModel\WidgetDropdownItemDataModel;
use ChameleonSystem\CmsDashboardBundle\Service\GoogleAnalyticsDashboardService;
use ChameleonSystem\SecurityBundle\DataAccess\RightsDataAccessInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\RestrictedByCmsGroupInterface;
use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets\DashboardWidget;
use Symfony\Contracts\Translation\TranslatorInterface;

class GeoLocationWidget extends DashboardWidget implements RestrictedByCmsGroupInterface
{
    private const WIDGET_NAME = 'widget-google-analytics-geo-location';

    private const WIDGET_AS_MAP = false;

    private const MAX_LOCATION_ELEMENTS = 10;

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
            'chameleon_system_cms_dashboard.widget.google_analytics.geo_location_title',
            [
                '%property%' => $this->googleAnalyticsPropertyId,
                '%days%' => $this->googleAnalyticsPeriodDays
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

        foreach ($this->getPermittedGroupSystemNames() as $groupSystemName) {
            if (true === $this->securityHelperAccess->isGranted($groupSystemName, $this)) {
                return true;
            }
        }

        return false;
    }

    public function getDropdownItems(): array
    {
        $dropDownMenuItem = new WidgetDropdownItemDataModel(
            'googleAnalyticsGeoLocationWidget',
            $this->translator->trans('chameleon_system_cms_dashboard.widget.google_analytics_dashboard_link_title'),
            'https://analytics.google.com/analytics/web/#/report-home/a'.$this->googleAnalyticsPropertyId
        );

        $dropDownMenuItem->setTarget('_blank');

        return [$dropDownMenuItem];
    }

    public function getWidgetId(): string
    {
        return self::WIDGET_NAME;
    }

    protected function generateBodyHtml(): string
    {
        $currentEnd = (new \DateTime('- 1 days'))->format('Y-m-d');
        $currentStart = (new \DateTime('-'.$this->googleAnalyticsPeriodDays.' days'))->format('Y-m-d');

        $geoLocation = $this->googleAnalyticsService->getGeoLocation(
            $this->googleAnalyticsPropertyId,
            $currentStart,
            $currentEnd
        );

        $geoLocation = array_splice($geoLocation, 0, self::MAX_LOCATION_ELEMENTS);

        // Pass the data to the view renderer
        $this->renderer->AddSourceObject('dayPeriod', $this->googleAnalyticsPeriodDays);
        $this->renderer->AddSourceObject('geoLocation', $geoLocation);

        $renderTemplate = 'CmsDashboard/google-analytics/geo-location-widget.html.twig';
        if(true === self::WIDGET_AS_MAP) {
            $renderTemplate = 'CmsDashboard/google-analytics/geo-location-map-widget.html.twig';;
        }

        $renderedTable = $this->renderer->Render($renderTemplate);

        return "<div>
                    <div class='bg-white'>
                        ".$renderedTable.'
                    </div>
                </div>';
    }

    public function getPermittedGroups(?string $qualifier = null): array
    {
        $groupSystemNames = $this->getPermittedGroupSystemNames();

        $groupIds = [];
        foreach ($groupSystemNames as $groupSystemName) {
            $groupId = $this->rightsDataAccess->getGroupIdBySystemName($groupSystemName);
            if (null !== $groupId) {
                $groupIds[] = $groupId;
            }
        }

        return $groupIds;
    }

    protected function getPermittedGroupSystemNames(): array
    {
        return [
            'CMS_GROUP_CMS_MANAGEMENT',
            'CMS_GROUP_CMS_ADMIN',
        ];
    }
}
