<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures;

use Nadia\Bundle\NadiaMenuBundle\Builder\MenuBuilder;

/**
 * Class TestMenuProvider
 */
class TestMenuProvider
{
    public static function provider1(MenuBuilder $builder)
    {
        $builder
            ->addChild('menu #1', ['roles' => ['ROLE_PAGE1']])
                ->addChild('menu #1-1', ['roles' => ['ROLE_PAGE1']])->end()
                ->addChild('menu #1-2', ['roles' => ['ROLE_PAGE1']])->end()
            ->end()
            ->addChild('menu #2', ['roles' => ['ROLE_PAGE2']])
                ->addChild('menu #2-1', ['roles' => ['ROLE_PAGE2']])
                    ->addChild('menu #2-1-1')->end()
                ->end()
            ->end()
            ->addChild('menu #3', ['roles' => ['ROLE_PAGE3']])
                ->addChild('menu #3-1', ['roles' => ['ROLE_PAGE3']])->end()
            ->end()
        ;
    }

    public static function provider2(MenuBuilder $builder)
    {
        $builder
            ->addChild('menu2 #1')
                ->addChild('menu2 #1-1')->end()
            ->end()
            ->addChild('menu2 #2')
                ->addChild('menu2 #2-1')
                    ->addChild('menu2 #2-1-1')->end()
                ->end()
            ->end()
        ;
    }

    public function provider3(MenuBuilder $builder)
    {
        $builder
            ->addChild('menu2 #1')
                ->addChild('menu2 #1-1')->end()
            ->end()
            ->addChild('menu2 #2')
                ->addChild('menu2 #2-1')
                    ->addChild('menu2 #2-1-1')->end()
                ->end()
            ->end()
        ;
    }
}

function testMenuProvider(MenuBuilder $builder)
{
}
