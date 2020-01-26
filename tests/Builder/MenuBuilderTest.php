<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Tests\Builder;

use Nadia\Bundle\NadiaMenuBundle\Builder\MenuBuilder;
use Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class MenuBuilderTest
 */
class MenuBuilderTest extends TestCase
{
    public function testBuilders()
    {
        $builder = $this->createMenuBuilder();
        TestMenuProvider::provider1($builder);
        $this->assertEquals($this->getExpectedMenu('test'), $builder->getMenu());

        $builder = $this->createMenuBuilder();
        TestMenuProvider::provider2($builder);
        $this->assertEquals($this->getExpectedMenu('test2'), $builder->getMenu());

        $builder = $this->createMenuBuilder();
        (new TestMenuProvider())->provider3($builder);
        $this->assertEquals($this->getExpectedMenu('test2'), $builder->getMenu());
    }

    private function getExpectedMenu($name)
    {
        $menus = require __DIR__ . '/../Fixtures/config/test-menus.php';

        return $menus[$name];
    }

    private function createMenuBuilder()
    {
        return new MenuBuilder();
    }
}
