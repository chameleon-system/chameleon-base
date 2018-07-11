<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Monolog\Handler\AbstractProcessingHandler;

class TPkgCmsCoreLogMonologHandler_Database extends AbstractProcessingHandler
{
    protected function write(array $record)
    {
        static $rootDir = null;
        if (null === $rootDir) {
            $rootDir = false;
            if (isset($_SERVER['DOCUMENT_ROOT'])) {
                $rootDir = realpath($_SERVER['DOCUMENT_ROOT'].'/../../');
            }
        }
        /** @var DateTime $oDate */
        $oDate = $record['datetime'];
        $aCleanContext = $record['context'];

        if (isset($aCleanContext['_cmsRequestDetails'])) {
            $aRequestDetails = $aCleanContext['_cmsRequestDetails'];
            unset($aCleanContext['_cmsRequestDetails']);
            $sFile = $aRequestDetails['file'];
            if (false !== $rootDir) {
                $sFile = str_replace($rootDir, '', $sFile);
            }
            $requestData = array(
                'uid' => $aRequestDetails['uid'],
                'session' => $aRequestDetails['session'],
                'file' => $sFile,
                'line' => $aRequestDetails['line'],
                'server' => $aRequestDetails['server'],
                'ip' => $aRequestDetails['ip'],
                'request_url' => $aRequestDetails['requestURL'],
                'referrer_url' => $aRequestDetails['referrerURL'],
                'http_method' => $aRequestDetails['httpMethod'],
                'data_extranet_user_id' => $aRequestDetails['data_extranet_user_id'],
                'cms_user_id' => $aRequestDetails['cms_user_id'],
                'data_extranet_user_name' => $aRequestDetails['data_extranet_user_name'],
            );
        } else {
            $requestData = array();
        }

        $aData = array(
            'id' => TTools::GetUUID(),
            'timestamp' => $oDate->getTimestamp(),
            'channel' => $record['channel'],
            'message' => $record['message'],
            'level' => $record['level'],
            'context' => serialize(array('context' => $aCleanContext, 'extra' => $record['extra'])),
        );

        $aData = array_merge($aData, $requestData);

        $aParts = array();
        foreach ($aData as $field => $val) {
            $aParts[] = "`{$field}` = '".MySqlLegacySupport::getInstance()->real_escape_string($val)."'";
        }
        $sQuery = 'INSERT INTO `pkg_cms_core_log`
                           SET '.implode(",\n", $aParts).'
                  ';
        MySqlLegacySupport::getInstance()->query($sQuery);
    }
}
