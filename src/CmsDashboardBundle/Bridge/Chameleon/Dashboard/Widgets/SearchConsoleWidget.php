<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Service\DashboardCacheService;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchConsoleWidget extends DashboardWidget
{
    private const WIDGET_NAME = 'widget-search-console';

    public function __construct(
        protected readonly DashboardCacheService $dashboardCacheService,
        protected readonly \ViewRenderer $renderer,
        protected readonly TranslatorInterface $translator,
        protected readonly SecurityHelperAccess $securityHelperAccess
        )
    {
        parent::__construct($dashboardCacheService, $translator);
    }

    public function getTitle(): string
    {
        return $this->translator->trans('chameleon_system_shop.widget.shop_product_status_widget.title');
    }

    public function getDropdownItems(): array
    {
        return [];
    }

    public function getWidgetId(): string
    {
        return self::WIDGET_NAME;
    }

    protected function generateBodyHtml(): string
    {
        $this->renderer->AddSourceObject('reloadEventButtonId', 'reload-'.$this->getWidgetId());

        $renderedTable = $this->renderer->Render('CmsDashboard/search-console-widget.html.twig');

        return "<div>
                    <div class='bg-white'>
                        ".$renderedTable.'
                    </div>
                </div>';
    }
}
