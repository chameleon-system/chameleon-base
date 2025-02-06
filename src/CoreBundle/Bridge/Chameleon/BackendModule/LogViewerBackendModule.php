<?php

declare(strict_types=1);

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\BackendModule;

use ChameleonSystem\CoreBundle\DataModel\LogViewerItemDataModel;
use ChameleonSystem\CoreBundle\Service\LogViewerService;
use ChameleonSystem\CoreBundle\Service\LogViewerServiceInterface;

class LogViewerBackendModule extends \MTPkgViewRendererAbstractModuleMapper
{
    public function __construct(
        private readonly LogViewerServiceInterface $logViewerService
    ) {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(\IMapperVisitorRestricted $oVisitor, $bCachingEnabled, \IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        $logFiles = [];

        foreach ($this->logViewerService->getLogFiles() as $filename) {
            $filePath = LogViewerService::LOG_DIR.'/'.$filename;

            $fileSize = file_exists($filePath) ? round(filesize($filePath) / 1024, 2).' KB' : 'Unknown';

            $logFiles[] = new LogViewerItemDataModel($filename, $fileSize, date('Y-m-d H:i:s', filemtime($filePath)));
        }

        $oVisitor->SetMappedValue('logFiles', $logFiles);
    }

    public function GetHtmlFooterIncludes(): array
    {
        return ['<script src="/bundles/chameleonsystemcore/javascript/log-viewer/log-viewer.js"></script>'];
    }
}
