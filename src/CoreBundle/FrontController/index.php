<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../../../../../../src/.bootstrap.php';

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

define('_DEVELOPMENT_MODE', $_SERVER['APP_DEBUG']);

if (true === $_SERVER['APP_DEBUG']) {
    umask(0000);
    Debug::enable();
}

require_once __DIR__.'/chameleon.php';
$chameleon = new chameleon();
$chameleon->boot();

spl_autoload_register('ChameleonSystem\AutoclassesBundle\Loader\AutoClassLoader::loadClassDefinition');

$kernel = new Kernel($_SERVER['APP_ENV'], $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
