<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\DependencyInjection\Compiler;

use Nadia\Bundle\NadiaMenuBundle\MenuProvider\MenuProvider;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class MenuProviderPass
 */
class MenuProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(MenuProvider::class);
        $definition
            ->setArgument(2, $this->getMenuProviders($definition->getArgument(2), $container));
    }

    /**
     * @param array            $menuProviderConfigs
     * @param ContainerBuilder $container
     *
     * @return array
     *
     * @throws ReflectionException
     */
    private function getMenuProviders(array $menuProviderConfigs, ContainerBuilder $container)
    {
        $return = [];

        foreach ($menuProviderConfigs as $menuName => $callableMethod) {
            if (false !== strpos($callableMethod, '::')) {
                list($className, $methodName) = explode('::', $callableMethod);

                if (class_exists($className)) {
                    $ref = new \ReflectionMethod($className, $methodName);

                    if ($ref->isStatic()) {
                        $return[$menuName] = $callableMethod;
                    } elseif ($container->has($className)) {
                        $return[$menuName] = [new Reference($className), $methodName];
                    } else {
                        $return[$menuName] = [new $className(), $methodName];
                    }
                } else {
                    $return[$menuName] = [new Reference($className), $methodName];
                }
            } else {
                $return[$menuName] = $callableMethod;
            }
        }

        return $return;
    }
}
