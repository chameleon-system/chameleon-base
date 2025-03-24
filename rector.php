<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        dirname(__DIR__, 3) . '/src'
    ]);


    $rectorConfig->sets([
        SetList::PHP_80,
        SetList::PHP_81,
        SetList::PHP_82,
        SetList::PHP_83,
        SymfonySetList::SYMFONY_50,
        SymfonySetList::SYMFONY_51,
        SymfonySetList::SYMFONY_52,
        SymfonySetList::SYMFONY_53,
        SymfonySetList::SYMFONY_54,
        SymfonySetList::SYMFONY_60,
        SymfonySetList::SYMFONY_61,
        SymfonySetList::SYMFONY_62,
        SymfonySetList::SYMFONY_63,
        SymfonySetList::SYMFONY_64,
    ]);

    $rectorConfig->import(__DIR__ . '/rector-custom-base.php');
    $rectorConfig->import(__DIR__ . '/rector-custom-shop.php');

    $rectorConfig->bootstrapFiles([
        dirname(__DIR__, 3) . '/vendor/autoload.php',
    ]);
};
