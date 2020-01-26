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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class XmlNadiaMenuExtensionTest
 */
class XmlNadiaMenuExtensionTest extends NadiaMenuExtensionTest
{
    protected function loadConfigFile(ContainerBuilder $container, $filename)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Fixtures/config'));
        $loader->load($filename . '.xml');
    }
}
