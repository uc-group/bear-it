<?php

namespace App\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ApiDataValidatorCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     * @throws \ReflectionException
     */
    public function process(ContainerBuilder $container)
    {
        $validatorsDefinition = $container->findDefinition('app.api_data_validators');
        if (!$validatorsDefinition) {
            return;
        }

        $serviceList = [];
        $taggedServices = $container->findTaggedServiceIds('bearit.api_data_validator');
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $tag) {
                $routeName = $tag['route'] ?? $this->getRouteNameFromServiceId($container, $id);
                $serviceList[$routeName] = new Reference($id);
            }
        }

        $validatorsDefinition->setArgument(0, $serviceList);
    }

    /**
     * @param ContainerBuilder $container
     * @param $id
     * @return string|string[]|null
     * @throws \ReflectionException
     */
    private function getRouteNameFromServiceId(ContainerBuilder $container, $id)
    {
        $definition = $container->findDefinition($id);
        $class = $definition->getClass();
        $classParts = explode('\\', $class);
        $count = count($classParts);
        $controller = $classParts[$count - 2];
        $action = str_replace('DataValidator', '', $classParts[$count - 1]);

        return sprintf(
            'api_%s_%s',
            $this->camelCaseToSnakeCase($controller),
            $this->camelCaseToSnakeCase($action)
        );
    }

    /**
     * @param $camelCase
     * @return string
     */
    private function camelCaseToSnakeCase($camelCase)
    {
        $camelCase = strtolower($camelCase[0]) . substr($camelCase, 1);

        return preg_replace_callback('/([A-Z])/', function ($m) {
            return '_' . strtolower($m[1]);
        }, $camelCase);
    }
}