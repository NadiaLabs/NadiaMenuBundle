<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\DependencyInjection;

use Nadia\Bundle\NadiaMenuBundle\Menu\KnpMenuFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class NadiaMenuExtension
 */
class NadiaMenuExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config/');
        $loader  = new YamlFileLoader($container, $locator);

        $loader->load('services.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['knp_menu_builders'] as $alias => $className) {
            $builderId = 'nadia_menu.knp_menu_builder.' . $alias;

            $container->setDefinition($builderId, new Definition($className));

            $menuDefinition = (new Definition('Knp\Menu\MenuItem'))
                ->setFactory([new Reference(KnpMenuFactory::class), 'create'])
                ->setArgument(0, new Reference($builderId))
                ->addTag('knp_menu.menu', ['alias' => $alias])
            ;

            $container->setDefinition('nadia_menu.knp_menu.' . $alias, $menuDefinition);
        }
    }
}
