<?php
namespace PhpCsFixer;
return Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2'        => true,
        'array_syntax' => ['syntax' => 'long'],
        'concat_space' => ['spacing' => 'one'],
        'is_null'      => ['use_yoda_style' => true],
        'strict_param' => true,
    ])
    ->setFinder(Finder::create()
        ->in(__DIR__)
        ->exclude('tests')
    )
;