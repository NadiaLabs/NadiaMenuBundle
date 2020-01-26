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

use Nadia\Bundle\NadiaMenuBundle\DependencyInjection\NadiaMenuExtension;
use Nadia\Bundle\NadiaMenuBundle\MenuFactory\KnpMenuFactory;
use Nadia\Bundle\NadiaMenuBundle\MenuProvider\MenuProvider;
use Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Filesystem;

if (!function_exists('Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\testMenuProvider')) {
    require __DIR__ . '/../Fixtures/TestMenuProvider.php';
}

/**
 * Class NadiaMenuExtensionTest
 */
abstract class NadiaMenuExtensionTest extends TestCase
{
    /**
     * @param ContainerBuilder $container
     * @param string           $filename  Filename without extension part (e.g. "test" for test.php/test.xml/test.yml)
     */
    abstract protected function loadConfigFile(ContainerBuilder $container, $filename);

    public function testCacheDirParameter()
    {
        $container = $this->createContainerByConfigFile('test');
        $cacheDir = $container->getParameter('nadia.menu.static_menu_cache_dir');
        $expectedMenus = require __DIR__ . '/../Fixtures/config/test-menus.php';

        foreach ($expectedMenus as $name => $menu) {
            $actualMenu = unserialize(file_get_contents($cacheDir . '/' . $name . '.cache'));

            $this->assertEquals($menu, $actualMenu);
        }

        // Remove test cache directory
        (new Filesystem())->remove($container->getParameter('kernel.cache_dir'));
    }

    public function testMenuProviders()
    {
        $container = $this->createContainerByConfigFile('test');
        $expectedMenus = require __DIR__ . '/../Fixtures/config/test-menus.php';

        $menuProviderDefinition = $container->getDefinition(MenuProvider::class);
        $expectedMenuProviders = [
            'menu_provider1' => 'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::provider1',
            'menu_provider2' => 'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::provider2',
            'menu_provider3' => [new TestMenuProvider(), 'provider3'],
            'menu_provider4' => [new Reference('test.menu.provider.service'), 'provider3'],
            'menu_provider5' => 'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\testMenuProvider',
        ];

        $this->assertEquals(array_keys($expectedMenus), $menuProviderDefinition->getArgument(1));
        $this->assertEquals($expectedMenuProviders, $menuProviderDefinition->getArgument(2));

        // Remove test cache directory
        (new Filesystem())->remove($container->getParameter('kernel.cache_dir'));
    }

    public function testCacheConfiguration()
    {
        $container = $this->createContainerByConfigFile('test');
        $cacheConfig = require __DIR__ . '/../Fixtures/config/test-cache.php';

        $cacheTagAwareAdapterDefinition = $container->getDefinition('nadia.menu.cache_tag_aware_adapter');

        $this->assertEquals($cacheConfig['adapter'], $cacheTagAwareAdapterDefinition->getArgument(0));
        $this->assertEquals($cacheConfig['adapter'], $cacheTagAwareAdapterDefinition->getArgument(1));

        $knpMenuFactoryDefinition = $container->getDefinition(KnpMenuFactory::class);
        $actualCacheConfig = $knpMenuFactoryDefinition->getArgument(5);
        $expectedCacheConfig = [
            'cache_ttl' => $cacheConfig['ttl'],
            'cache_group_key' => $cacheConfig['group_key'],
            'check_item_changes' => $cacheConfig['check_item_changes'],
        ];

        $this->assertEquals($expectedCacheConfig, $actualCacheConfig);

        // Remove test cache directory
        (new Filesystem())->remove($container->getParameter('kernel.cache_dir'));
    }

    protected function createContainerByConfigFile($filename, array $data = [])
    {
        $container = $this->createBaseContainer($data);

        $container->register('test.menu.provider.service', TestMenuProvider::class);

        $container->registerExtension(new NadiaMenuExtension());

        $this->loadConfigFile($container, $filename);

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        $container->compile();

        return $container;
    }

    /**
     * @param array $data
     *
     * @return ContainerBuilder
     */
    protected function createBaseContainer(array $data = [])
    {
        // Make sure cache directory is different
        sleep(1);

        return new ContainerBuilder(new ParameterBag(array_merge([
            'kernel.bundles' => ['NadiaMenuBundle' => 'Nadia\\Bundle\\NadiaMenuBundle\\NadiaMenuBundle'],
            'kernel.bundles_metadata' => [
                'NadiaMenuBundle' => [
                    'namespace' => 'Nadia\\Bundle\\NadiaMenuBundle',
                    'path' => __DIR__ . '/../..',
                ]
            ],
            'kernel.cache_dir' => sys_get_temp_dir() . '/nadia-menu-bundle-tests-' . time(),
            'kernel.project_dir' => __DIR__,
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.name' => 'kernel',
            'kernel.root_dir' => __DIR__,
            'kernel.container_class' => 'testContainer',
        ], $data)));
    }
}
