<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Menu;

interface KnpMenuBuilderInterface
{
    /**
     * Build a KnpMenu instance.
     *
     * Each menu contains 3 properties:
     *
     * - title: Menu title
     * - options: Menu options, contains those basic options:
     *     - uri
     *     - label
     *     - attributes
     *     - linkAttributes
     *     - childrenAttributes
     *     - labelAttributes
     *     - extras
     *     - current
     *     - display
     *     - displayChildren
     *     - route
     *     - routeParameters
     *     - routeAbsolute
     * - children: Submenus, each sub-menu contains "title", "options", and "children" properties.
     *
     * Example:
     * <code>
     * class ExampleKnpMenuBuilder implements KnpMenuBuilderInterface
     * {
     *   public function build(): KnpMenu
     *   {
     *     $root = new KnpMenu('root');
     *     $root
     *       ->child('Menu #1')
     *         ->child('Menu #1-1')->end()
     *         ->child('Menu #1-2')->end()
     *       ->end()
     *       ->child('Menu #2')->end()
     *     ;
     *     return $root;
     *   }
     * }
     * </code>
     *
     * @return KnpMenu
     */
    public function build(): KnpMenu;
}
