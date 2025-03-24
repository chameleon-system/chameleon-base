<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets\DashboardWidgetInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Doctrine\DBAL\Connection;

final class DashboardModulesProvider
{
    private array $widgets = [];

    public function __construct(
        private readonly SecurityHelperAccess $securityHelperAccess,
        private readonly Connection $databaseConnection
    ) {
    }

    /**
     * Used by the compiler pass to add all tagged dashboard widgets to this provider.
     */
    public function addDashboardWidget(DashboardWidgetInterface $widget, string $id, string $collection = 'default', int $priority = 0): void
    {
        if (false === $widget->showWidget()) {
            return;
        }

        $this->widgets[$collection][] = [
            'id' => $id,
            'widget' => $widget,
            'priority' => $priority,
        ];
    }

    /**
     * Returns the user's widget layout from the database.
     */
    private function getUserWidgetLayout(): array
    {
        $user = $this->securityHelperAccess->getUser();
        if (null === $user) {
            return [];
        }

        $query = 'SELECT `dashboard_widget_config` FROM `cms_user` WHERE `id` = :userId';
        $result = $this->databaseConnection->executeQuery($query, ['userId' => $user->getId()]);

        return json_decode($result->fetchOne(), true) ?? [];
    }

    /**
     * Returns the widget collections, sorted and filtered according to the user's preferences.
     * If the user has no saved layout, return all available widgets.
     *
     * @return array<DashboardWidgetInterface>
     */
    public function getWidgetCollections(?string $collection = null): array
    {
        $userWidgetLayout = $this->getUserWidgetLayout();

        // if the user has no saved layout, return all widgets
        if (empty($userWidgetLayout)) {
            return null === $collection ? $this->widgets : ($this->widgets[$collection] ?? []);
        }

        if (null === $collection) {
            return $this->applyUserSortingAndFiltering($this->widgets, $userWidgetLayout);
        }

        if (!isset($this->widgets[$collection])) {
            return [];
        }

        return $this->applyUserSortingAndFiltering([$collection => $this->widgets[$collection]], $userWidgetLayout)[$collection] ?? [];
    }

    /**
     * Returns the available collections that are not yet in the user's layout.
     * If the user has no saved layout, return all collections.
     */
    public function getAvailableCollectionsForUser(): array
    {
        $userWidgetLayout = $this->getUserWidgetLayout();
        $allCollections = array_keys($this->widgets);

        if (empty($userWidgetLayout)) {
            return $allCollections;
        }

        return array_values(array_diff($allCollections, $userWidgetLayout));
    }

    /**
     * Applies user-defined sorting and filters out non-configured collections.
     */
    private function applyUserSortingAndFiltering(array $collections, array $userWidgetLayout): array
    {
        if (empty($userWidgetLayout)) {
            return $collections;
        }

        $sortedCollections = [];

        // Filter and sort collections based on the user-defined order
        foreach ($userWidgetLayout as $position => $collectionId) {
            if (isset($collections[$collectionId])) {
                $sortedCollections[$collectionId] = $collections[$collectionId];
            }
        }

        return $sortedCollections;
    }
}
