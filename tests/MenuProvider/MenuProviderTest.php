<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Tests\MenuProvider;

use Nadia\Bundle\NadiaMenuBundle\MenuProvider\MenuProvider;
use Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class MenuProviderTest
 */
class MenuProviderTest extends TestCase
{
    public function testMenus()
    {
        $cacheDir = $this->getCacheDir();
        $provider = $this->createNormalMenuProvider();
        $testMenu = unserialize(file_get_contents($cacheDir . '/test.cache'));
        $test2Menu = unserialize(file_get_contents($cacheDir . '/test2.cache'));

        $this->assertEquals($testMenu, $provider->getMenu('test'));
        $this->assertEquals($test2Menu, $provider->getMenu('test2'));

        $expectedCallbackMenus = $this->getExpectedCallbackMenus();

        $this->assertEquals($expectedCallbackMenus['test'], $provider->getMenu('provider1'));
        $this->assertEquals($expectedCallbackMenus['test2'], $provider->getMenu('provider2'));
        $this->assertEquals($expectedCallbackMenus['test2'], $provider->getMenu('provider3'));
    }

    public function testMenuNotFound()
    {
        $this->expectException('InvalidArgumentException');

        $provider = $this->createEmptyMenuProvider();

        $provider->getMenu('any');
    }

    public function testMenuVersions()
    {
        $cacheDir = $this->getCacheDir();
        $provider = $this->createNormalMenuProvider();
        $testMenuHash = md5_file($cacheDir . '/test.cache');
        $test2MenuHash = md5_file($cacheDir . '/test2.cache');

        $this->assertEquals($testMenuHash, $provider->getMenuVersion('test'));
        $this->assertEquals($test2MenuHash, $provider->getMenuVersion('test2'));

        $expectedCallbackMenuHash = md5_file(__DIR__ . '/../Fixtures/TestMenuProvider.php');

        $this->assertEquals($expectedCallbackMenuHash, $provider->getMenuVersion('provider1'));
        $this->assertEquals($expectedCallbackMenuHash, $provider->getMenuVersion('provider2'));
        $this->assertEquals($expectedCallbackMenuHash, $provider->getMenuVersion('provider3'));
    }

    public function testMenuVersionNotFound()
    {
        $this->expectException('InvalidArgumentException');

        $provider = $this->createEmptyMenuProvider();

        $provider->getMenuVersion('any');
    }

    public function testNotCallableMenuProviderException()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage(
            'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::providerNotCallable'
        );

        $provider = $this->createNotCallableMenuProvider();

        $provider->getMenuVersion('provider1');
    }

    private function createNormalMenuProvider()
    {
        return new MenuProvider(
            $this->getCacheDir(),
            ['test', 'test2'],
            [
                'provider1' => 'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::provider1',
                'provider2' => 'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::provider2',
                'provider3' => [new TestMenuProvider(), 'provider3'],

                // Test for duplicated menu name
                'test2' => 'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::provider2',
            ]
        );
    }

    private function createNotCallableMenuProvider()
    {
        return new MenuProvider(
            $this->getCacheDir(),
            [],
            [
                'provider1' => 'Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestMenuProvider::providerNotCallable',
            ]
        );
    }

    private function createEmptyMenuProvider()
    {
        return new MenuProvider($this->getCacheDir(), [], []);
    }

    private function getCacheDir()
    {
        return __DIR__ . '/../Fixtures/cache';
    }

    private function getExpectedCallbackMenus()
    {
        return require __DIR__ . '/../Fixtures/config/test-menus.php';
    }
}
