<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Tests;

use Nadia\Bundle\NadiaMenuBundle\DependencyInjection\Compiler\MenuProviderPass;
use Nadia\Bundle\NadiaMenuBundle\NadiaMenuBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class NadiaMenuBundle
 */
class NadiaMenuBundleTest extends TestCase
{
    public function testBuild()
    {
        $bundle = new NadiaMenuBundle();
        $container = new ContainerBuilder();

        $bundle->build($container);

        $count = 0;

        foreach ($container->getCompiler()->getPassConfig()->getBeforeOptimizationPasses() as $pass) {
            if ($pass instanceof MenuProviderPass) {
                ++$count;
            }
        }


        $this->assertEquals(1, $count);
    }
}
