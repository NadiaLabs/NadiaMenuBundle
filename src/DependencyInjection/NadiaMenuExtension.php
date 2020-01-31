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

use Nadia\Bundle\NadiaMenuBundle\MenuFactory\KnpMenuFactory;
use Nadia\Bundle\NadiaMenuBundle\MenuProvider\MenuProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Filesystem;

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
        $kernelCacheDir = $container->getParameter('kernel.cache_dir');
        $staticMenuCacheDir = $kernelCacheDir . '/knp_menu/menus';

        $container->setParameter('nadia.menu.static_menu_cache_dir', $staticMenuCacheDir);

        $loader->load('services.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        // Remove duplicated menu names from menu_providers
        foreach ($config['menus'] as $menuName => $menu) {
            if (isset($config['menu_providers'][$menuName])) {
                unset($config['menu_providers'][$menuName]);
            }
        }

        $this->createMenuCacheFiles($config['menus'], $staticMenuCacheDir);

        $container->getDefinition('nadia.menu.cache_tag_aware_adapter')
            ->setArgument(0, new Reference($config['cache']['adapter']))
            ->setArgument(1, new Reference($config['cache']['adapter']));

        $container->getDefinition(MenuProvider::class)
            ->setArgument(1, array_keys($config['menus']))
            ->setArgument(2, $config['menu_providers']);

        $container->getDefinition(KnpMenuFactory::class)->setArgument(5, [
            'cache_ttl' => $config['cache']['ttl'],
            'cache_group_key' => $config['cache']['group_key'],
            'check_item_changes' => $config['cache']['check_item_changes'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getNamespace()
    {
        return 'http://nadialabs.com.tw/schema/dic/menu';
    }

    /**
     * @param array  $menus
     * @param string $cacheDir
     */
    private function createMenuCacheFiles(array $menus, $cacheDir)
    {
        $fs = new Filesystem();
        $dir = rtrim($cacheDir, '/ ');

        if (!file_exists($dir)) {
            $fs->mkdir($dir, 0755);
        }

        foreach ($menus as $name => $menu) {
            $filename = $dir . '/' . $name . '.cache';

            $fs->dumpFile($filename, serialize($menu));
        }
    }
}
