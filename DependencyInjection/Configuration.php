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

use Nadia\Bundle\NadiaMenuBundle\Menu\KnpMenuBuilderInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        if (version_compare(Kernel::VERSION, '4.3', '<')) {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('nadia_menu');
        } else {
            $treeBuilder = new TreeBuilder('nadia_menu');
            $rootNode = $treeBuilder->getRootNode();
        }

        $rootNode
            ->fixXmlConfig('knp_menu_builder')
            ->children()
                ->arrayNode('knp_menu_builders')
                    ->useAttributeAsKey('alias')
                    ->scalarPrototype()
                        ->validate()
                            ->ifTrue(function ($v) {
                                return !is_subclass_of($v, KnpMenuBuilderInterface::class);
                            })
                            ->thenInvalid(sprintf(
                                '%%s should implement "%s" interface',
                                KnpMenuBuilderInterface::class
                            ))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
