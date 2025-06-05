<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Service\DashboardCacheService;
use ChameleonSystem\CmsDashboardBundle\DataModel\WidgetDropdownItemDataModel;
use ChameleonSystem\CmsDashboardBundle\Library\Constants\CmsGroup;
use ChameleonSystem\CmsDashboardBundle\Service\GoogleSearchConsoleService;
use ChameleonSystem\SecurityBundle\DataAccess\RightsDataAccessInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use ChameleonSystem\SecurityBundle\Voter\RestrictedByCmsGroupInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchConsoleWidget extends DashboardWidget implements RestrictedByCmsGroupInterface
{
    public const string WIDGET_ID = 'widget-search-console';

    public function __construct(
        private readonly DashboardCacheService $dashboardCacheService,
        private readonly \ViewRenderer $renderer,
        private readonly TranslatorInterface $translator,
        private readonly SecurityHelperAccess $securityHelperAccess,
        private readonly GoogleSearchConsoleService $googleSearchConsoleService,
        private readonly string $googleSearchConsoleAuthJson,
        private readonly string $googleSearchConsoleDomainProperty,
        private readonly int $googleSearchConsolePeriodDays,
        private readonly RightsDataAccessInterface $rightsDataAccess
    ) {
        parent::__construct($dashboardCacheService, $translator);
    }

    public function getTitle(): string
    {
        return $this->translator->trans(
            'chameleon_system_cms_dashboard.widget.search_console_title',
            [
                '%domain%' => $this->googleSearchConsoleDomainProperty,
                '%days%' => $this->googleSearchConsolePeriodDays,
            ]
        );
    }

    public function showWidget(): bool
    {
        if ('' === $this->googleSearchConsoleAuthJson || '' === $this->googleSearchConsoleDomainProperty) {
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
            'searchConsoleWidget',
            $this->translator->trans(
                'chameleon_system_cms_dashboard.widget.search_console_dropdown_menu_console_link_title'
            ),
            'https://search.google.com/search-console/performance/search-analytics?resource_id=sc-domain%3A'
            .$this->googleSearchConsoleDomainProperty
            .'&hl=de&num_of_days=28&compare_date=PREV&metrics=CLICKS%2CIMPRESSIONS'
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
        $currentEnd = (new \DateTime())->format('Y-m-d');
        $currentStart = (new \DateTime('-'.$this->googleSearchConsolePeriodDays.' days'))->format('Y-m-d');
        $previousEnd = (new \DateTime('-'.($this->googleSearchConsolePeriodDays + 1).' days'))->format('Y-m-d');
        $previousStart = (new \DateTime('-'.($this->googleSearchConsolePeriodDays * 2 + 1).' days'))->format('Y-m-d');

        $comparisonData = $this->googleSearchConsoleService->getComparisonData(
            'sc-domain:'.$this->googleSearchConsoleDomainProperty,
            $currentStart,
            $currentEnd,
            $previousStart,
            $previousEnd
        );

        $this->renderer->AddSourceObject('dayPeriod', $this->googleSearchConsolePeriodDays);
        $this->renderer->AddSourceObject('searchConsoleCurrentData', $comparisonData['current']);
        $this->renderer->AddSourceObject('searchConsolePreviousData', $comparisonData['previous']);
        $this->renderer->AddSourceObject('searchConsoleTopImprovedQueries', $comparisonData['topImprovedQueries']);

        $renderedTable = $this->renderer->Render('CmsDashboard/search-console-widget.html.twig');

        return "<div>
                    <div class='bg-white'>
                        ".$renderedTable.'
                    </div>
                </div>';
    }

    /**
     * method is used by SecurityHelperAccess to check if the user has the required rights to see the widget.
     */
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
