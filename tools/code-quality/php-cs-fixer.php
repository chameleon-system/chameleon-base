<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
            'src',
        ]
    )
    ->name('*.php')
    ->exclude('**/.idea')
    ->exclude('**/vendor')
    ->notName('*.inc.php') // Migrations
    ->size('< 200K');

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setUsingCache(false)
    ->setRules([
        '@Symfony' => true,
        'single_line_throw' => false, // a change rule, we do not want, because it creates less readable code
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_tag_type' => false, // we do not want e.g. "inheritDoc" to be modified to always be either inline or annotation (clearly incorrect behaviour)
        'no_superfluous_phpdoc_tags' => ['remove_inheritdoc' => false], // some "inheritDoc" are removed even though they are "required"
        'cast_spaces' => ['space' => 'single'],
    ]);
