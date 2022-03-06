<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Tests\Menu;

use Nadia\Bundle\NadiaMenuBundle\Menu\KnpMenu;
use PHPUnit\Framework\TestCase;

/**
 * Class MenuBuilderTest
 */
class KnpMenuTest extends TestCase
{
    public function testCase1()
    {
        $expected = [
            'title' => 'root',
            'options' => ['foo' => 'bar'],
            'children' => [],
        ];
        $root = new KnpMenu('root', ['foo' => 'bar']);

        $this->assertEquals($expected, $root->toArray());
    }

    public function testCase2()
    {
        $expected = [
            'title' => 'root',
            'options' => [],
            'children' => [
                [
                    'title' => 'Menu #1',
                    'options' => ['uri' => '/foo'],
                    'children' => [],
                ],
                [
                    'title' => 'Menu #2',
                    'options' => ['uri' => '/bar'],
                    'children' => [],
                ],
                [
                    'title' => 'Menu #3',
                    'options' => ['uri' => '/foobar'],
                    'children' => [],
                ],
            ],
        ];
        $root = new KnpMenu('root');

        $root
            ->child('Menu #1')->uri('/foo')->end()
            ->child('Menu #2')->uri('/bar')->end()
            ->child('Menu #3')->uri('/foobar')->end()
        ;

        $this->assertEquals($expected, $root->toArray());
    }

    public function testCase3()
    {
        $expected = [
            'title' => 'root',
            'options' => [],
            'children' => [
                [
                    'title' => 'Menu #1',
                    'options' => ['uri' => '/foo'],
                    'children' => [
                        [
                            'title' => 'Menu #1-1',
                            'options' => ['route' => 'foo1', 'routeParameters' => [], 'routeAbsolute' => false],
                            'children' => [],
                        ],
                        [
                            'title' => 'Menu #1-2',
                            'options' => [
                                'route' => 'foo2',
                                'routeParameters' => ['foo' => 'foo'],
                                'routeAbsolute' => false,
                            ],
                            'children' => [],
                        ],
                    ],
                ],
                [
                    'title' => 'Menu #2',
                    'options' => ['uri' => '/bar'],
                    'children' => [
                        [
                            'title' => 'Menu #2-1',
                            'options' => [
                                'route' => 'bar1',
                                'routeParameters' => ['foo' => 'foo'],
                                'routeAbsolute' => true,
                            ],
                            'children' => [],
                        ],
                    ],
                ],
                [
                    'title' => 'Menu #3',
                    'options' => ['uri' => '/foobar'],
                    'children' => [
                        [
                            'title' => 'Menu #3-1',
                            'options' => [
                                'extras' => [
                                    'routes' => [
                                        ['route' => 'foobar1', 'routeParameters' => ['foo' => 'foo']],
                                        ['route' => 'foobar2', 'routeParameters' => ['foo' => 'foo2']],
                                    ],
                                ],
                            ],
                            'children' => [],
                        ],
                    ],
                ],
            ],
        ];
        $root = new KnpMenu('root');

        $root
            ->child('Menu #1')->uri('/foo')
                ->child('Menu #1-1')->route('foo1')->end()
                ->child('Menu #1-2')->route('foo2', ['foo' => 'foo'])->end()
            ->end()
            ->child('Menu #2')->uri('/bar')
                ->child('Menu #2-1')->route('bar1', ['foo' => 'foo'], true)->end()
            ->end()
            ->child('Menu #3')->uri('/foobar')
                ->child('Menu #3-1')
                    ->exRoute('foobar1', ['foo' => 'foo'])
                    ->exRoute('foobar2', ['foo' => 'foo2'])
                ->end()
            ->end()
        ;

        $this->assertEquals($expected, $root->toArray());
    }
}
