<?php
    $aTree = array(
        array(
            'bIsActive' => false,
            'bIsExpanded' => true,
            'sLink' => '#test',
            'sTitle' => 'Navi 1',
            'sSeoTitle' => 'Seo Titel 1',
            'aChildren' => array(
                array(
                    'bIsActive' => false,
                    'bIsExpanded' => false,
                    'sLink' => '#test11',
                    'sTitle' => 'Navi 1.1',
                    'sSeoTitle' => 'Seo Titel 1.1',
                    'aChildren' => array(
                    ),
                ),
                array(
                    'bIsActive' => true,
                    'bIsExpanded' => true,
                    'sLink' => '#test12',
                    'sTitle' => 'Navi 1.2',
                    'aChildren' => array(
                    ),
                ),
            ),
        ),
        array(
            'bIsActive' => false,
            'bIsExpanded' => false,
            'sLink' => '#test2',
            'sTitle' => 'Navi 2',
            'sSeoTitle' => 'Seo Titel 2',
            'aChildren' => array(),
        ),
    );

    return array('aTree' => $aTree);
