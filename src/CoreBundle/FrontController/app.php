<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__).'/Resources/config/const.inc.php';

if (array_key_exists('HTTP_X_FORWARDED_HOST', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
}
if ((array_key_exists('HTTPS', $_SERVER) && 'on' === $_SERVER['HTTPS'])
    || (array_key_exists('HTTP_X_FORWARDED_PROTO', $_SERVER) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'])
    || (array_key_exists('HTTP_X_PROTO', $_SERVER) && 'https' === $_SERVER['HTTP_X_PROTO'])
    || (array_key_exists('X_PROTO', $_SERVER) && 'https' === $_SERVER['X_PROTO'])
) {
    $_SERVER['HTTPS'] = 'on';
    define('REQUEST_PROTOCOL', 'https');
} else {
    define('REQUEST_PROTOCOL', 'http');
}

require_once PATH_PROJECT_CONFIG.'/config.inc.php';

require_once __DIR__.'/chameleon.php';
$chameleon = new chameleon();
$chameleon->boot();

spl_autoload_register('ChameleonSystem\AutoclassesBundle\Loader\AutoClassLoader::loadClassDefinition');

require_once PATH_PROJECT_BASE.'/app/AppKernel.php';

$devmode = defined('_DEVELOPMENT_MODE') && _DEVELOPMENT_MODE === true;
$env = $devmode ? 'dev' : 'prod';
if ($devmode) {
    Symfony\Component\ErrorHandler\Debug::enable();
}

$kernel = new AppKernel($env, $devmode);
$request = Request::createFromGlobals();

$response = $kernel->handle($request);

$response->send();
$kernel->terminate($request, $response);
