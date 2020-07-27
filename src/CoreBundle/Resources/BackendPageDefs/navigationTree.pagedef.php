<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$layoutTemplate = 'default';

$moduleList = [
    'contentmodule' => [
        'model' => 'chameleon_system_core.module.navigation_tree',
        'moduleType' => '@CoreBundle',
        'view' => 'standard',
    ],
];

$allowedRights = 'navigation_edit';

addDefaultPageTitle($moduleList);
addDefaultHeader($moduleList);
addDefaultBreadcrumb($moduleList);
addDefaultSidebar($moduleList);
