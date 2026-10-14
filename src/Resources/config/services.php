<?php

declare(strict_types=1);

use Robole\SuluAITranslatorBundle\Admin\AITranslatorAdmin;
use Robole\SuluAITranslatorBundle\Service\DeeplService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('ai_translator.admin', AITranslatorAdmin::class)
        ->args([
            service('sulu_admin.view_builder_factory'),
            service('sulu_security.security_checker'),
        ])
        ->tag('sulu.admin')
        ->tag('sulu.context', ['context' => 'admin']);

    $services->set('ai_translator.deepl_service', DeeplService::class)
        ->public()
        ->args([
            param('sulu_ai_translator.deepl_api_key'),
        ]);
};
