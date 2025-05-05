<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\DBAL\Connection;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

/**
 * @deprecated since 6.3.0 - use Psr\Log\LoggerInterface in conjunction with Monolog logging instead
 */
class TPkgCmsCoreLogMonologHandler_Database extends AbstractProcessingHandler
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();

        $this->connection = $connection;
    }

    protected function write(array|LogRecord $record): void
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
        $cleanContext = $record['context'];

        if (isset($cleanContext['_cmsRequestDetails'])) {
            $aRequestDetails = $cleanContext['_cmsRequestDetails'];
            unset($cleanContext['_cmsRequestDetails']);
            $sFile = $aRequestDetails['file'];
            if (false !== $rootDir) {
                $sFile = str_replace($rootDir, '', $sFile);
            }
            $requestData = [
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
            ];
        } else {
            $requestData = [];
        }

        foreach ($cleanContext as $key => $value) {
            if ($value instanceof Throwable) {
                $cleanContext[$key] = $this->formatExceptionForContext($value);
            }
        }

        $data = [
            'id' => TTools::GetUUID(),
            'timestamp' => $oDate->getTimestamp(),
            'channel' => $record['channel'],
            'message' => $record['message'],
            'level' => $record['level'],
            'context' => serialize(['context' => $cleanContext, 'extra' => $record['extra']]),
        ];

        $data = array_merge($data, $requestData);

        $this->connection->insert('pkg_cms_core_log', $data);
    }

    private function formatExceptionForContext(Throwable $e): string
    {
        // Taken from \Monolog\Formatter\LineFormatter

        $previousText = '';
        if ($previous = $e->getPrevious()) {
            do {
                $previousText .= ', '.$this->getClass($previous).'(code: '.$previous->getCode().'): '.$previous->getMessage().' at '.$previous->getFile().':'.$previous->getLine();
            } while ($previous = $previous->getPrevious());
        }

        $str = '[object] ('.$this->getClass($e).'(code: '.$e->getCode().'): '.$e->getMessage().' at '.$e->getFile().':'.$e->getLine().$previousText.')';
        $str .= "\n[stacktrace]\n".$e->getTraceAsString()."\n";

        return $str;
    }

    /**
     * @param object $object
     */
    private function getClass($object): string
    {
        $class = \get_class($object);

        return 'c' === $class[0] && 0 === strpos($class, "class@anonymous\0") ? get_parent_class($class).'@anonymous' : $class;
    }
}
