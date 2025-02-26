<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\String_\RenameStringRector;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;

return static function (RectorConfig $rectorConfig): void {
    /*
     * Basis-String-Ersetzungen (nur in PHP-Dateien!)
     */
    // 1. TGlobal::Translate ersetzen:
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        'TGlobal::Translate(' => "ServiceLocator::get('translator')->trans(",
    ]);

    // 2. Namespace von TranslatorInterface anpassen:
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        'Symfony\\Component\\Translation\\TranslatorInterface'
        => 'Symfony\\Contracts\\Translation\\TranslatorInterface',
    ]);

    // 3. Twig Error Routing ändern (Hinweis: Greift nur in PHP-Dateien, YAML-Dateien bleiben unverändert):
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        '@TwigBundle/Resources/config/routing/errors.xml'
        => '@FrameworkBundle/Resources/config/routing/errors.xml',
    ]);

    // 4. Doctrine-Methoden anpassen:
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        'fetchArray'  => 'fetchNumeric',
        'fetchAssoc'  => 'fetchAssociative',
        'fetchAll('   => 'fetchAllAssociative(',
    ]);

    // 5. RequestStack- und KernelEvent-Methoden anpassen:
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        'getMasterRequest' => 'getMainRequest',
        'isMasterRequest'  => 'isMainRequest',
    ]);

    // 6. HttpKernelInterface-Konstante anpassen:
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        'HttpKernelInterface::MASTER_REQUEST'
        => 'HttpKernelInterface::MAIN_REQUEST',
    ]);

    // 7. Event-Klassen umbenennen:
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        'FilterResponseEvent'            => 'ResponseEvent',
        'GetResponseEvent'               => 'RequestEvent',
        'GetResponseForExceptionEvent'   => 'ExceptionEvent',
        'PostResponseEvent'              => 'TerminateEvent',
    ]);

    /*
     * Basis-Methodenumbenennung:
     */
    // 8. InputFilterUtil-Methode umbenennen:
    $rectorConfig->ruleWithConfiguration(RenameMethodRector::class, [
        new MethodCallRename(
            'ChameleonSystem\\CoreBundle\\Util\\InputFilterUtil',
            'getFilteredGetInput',
            'getFilteredGetInputArray'
        ),
    ]);
};
