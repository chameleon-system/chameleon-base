<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\String_\RenameStringRector;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;

return static function (RectorConfig $rectorConfig): void {
    /*
     * Basic string replacements (only in PHP files!)
     */
    // 1. Replace TGlobal::Translate:
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        'TGlobal::Translate(' => "\\ChameleonSystem\\CoreBundle\\ServiceLocator::get('translator')->trans(",
    ]);

    // 2. Adjust the namespace of TranslatorInterface:
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        'Symfony\\Component\\Translation\\TranslatorInterface'
        => 'Symfony\\Contracts\\Translation\\TranslatorInterface',
    ]);

    // 3. Change Twig error routing (Note: Only affects PHP files, YAML files remain unchanged):
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        '@TwigBundle/Resources/config/routing/errors.xml'
        => '@FrameworkBundle/Resources/config/routing/errors.xml',
    ]);

    // 4. Adjust Doctrine methods:
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        'fetchArray'  => 'fetchNumeric',
        'fetchAssoc'  => 'fetchAssociative',
        'fetchAll('   => 'fetchAllAssociative(',
    ]);

    // 5. Adjust RequestStack and KernelEvent methods:
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        'getMasterRequest' => 'getMainRequest',
        'isMasterRequest'  => 'isMainRequest',
    ]);

    // 6. Adjust HttpKernelInterface constant:
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        'HttpKernelInterface::MASTER_REQUEST'
        => 'HttpKernelInterface::MAIN_REQUEST',
    ]);

    // 7. Rename event classes:
    $rectorConfig->ruleWithConfiguration(RenameStringRector::class, [
        'FilterResponseEvent'            => 'ResponseEvent',
        'GetResponseEvent'               => 'RequestEvent',
        'GetResponseForExceptionEvent'   => 'ExceptionEvent',
        'PostResponseEvent'              => 'TerminateEvent',
    ]);
};