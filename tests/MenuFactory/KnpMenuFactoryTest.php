<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Tests\MenuFactory;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Nadia\Bundle\NadiaMenuBundle\MenuFactory\KnpMenuFactory;
use Nadia\Bundle\NadiaMenuBundle\MenuProvider\MenuProvider;
use Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestAuthorizationChecker;
use Nadia\Bundle\NadiaMenuBundle\Tests\Fixtures\TestUsernamePasswordToken;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class KnpMenuFactoryTest
 */
class KnpMenuFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = $this->createKnpMenuFactory(
            $this->mockUsernamePasswordToken(['ROLE_USER', 'ROLE_PAGE1', 'ROLE_PAGE2']),
            ['ROLE_PAGE1', 'ROLE_PAGE2'],
            ['ROLE_PAGE_3']
        );

        $menu = $factory->create('test');

        $this->doTestMenuAssertions($menu);

        $menu = $factory->create('test');

        $this->doTestMenuAssertions($menu);

        $factory = $this->createKnpMenuFactory(
            $this->mockUsernamePasswordToken(['ROLE_USER', 'ROLE_PAGE1', 'ROLE_PAGE2']),
            ['ROLE_PAGE1', 'ROLE_PAGE2'],
            ['ROLE_PAGE_3'],
            ['check_item_changes' => false]
        );

        $menu = $factory->create('test');
        $menu = $factory->create('test');

        $this->doTestMenuAssertions($menu);

        if (version_compare(Kernel::VERSION, '4.3', '<')) {
            $factory = $this->createKnpMenuFactory(
                $this->mockTestUsernamePasswordToken(['ROLE_USER', 'ROLE_PAGE1', 'ROLE_PAGE2']),
                ['ROLE_PAGE1', 'ROLE_PAGE2'],
                ['ROLE_PAGE_3']
            );

            $menu = $factory->create('test');

            $this->doTestMenuAssertions($menu);
        }
    }

    /**
     * @param ItemInterface $menu
     */
    private function doTestMenuAssertions($menu)
    {
        $this->assertInstanceOf(MenuItem::class, $menu);
        $this->assertInstanceOf(MenuItem::class, $menu->getChild('menu #1'));
        $this->assertEquals('menu #1', $menu->getChild('menu #1')->getName());

        $this->assertInstanceOf(MenuItem::class, $menu->getChild('menu #2'));
        $this->assertEquals('menu #2', $menu->getChild('menu #2')->getName());

        $menu2 = $menu->getChild('menu #2');

        $this->assertInstanceOf(MenuItem::class, $menu2->getChild('menu #2-1'));
        $this->assertEquals('menu #2-1', $menu2->getChild('menu #2-1')->getName());

        $menu2_1 = $menu2->getChild('menu #2-1');

        $this->assertInstanceOf(MenuItem::class, $menu2_1->getChild('menu #2-1-1'));
        $this->assertEquals('menu #2-1-1', $menu2_1->getChild('menu #2-1-1')->getName());

        $this->assertNull($menu->getChild('menu #4'));
    }

    private function createKnpMenuFactory(
        TokenInterface $token,
        array $grantedRoles,
        array $notGrantedRoles,
        array $options = []
    ) {
        return new KnpMenuFactory(
            $this->mockMenuProvider(),
            new MenuFactory(),
            new TagAwareAdapter(new ArrayAdapter(), new ArrayAdapter()),
            $this->mockTokenStorage($token),
            $this->mockAuthorizationChecker($grantedRoles, $notGrantedRoles),
            $options
        );
    }

    /**
     * @return MenuProvider
     */
    private function mockMenuProvider()
    {
        $menus = require __DIR__ . '/../Fixtures/config/test-menus.php';

        return new MenuProvider(__DIR__ . '/../Fixtures/cache', array_keys($menus), []);
    }

    /**
     * @param array $grantedRoles
     * @param array $notGrantedRoles
     *
     * @return MockObject|AuthorizationCheckerInterface
     */
    private function mockAuthorizationChecker(array $grantedRoles, array $notGrantedRoles)
    {
        return new TestAuthorizationChecker($grantedRoles, $notGrantedRoles);
    }

    /**
     * @param TokenInterface $token
     *
     * @return TokenStorage
     */
    private function mockTokenStorage(TokenInterface $token)
    {
        $tokenStorage = new TokenStorage();

        $tokenStorage->setToken($token);

        return $tokenStorage;
    }

    /**
     * @param array $roles
     *
     * @return UsernamePasswordToken
     */
    private function mockUsernamePasswordToken(array $roles)
    {
        return new UsernamePasswordToken('testUser', 'secret', 'test', $roles);
    }

    /**
     * @param array $roles
     *
     * @return TestUsernamePasswordToken
     */
    private function mockTestUsernamePasswordToken(array $roles)
    {
        return new TestUsernamePasswordToken('testUser', 'secret', 'test', $roles);
    }
}
