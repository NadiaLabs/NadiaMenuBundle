<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Tests\DependencyInjection\Compiler;

use Nadia\Bundle\NadiaMenuBundle\DependencyInjection\Compiler\MenuProviderPass;
use Nadia\Bundle\NadiaMenuBundle\MenuProvider\MenuProvider;
use Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class MenuProviderPass
 */
class MenuProviderPassTest extends TestCase
{
    public function testProcess()
    {
        $menuProviders = require __DIR__ . '/../../Fixtures/config/test-menus-providers.php';

        $container = new ContainerBuilder();

        $container->setDefinition(TestMenuProvider::class, new Definition(TestMenuProvider::class));
        $container->setDefinition('test.menu.provider.service', new Definition(TestMenuProvider::class));
        $container->setDefinition(
            MenuProvider::class,
            new Definition(MenuProvider::class, ['%nadia.menu.static_menu_cache_dir%', [], $menuProviders])
        );

        $expectedMenuProviders = [
            'menu_provider1' => 'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::provider1',
            'menu_provider2' => 'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::provider2',
            'menu_provider3' => [new Reference(TestMenuProvider::class), 'provider3'],
            'menu_provider4' => [new Reference('test.menu.provider.service'), 'provider3'],
            'menu_provider5' => 'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\testMenuProvider',
            'test2' => 'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::provider2',
        ];

        $pass = new MenuProviderPass();

        $pass->process($container);

        $this->assertEquals($expectedMenuProviders, $container->getDefinition(MenuProvider::class)->getArgument(2));
    }
}
