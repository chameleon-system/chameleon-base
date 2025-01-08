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

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\DashboardModulesProvider;

// Dashboard is the main module that renders all dashboard modules inside the dashboard page in the backend
final class Dashboard extends \MTPkgViewRendererAbstractModuleMapper
{
    public function __construct(private readonly DashboardModulesProvider $provider)
    {
        parent::__construct();
    }

    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        $oVisitor->SetMappedValue('greeting', 'Hello World from Dashboard');
        $oVisitor->SetMappedValue('widgetCollections', $this->provider->getWidgetCollections());
    }

    public function GetHtmlFooterIncludes(): array
    {
        $includes = [];
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
}
