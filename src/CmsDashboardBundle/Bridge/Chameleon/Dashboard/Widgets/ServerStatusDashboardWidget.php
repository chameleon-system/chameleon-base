<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Attribute\ExposeAsApi;
use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Service\DashboardCacheService;
use ChameleonSystem\CmsDashboardBundle\DataModel\DatabaseStatusDataModel;
use ChameleonSystem\CmsDashboardBundle\DataModel\MemcacheStatusDataModel;
use ChameleonSystem\CmsDashboardBundle\DataModel\MemoryUsageDataModel;
use ChameleonSystem\CmsDashboardBundle\DataModel\ServerStatusDataModel;
use ChameleonSystem\CmsDashboardBundle\DataModel\WidgetDropdownItemDataModel;
use ChameleonSystem\CmsDashboardBundle\Library\Constants\CmsGroup;
use ChameleonSystem\SecurityBundle\DataAccess\RightsDataAccessInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use ChameleonSystem\SecurityBundle\Voter\RestrictedByCmsGroupInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class ServerStatusDashboardWidget extends DashboardWidget implements RestrictedByCmsGroupInterface
{
    private const WIDGET_NAME = 'widget-server-status';

    public function __construct(
        protected readonly DashboardCacheService $dashboardCacheService,
        protected readonly \ViewRenderer $renderer,
        protected readonly TranslatorInterface $translator,
        protected readonly SecurityHelperAccess $securityHelperAccess,
        private readonly RightsDataAccessInterface $rightsDataAccess,
        private readonly \TCMSMemcache $memcacheCache,
        private readonly \TCMSMemcache $memcacheCacheSession,
        private readonly Connection $databaseConnection
    ) {
        parent::__construct($dashboardCacheService, $translator);
    }

    public function getTitle(): string
    {
        return $this->translator->trans('chameleon_system_cms_dashboard.widget.server_status.title').' (Host: '.$_SERVER['SERVER_ADDR'] ?? 'Unknown)';
    }

    public function showWidget(): bool
    {
        if (true === $this->securityHelperAccess->isGranted(CmsPermissionAttributeConstants::DASHBOARD_ACCESS, $this)) {
            return true;
        }

        return false;
    }

    public function getDropdownItems(): array
    {
        return [];
    }

    #[ExposeAsApi(description: 'Call this method dynamically via API:/cms/api/dashboard/widget/{widgetServiceId}/getWidgetHtmlAsJson')]
    public function getWidgetHtmlAsJson(): JsonResponse
    {
        $data = [
            'htmlTable' => $this->getBodyHtml(true),
            'dateTime' => date('d.m.Y H:i'),
        ];

        return new JsonResponse(json_encode($data));
    }

    public function getWidgetId(): string
    {
        return self::WIDGET_NAME;
    }

    protected function generateBodyHtml(): string
    {
        $serverData = $this->getServerData();

        $this->renderer->AddSourceObject('serverData', $serverData);
        $this->renderer->AddSourceObject('reloadEventButtonId', 'reload-'.$this->getWidgetId());

        return $this->renderer->Render('CmsDashboard/server-status-widget.html.twig');
    }

    private function getServerData(): ServerStatusDataModel
    {
        return new ServerStatusDataModel(
            PHP_VERSION,
            $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            $this->getMemoryLimit(),
            $this->formatBytes(disk_free_space('/')),
            $this->formatBytes(disk_total_space('/')),
            round((1 - (disk_free_space('/') / disk_total_space('/'))) * 100, 2),
            sys_getloadavg(),
            $this->getMemoryUsage(),
            $this->getMemcacheStats('cache'),
            $this->getMemcacheStats('session'),
            $this->getDataBaseStats()
        );
    }

    private function getMemoryLimit(): string
    {
        try {
            return ini_get('memory_limit');
        } catch (\Throwable $e) {
            return 'Unknown';
        }
    }

    /**
     * @param string $cacheType either 'cache' or 'session'
     */
    private function getMemcacheStats(string $cacheType): MemcacheStatusDataModel
    {
        try {
            if ('cache' === $cacheType) {
                $driver = $this->memcacheCache->getDriver();
            } else {
                $driver = $this->memcacheCacheSession->getDriver();
            }

            if (null !== $driver) {
                $cacheStats = [];

                $stats = $driver->getStats();
                if (count($stats) > 0) {
                    $s = current($stats);
                    $cacheStats['enabled'] = true;
                    $cacheStats['version'] = $s['version'] ?? 'Unknown';
                    $cacheStats['uptime'] = $this->formatUptime((int) ($s['uptime'] ?? 0));
                    $limit = (float) ($s['limit_maxbytes'] ?? 0);
                    $used = (float) ($s['bytes'] ?? 0);
                    $cacheStats['memory_limit'] = $this->formatBytes($limit);
                    $cacheStats['memory_usage'] = $this->formatBytes($used);
                    $cacheStats['memory_usage_percent'] = $limit > 0 ? round(($used / $limit) * 100, 2) : 0;
                }

                return new MemcacheStatusDataModel(
                    $cacheStats['enabled'],
                    $cacheStats['version'],
                    $cacheStats['uptime'],
                    $cacheStats['memory_limit'],
                    $cacheStats['memory_usage'],
                    $cacheStats['memory_usage_percent']
                );
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return new MemcacheStatusDataModel(
            false,
            'Unknown',
            0,
            'N/A',
            'N/A',
            0
        );
    }

    private function formatUptime(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds.' s';
        }

        if ($seconds < 3600) {
            return floor($seconds / 60).' min';
        }

        if ($seconds < 86400) {
            return floor($seconds / 3600).' h';
        }

        return floor($seconds / 86400).' d';
    }

    private function getDataBaseStats(): DatabaseStatusDataModel
    {
        $dbStats = [];
        try {
            // total DB size
            $sql = 'SELECT SUM(data_length + index_length) AS size FROM information_schema.tables WHERE table_schema = DATABASE()';
            $result = $this->databaseConnection->executeQuery($sql)->fetchOne();
            if (is_numeric($result)) {
                $dbStats['total_size'] = $this->formatBytes((float) $result);
            }
            // current connections
            $stmt = $this->databaseConnection->executeQuery("SHOW STATUS LIKE 'Threads_connected'");
            $row = $stmt->fetchAssociative();
            if (isset($row['Value'])) {
                $dbStats['threads_connected'] = (int) $row['Value'];
            }
            // top 5 largest tables
            $sql = 'SELECT table_name, (data_length + index_length) AS size FROM information_schema.tables WHERE table_schema = DATABASE() ORDER BY size DESC LIMIT 5';
            $tables = $this->databaseConnection->executeQuery($sql)->fetchAllAssociative();
            foreach ($tables as $t) {
                $dbStats['top_tables'][] = [
                    'name' => $t['table_name'],
                    'size' => $this->formatBytes((float) $t['size']),
                ];
            }
        } catch (\Throwable $e) {
            // ignore DB errors
            return new DatabaseStatusDataModel(
                'N/A',
                0,
                []
            );
        }

        return new DatabaseStatusDataModel(
            $dbStats['total_size'],
            $dbStats['threads_connected'],
            $dbStats['top_tables']
        );
    }

    private function formatBytes(float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }

    private function getMemoryUsage(): MemoryUsageDataModel
    {
        if (function_exists('shell_exec')) {
            $free = shell_exec('free');
            if (null !== $free) {
                $lines = explode("\n", $free);
                $memory = explode(' ', preg_replace('/\s+/', ' ', $lines[1]));

                return new MemoryUsageDataModel(
                    $this->formatBytes($memory[1] * 1024),
                    $this->formatBytes($memory[2] * 1024),
                    $this->formatBytes($memory[3] * 1024),
                    round(($memory[2] / $memory[1]) * 100, 2),
                );
            }
        }

        return new MemoryUsageDataModel(
            'N/A',
            'N/A',
            'N/A',
            0,
        );
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
            ],
        ];

        return $groups[$qualifier] ?? [];
    }
}
