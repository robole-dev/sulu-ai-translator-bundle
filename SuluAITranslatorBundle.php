<?php

/*
 * @todo This file is part of Sulu.
 *
 * (c) robole GbR
 *
 * @todo This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Robole\SuluAiTranslatorBundle

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SuluAiTranslatorBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}

