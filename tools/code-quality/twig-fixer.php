<?php

$ruleset = new TwigCsFixer\Ruleset\Ruleset();

$ruleset->addStandard(new TwigCsFixer\Standard\TwigCsFixer());

$ruleset->removeRule(TwigCsFixer\Rules\Whitespace\BlankEOFRule::class);
$ruleset->removeRule(TwigCsFixer\Rules\Punctuation\PunctuationSpacingRule::class);
$ruleset->removeRule(TwigCsFixer\Rules\Delimiter\DelimiterSpacingRule::class);

$config = new TwigCsFixer\Config\Config();
$config->setRuleset($ruleset);

$finder = (new PhpCsFixer\Finder())
    ->in(['tools'])
    //->in(getcwd())
    // exclude etc. operate relative to the in() path
        // disable for now - it throws a lot of errors
    //->in([
    //        'packages/common-bundle',
    //        'packages/ap-plus-bundle',
    //        'packages/multi-channel-voucher-bundle',
    //        'schafferer-eh/src',
    //        'schafferer-gh/src',
    //        'tischwelt/src',
    //    ]
    //)
    ->exclude('**/.idea')
    ->exclude('**/vendor')
    ->name('*.twig')

    ->size('< 200K');

$config->setFinder($finder);

return $config;
