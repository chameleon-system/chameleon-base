<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;

class chameleon
{
    /**
     * @var int
     */
    private static $requestType;

    /**
     * @return void
     */
    public function boot()
    {
        if (!array_key_exists('HTTP_HOST', $_SERVER)) {
            echo 'no HTTP_HOST in chameleon.php';
            exit(0);
        }
        mb_internal_encoding('UTF-8');
        $this->InitAutoloader();
        if ('console' === REQUEST_PROTOCOL) {
            $requestType = RequestTypeInterface::REQUEST_TYPE_BACKEND;
        } else {
            $requestType = $this->determineRequestType($_SERVER['REQUEST_URI']);
        }
        self::$requestType = $requestType;
        switch ($requestType) {
            case RequestTypeInterface::REQUEST_TYPE_FRONTEND:
            case RequestTypeInterface::REQUEST_TYPE_ASSETS:
                require_once PATH_CUSTOMER_FRAMEWORK.'/config/config.inc.php';
                break;
            case RequestTypeInterface::REQUEST_TYPE_BACKEND:
                require_once PATH_CORE_CONFIG.'/config.inc.php';
                break;
            default:
                throw new ErrorException('unknown request type', 0, E_USER_ERROR, __FILE__, __LINE__);
                break;
        }
        require_once PATH_CORE_CONFIG.'/defaults.inc.php';

        if (RequestTypeInterface::REQUEST_TYPE_BACKEND === $requestType) {
            TGlobal::setMode(TGlobal::MODE_BACKEND);
        } else {
            TGlobal::setMode(TGlobal::MODE_FRONTEND);
        }

        date_default_timezone_set(CMS_DEFAULT_TIME_ZONE);

        if (true === $this->isInMaintenanceMode($requestType)) {
            $this->clearMaintenanceModeMarkerFileCache();

            if (true === $this->isInMaintenanceMode($requestType)) {
                $this->showMaintenanceModePage();
            }
        }
    }

    /**
     * @return void
     */
    private function InitAutoloader()
    {
        require_once realpath(PATH_PROJECT_BASE.'/vendor/autoload.php');
    }

    /**
     * @param string $requestUri
     *
     * @return int
     */
    protected function determineRequestType($requestUri)
    {
        if (true === $this->isBackendCall($requestUri)) {
            return RequestTypeInterface::REQUEST_TYPE_BACKEND;
        } elseif (true === $this->isAssetCall($requestUri)) {
            return RequestTypeInterface::REQUEST_TYPE_ASSETS;
        } elseif (isset($_SERVER['HTTP_REFERER']) && $this->isSymfonyProfiler($requestUri)) {
            $referrer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
            if ($this->isSymfonyProfiler($referrer)) {
                /*
                 * If the referring URL is also the Symfony profiler, then the user clicked through the profiler panels.
                 * In this case the request type cannot be determined but really doesn't matter. We default to frontend
                 * mode.
                 */
                return RequestTypeInterface::REQUEST_TYPE_FRONTEND;
            } else {
                return $this->determineRequestType($referrer);
            }
        } else {
            return RequestTypeInterface::REQUEST_TYPE_FRONTEND;
        }
    }

    /**
     * do not call directly - use getRequestType instead.
     *
     * @param string $requestUri
     *
     * @return bool
     */
    protected function isBackendCall($requestUri)
    {
        if ('console' === REQUEST_PROTOCOL) {
            return true;
        }

        $requestPath = parse_url($requestUri, PHP_URL_PATH);

        if ('/cms/frontend' === $requestPath) {
            return false;
        }

        return 1 === preg_match('#^/cms($|/|[?])#', $requestUri);
    }

    /**
     * @param string $requestUri
     *
     * @return bool
     */
    private function isAssetCall($requestUri)
    {
        $requestPath = strtolower($requestUri);
        $path = parse_url($requestPath, PHP_URL_PATH);
        if (null === $path) {
            return false;
        }
        $path = substr($path, strrpos($path, '/'));

        return '.css' === substr($path, -4) && '/chameleon_' === substr($path, 0, 11);
    }

    /**
     * @param string $requestUri
     *
     * @return bool
     */
    private function isSymfonyProfiler($requestUri)
    {
        return _DEVELOPMENT_MODE
            && ('/_wdt/' === substr($requestUri, 0, 6)
            || '/_profiler/' === substr($requestUri, 0, 11)
            || '/_configurator/' === substr($requestUri, 0, 15))
        ;
    }

    /**
     * @param int $requestType
     *
     * @psalm-param RequestTypeInterface::REQUEST_TYPE_* $requestType
     *
     * @return bool
     */
    private function isInMaintenanceMode($requestType)
    {
        if (RequestTypeInterface::REQUEST_TYPE_FRONTEND !== $requestType) {
            return false;
        }
        if ($this->isTemplateEngineMode()) {
            return false;
        }

        return file_exists(PATH_MAINTENANCE_MODE_MARKER);
    }

    /**
     * @return bool
     */
    private function isTemplateEngineMode()
    {
        return (isset($_GET['__modulechooser']) && 'true' === $_GET['__modulechooser'])
            || (isset($_POST['__modulechooser']) && 'true' === $_POST['__modulechooser']);
    }

    private function showMaintenanceModePage(): void
    {
        if (\file_exists(PATH_WEB.'/maintenance.php')) {
            require PATH_WEB.'/maintenance.php';

            exit;
        }

        exit('Sorry! This page is down for maintenance.');
    }

    private function clearMaintenanceModeMarkerFileCache(): void
    {
        clearstatcache(true, PATH_MAINTENANCE_MODE_MARKER);
    }

    /**
     * @return int
     */
    public static function getRequestType()
    {
        return self::$requestType;
    }
}
