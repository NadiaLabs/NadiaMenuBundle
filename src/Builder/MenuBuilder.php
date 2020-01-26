<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Builder;

/**
 * Class MenuBuilder
 *
 * Usages:
 * <code><pre>
 * $builder = new MenuBuilder();
 * $menu =
 *     $builder
 *         ->addChild('Menu 1')
 *             ->addChild('Menu 1-1', ['route' => 'route-1-1'])->end()
 *             ->addChild('Menu 1-2', ['route' => 'route-1-2'])->end()
 *             ->addChild('Menu 1-3')
 *                 ->addChild('Menu 1-3-1', ['route' => 'route-1-3-1', 'roles' => ['ROLE_XXX']])->end()
 *                 ->addChild('Menu 1-3-2', ['route' => 'route-1-3-2', 'roles' => ['ROLE_YYY']])->end()
 *                 ->addChild('Menu 1-3-3', ['route' => 'route-1-3-3', 'roles' => ['ROLE_ZZZ']])->end()
 *                 ->addChild('Menu 1-3-4', ['route' => 'route-1-3-4', 'roles' => ['ROLE_ABC']])->end()
 *             ->end()
 *             ->addChild('Menu 1-4', ['route' => 'route-1-4'])->end()
 *         ->end()
 *         ->addChild('Menu 2')
 *             ->addChild('Menu 2-1', ['route' => 'route-2-1'])->end()
 *             ->addChild('Menu 2-2', ['route' => 'route-2-2'])->end()
 *             ->addChild('Menu 2-3', ['route' => 'route-2-3', 'generateUri' => false])->end()
 *             ->addChild('Menu 2-4', ['route' => 'route-2-4', 'generateUri' => false])->end()
 *         ->end()
 *         ->addChild('Menu 3')
 *             ->addChild('Menu 3-1', ['route' => 'route-3-1'])->end()
 *             ->addChild('Menu 3-2', ['route' => 'route-3-2'])->end()
 *             ->addChild('Menu 3-3', ['route' => 'route-3-3'])->end()
 *             ->addChild('Menu 3-4', ['route' => 'route-3-4', 'generateUri' => false])->end()
 *         ->end()
 *         ->getMenu()
 * ;
 * </pre></code>
 */
class MenuBuilder
{
    /**
     * @var array Format: <code><pre>
     * {
     *   'data' => [],
     *   'parent' => null,
     *   'children' => [
     *     {
     *       'data' => [
     *         'title' => 'Menu title #1',
     *         'options' => [
     *           'route' => 'admin-dashboard',
     *           'roles' => ['ROLE_XXX'],
     *         ],
     *       ],
     *       'parent' => $this->menu,
     *       'children' => [
     *         {
     *           'data' => [
     *             'title' => 'Menu title #2',
     *             'options' => [
     *               'route' => 'admin-dashboard2',
     *               'roles' => ['ROLE_YYY'],
     *             ],
     *           ],
     *           'parent' => $this->menu->children[0],
     *           'children' => [
     *             ...
     *           ]
     *         },
     *         ...
     *       ]
     *     },
     *     {
     *       'data' => [
     *         'title' => 'Menu title #3',
     *         'options' => [
     *           'route' => 'admin-dashboard3',
     *           'roles' => ['ROLE_ZZZ'],
     *         ],
     *       ],
     *       'parent' => $this->menu,
     *       'children' => [
     *         ...
     *       ]
     *     },
     *     ...
     *   ]
     * ]
     * </pre></code>
     */
    private $menu;

    /**
     * @var array A node pointer that pointing to a menu node
     */
    private $node;

    /**
     * MenuBuilder constructor.
     */
    public function __construct()
    {
        $this->menu = (object) ['data' => [], 'parent' => null, 'children' => []];
        $this->node = $this->menu;
    }

    /**
     * Add child menu item to current node
     *
     * @param string $title   Menu title
     * @param array  $options Menu options
     *
     * @return $this
     */
    public function addChild($title, array $options = [])
    {
        $menu = (object) [
            'data' => [
                'title' => $title,
                'options' => $options,
            ],
            'parent' => $this->node,
            'children' => [],
        ];

        $this->node->children[] = $menu;
        $this->node = $menu;

        return $this;
    }

    /**
     * Set node pointer from current node to its parent node
     *
     * @return $this
     */
    public function end()
    {
        if (!is_null($this->node->parent)) {
            $this->node = $this->node->parent;
        }

        return $this;
    }

    /**
     * Get root menu with array format
     *
     * @return array Format: <code><pre>
     * [
     *   [
     *     'title' => 'Menu title #1',
     *     'options' => [
     *       'route' => 'admin-dashboard',
     *       'roles' => ['ROLE_XXX'],
     *     ],
     *     'children' => [
     *       [
     *         'title' => 'Menu title #2',
     *         'options' => [
     *           'route' => 'admin-dashboard2',
     *           'roles' => ['ROLE_YYY'],
     *         ],
     *         'children' => [],
     *       ],
     *       ...
     *     ],
     *   ],
     *   [
     *     'title' => 'Menu title #3',
     *     'options' => [
     *       'route' => 'admin-dashboard3',
     *       'roles' => ['ROLE_ZZZ'],
     *     ],
     *     'children' => [
     *       ...
     *     ],
     *   ]
     *   ...
     * ]
     * </pre></code>
     */
    public function getMenu()
    {
        $menu = [];

        $toArray = function (&$menu, $node) use (&$toArray) {
            foreach ($node->children as $index => $child) {
                $data = $child->data;

                $menu[$index] = $data;
                $menu[$index]['children'] = [];

                if (!empty($child->children)) {
                    $toArray($menu[$index]['children'], $child);
                }
            }
        };

        $toArray($menu, $this->menu);

        return $menu;
    }
}
