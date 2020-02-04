<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Config\Definition\Builder;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class MenuNodeDefinition
 */
class MenuNodeDefinition extends ArrayNodeDefinition
{
    public function __construct(?string $name, NodeParentInterface $parent = null)
    {
        parent::__construct($name, $parent);

        $this->getNodeBuilder()->setNodeClass('menu', MenuNodeDefinition::class);
    }

    /**
     * @param int $depth
     *
     * @return MenuNodeDefinition
     */
    public function addAttributes($depth = 10)
    {
        if (0 === $depth) {
            return $this;
        }

        return $this
            ->beforeNormalization()
                ->ifTrue(function ($v) {
                    return isset($v['child']);
                })
                ->then(function ($v) {
                    return Processor::normalizeConfig($v, 'child', 'children');
                })
            ->end()
            ->arrayPrototype()
                ->fixXmlConfig('option')
                ->fixXmlConfig('child', 'children')
                ->children()
                    ->scalarNode('title')->end()
                    ->arrayNode('options')
                        ->useAttributeAsKey('name')
                        ->variablePrototype()
                            ->beforeNormalization()
                                ->ifTrue(function ($v) {
                                    return is_array($v) && !empty($v['type']);
                                })
                                ->then(function ($v) {
                                    return (new MenuOptionsNormalizer())->normalize($v);
                                })
                            ->end()
                        ->end()
                    ->end()
                    ->node('children', 'menu')
                        ->addAttributes($depth - 1)
                    ->end()
                ->end()
            ->end()
        ;
    }
}
