<?php

namespace App\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ComponentCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
    }
}
