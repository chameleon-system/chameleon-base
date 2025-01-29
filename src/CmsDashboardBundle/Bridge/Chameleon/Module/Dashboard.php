<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Module;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Attribute\ExposeAsApi;
use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\DashboardModulesProvider;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;

class Dashboard extends \MTPkgViewRendererAbstractModuleMapper
{
    public function __construct(
        private readonly DashboardModulesProvider $provider,
        private readonly SecurityHelperAccess $securityHelperAccess,
        private readonly RequestStack $requestStack,
        private readonly Connection $databaseConnection)
    {
        parent::__construct();
    }

    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        $reload = $this->requestStack->getCurrentRequest()?->query->get('reload', false);
        if ('true' === $reload) {
            $reload = true;
        }

        $oVisitor->SetMappedValue('forceReload', $reload);
        $oVisitor->SetMappedValue('loggedInUserName', $this->getLoggedInUserName());
        $oVisitor->SetMappedValue('cmsOwner', $this->getOwnerName());
        $oVisitor->SetMappedValue('widgetCollections', $this->provider->getWidgetCollections());
        $oVisitor->SetMappedValue('availableCollections', $this->provider->getAvailableCollectionsForUser());
    }

    #[ExposeAsApi(description: 'Call this method dynamically via API:/cms/api/dashboard/widget/{widgetServiceId}/getWidgetHtmlAsJson')]
    public function saveWidgetLayout(array $widgetLayout): void
    {
        $user = $this->securityHelperAccess->getUser();
        if (null === $user) {
            return;
        }

        $query = 'UPDATE `cms_user` SET `dashboard_widget_config` = :layout WHERE `id` = :userId';
        $this->databaseConnection->executeQuery($query, ['layout' => json_encode($widgetLayout), 'userId' => $user->getId()]);
    }

    public function GetHtmlFooterIncludes(): array
    {
        $includes = [];
        $includes[] = '<script src="/bundles/chameleonsystemcmsdashboard/js/dashboard.js"></script>';

        $widgetCollections = $this->provider->getWidgetCollections();
        foreach ($widgetCollections as $widgetCollection) {
            foreach ($widgetCollection as $widgetData) {
                $footerIncludesFromWidget = $widgetData['widget']->getFooterIncludes();
                if (count($footerIncludesFromWidget) > 0) {
                    $includes = array_merge($includes, $footerIncludesFromWidget);
                }
            }
        }

        return $includes;
    }

    protected function getOwnerName(): string
    {
        return \TdbCmsConfig::GetNewInstance('1')->fieldName;
    }

    private function getLoggedInUserName(): string
    {
        $user = $this->securityHelperAccess->getUser();

        if (null === $user) {
            return '';
        }

        $name = $user->getFirstname();
        if ('' !== $name) {
            $name .= ' ';
        }

        $name .= $user->getLastname();

        return $name;
    }
}
