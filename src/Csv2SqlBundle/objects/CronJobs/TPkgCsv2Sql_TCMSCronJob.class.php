<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use Psr\Log\LoggerInterface;

/**
 * install this job if you want to run the csv imports via cron job.
 * /**/
class TPkgCsv2Sql_TCMSCronJob extends TdbCmsCronjobs
{
    /**
     * @return void
     */
    protected function _ExecuteCron()
    {
        // run all csv2sql jobs
        $oView = new TViewParser();
        $aData = TPkgCsv2SqlManager::ProcessAll();
        $oView->AddVarArray($aData);
        $sResult = $oView->RenderObjectPackageView('vResult', 'pkgCsv2Sql/views/TCMSListManager/TPkgCsv2Sql_CmsListManagerPkgCsv2sql', 'Customer');

        $logger = $this->getCsv2SqlLogger();
        $logger->info('Run cron-job result for: '.$this->sqlData['name']."\n".$sResult);
    }

    private function getCsv2SqlLogger(): LoggerInterface
    {
        return ServiceLocator::get('monolog.logger.csv2sql');
    }
}
