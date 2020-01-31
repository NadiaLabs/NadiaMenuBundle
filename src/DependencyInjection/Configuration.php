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

use Nadia\Bundle\NadiaMenuBundle\Config\Definition\Builder\MenuNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $nodeBuilder = (new NodeBuilder())->setNodeClass('menu', MenuNodeDefinition::class);

        if (version_compare(Kernel::VERSION, '4.3', '<')) {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('nadia_menu', 'array', $nodeBuilder);
        } else {
            $treeBuilder = new TreeBuilder('nadia_menu', 'array', $nodeBuilder);
            $rootNode = $treeBuilder->getRootNode();
        }

        $rootNode
            ->fixXmlConfig('menu')
            ->fixXmlConfig('menu_provider')
            ->children()
                ->arrayNode('cache')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('adapter')->defaultValue('nadia.menu.default_cache_adapter')->end()
                        ->integerNode('ttl')->defaultValue(604800)->end()
                        ->scalarNode('group_key')->defaultValue('_nadia_menu_cache_group')->end()
                        // If enabled, will update cache automatically when menu items are changed.
                        // When this directive is disabled, you must reset cache manually via command `cache:clear`
                        ->booleanNode('check_item_changes')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('menus')
                    ->useAttributeAsKey('name')
                    ->validate()
                        ->ifTrue(function ($v) {
                            return $this->validateMenuNames($v);
                        })
                        ->then(function ($v) {
                            $this->throwInvalidMenuNameException($v);
                        })
                    ->end()
                    ->prototype('menu')->addAttributes()->end()
                ->end()
                ->arrayNode('menu_providers')
                    ->useAttributeAsKey('name')
                    ->validate()
                        ->ifTrue(function ($v) {
                            return $this->validateMenuNames($v);
                        })
                        ->then(function ($v) {
                            $this->throwInvalidMenuNameException($v);
                        })
                    ->end()
                    ->scalarPrototype()
                        ->validate()
                            ->ifTrue(function ($v) {
                                if (false !== strpos($v, '::')) {
                                    return 2 !== count(explode('::', $v));
                                }

                                return !is_callable($v);
                            })
                            ->thenInvalid('The provider %s is not callable! Valid format: "Foo\Bar\FooBar::foobar"')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * @param array $value
     *
     * @return bool
     */
    private function validateMenuNames(array $value)
    {
        foreach (array_keys($value) as $menuName) {
            if (!preg_match('/^[\w\-]+$/', $menuName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $value
     */
    private function throwInvalidMenuNameException(array $value)
    {
        throw new \InvalidArgumentException(
            sprintf(
                'One of the menu names is invalid (%s), ' .
                'valid menu name only include these characters [0-9a-zA-Z_-]',
                json_encode(array_keys($value))
            )
        );
    }
}
