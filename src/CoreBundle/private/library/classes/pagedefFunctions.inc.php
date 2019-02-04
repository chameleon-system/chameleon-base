<?php

function addDefaultPageTitle(array &$moduleList): void
{
    $moduleList['pagetitle'] = [
        'model' => 'MTHeader',
        'view' => 'title',
    ];
}

function addDefaultHeader(array &$moduleList): void
{
    $moduleList['headerimage'] = [
        'model' => 'MTHeader',
        'view' => 'standard',
    ];
}

function addDefaultBreadcrumb(array &$moduleList): void
{
    $moduleList['breadcrumb'] = [
        'model' => 'MTHeader',
        'view' => 'breadcrumb',
    ];
}

function addDefaultSidebar(array &$moduleList): void
{
    $moduleList['sidebar'] = [
        'model' => 'chameleon_system_core.module.sidebar.sidebar_backend_module',
        'moduleType' => '@ChameleonSystemCoreBundle',
        'view' => 'standard',
    ];
}