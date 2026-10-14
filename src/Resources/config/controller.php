<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Robole\SuluAITranslatorBundle\Controller\Admin\TranslationController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(TranslationController::class)
        ->public()
        ->args([
            service('ai_translator.deepl_service'),
            service('sulu_security.security_checker'),
            param('sulu_ai_translator.locale_mapping'),
        ])
        ->tag('sulu.context', ['context' => 'admin'])
        ->tag('controller.service_arguments')
        ->tag('container.service_subscriber')
        ->call('setContainer', [service(ContainerInterface::class)]);
};
