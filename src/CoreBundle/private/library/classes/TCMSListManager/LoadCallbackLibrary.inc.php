<?php

$oldGlobalCallbackPath = _CMS_CORE.'/callback_functions';
$globalCallbackPath = PATH_LIBRARY.'/functions/callback_functions';

$oldCustomerCallbackPath = PATH_CUSTOMER_FRAMEWORK.'/../callback_functions';
$customerCallbackPath = PATH_LIBRARY_CUSTOMER.'/functions/callback_functions';

// load global functions from new location
if (is_dir($globalCallbackPath)) {
    $dir = dir($globalCallbackPath);
    while (false !== ($entry = $dir->read())) {
        if ('gcf_' == substr($entry, 0, 4)) {
            require_once TGlobal::ProtectedPath($globalCallbackPath.'/'.$entry);
        }
    }
    $dir->close();
} else {
    if (is_dir($oldGlobalCallbackPath)) {
        $dir = dir($oldGlobalCallbackPath);
        while (false !== ($entry = $dir->read())) {
            if ('gcf_' == substr($entry, 0, 4)) {
                require_once TGlobal::ProtectedPath($oldGlobalCallbackPath.'/'.$entry);
            }
        }
        $dir->close();
    }
}

// load custom functions from new location
if (is_dir($customerCallbackPath)) {
    $dir = dir($customerCallbackPath);
    while (false !== ($entry = $dir->read())) {
        if ('ccf_' == substr($entry, 0, 4)) {
            require_once TGlobal::ProtectedPath($customerCallbackPath.'/'.$entry);
        }
    }
    $dir->close();
} else {
    // load custom functions from old location
    if (is_dir($oldCustomerCallbackPath)) {
        $dir = dir($oldCustomerCallbackPath);
        while (false !== ($entry = $dir->read())) {
            if ('ccf_' == substr($entry, 0, 4)) {
                require_once TGlobal::ProtectedPath($oldCustomerCallbackPath.'/'.$entry);
            }
        }
        $dir->close();
    }
}
