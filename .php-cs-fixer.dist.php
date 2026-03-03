<?php

$finder = (new PhpCsFixer\Finder)
    ->in(__DIR__)
    ->exclude(['tests']);

$config = new PhpCsFixer\Config();
$config->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'class_definition' => false,
        'concat_space' => ['spacing' => 'one'],
        'ordered_imports' => true,
        'native_function_invocation' => [
            'include' => [],
        ],
        'trailing_comma_in_multiline' => false,
    ])
    ->setFinder($finder);

return $config;
