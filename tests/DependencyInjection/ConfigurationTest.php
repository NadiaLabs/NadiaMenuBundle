<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Tests\DependencyInjection;

use Nadia\Bundle\NadiaMenuBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends TestCase
{
    public function testDefaultConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), []);

        $this->assertEquals(self::getBundleDefaultConfig(), $config);
    }

    /**
     * @param array $testConfig
     *
     * @dataProvider configProvider
     */
    public function testConfigs(array $testConfig)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [$testConfig]);
        $expectedConfig = array_merge(self::getBundleDefaultConfig(), $testConfig);

        $this->assertEquals($expectedConfig, $config);
    }

    /**
     * @param array $testConfig
     *
     * @dataProvider invalidMenuNameConfigProvider
     */
    public function testInvalidMenuNames(array $testConfig)
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(json_encode(array_keys(current($testConfig))));

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [$testConfig]);
    }

    public function invalidMenuNameConfigProvider()
    {
        return [
            [[
                'menus' => [
                    'foo@bar' => [],
                    'foo' => [],
                ],
            ]],
            [[
                'menu_providers' => [
                    'foo@bar' => 'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::provider1',
                    'foo' => 'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::provider2',
                ],
            ]]
        ];
    }

    public function testInvalidMenuProviders()
    {
        $invalidCallback =
            'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::invalidCallback::foobar';

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(json_encode($invalidCallback));

        $testConfig = [
            'menu_providers' => ['invalidMenu' => $invalidCallback],
        ];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), [$testConfig]);
    }

    /**
     * @return array
     */
    protected static function getBundleDefaultConfig()
    {
        return [
            'menus' => [],
            'menu_providers' => [],
            'cache' => [
                'adapter' => 'nadia.menu.default_cache_adapter',
                'ttl' => 604800,
                'group_key' => '_nadia_menu_cache_group',
                'check_item_changes' => true,
            ],
            'knp_menus' => [],
        ];
    }

    public function configProvider()
    {
        return [
            [[
                'cache' => [
                    'adapter' => 'cache.adapter.filesystem',
                    'ttl' => 6048000,
                    'group_key' => '_nadia_menu_cache_group_test',
                    'check_item_changes' => false,
                ],
            ]],
            [[
                'menus' => [
                    'testMenu' => [
                        'root_title' => 'root',
                        'root_options' => [],
                        'item_options' => [],
                        'children' => [
                            [
                                'title' => 'menu #1',
                                'options' => [],
                                'children' => [
                                    [
                                        'title' => 'menu #1-1',
                                        'options' => [],
                                        'children' => [
                                            [
                                                'title' => 'menu #1-1-1',
                                                'options' => [],
                                                'children' => [],
                                            ],
                                        ],
                                    ],
                                    [
                                        'title' => 'menu #1-2',
                                        'options' => [],
                                        'children' => [],
                                    ],
                                ],
                            ],
                            [
                                'title' => 'menu #2',
                                'options' => [],
                                'children' => [
                                    [
                                        'title' => 'menu #2-1',
                                        'options' => [],
                                        'children' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]],
            [[
                'menu_providers' => [
                    'menu_provider1' =>
                        'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::provider1',
                    'menu_provider2' =>
                        'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::provider2',
                    'menu_provider3' =>
                        'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\testMenuProvider',
                ],
            ]],
            [[
                'knp_menus' => ['test', 'test2'],
            ]]
        ];
    }
}
