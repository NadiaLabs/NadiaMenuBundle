<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\MenuFactory;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Nadia\Bundle\NadiaMenuBundle\MenuProvider\MenuProvider;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class KnpMenuFactory
 */
class KnpMenuFactory
{
    /**
     * @var MenuProvider
     */
    private $menuProvider;

    /**
     * @var FactoryInterface
     */
    private $knpMenuFactory;

    /**
     * @var TagAwareAdapterInterface
     */
    private $cache;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var array
     */
    private $options = [
        'cache_ttl' => 604800,
        'cache_group_key' => '_nadia_menu_cache_group',
        'check_item_changes' => true,
    ];

    /**
     * KnpMenuFactory constructor.
     *
     * @param MenuProvider $menuProvider
     * @param FactoryInterface $knpMenuFactory
     * @param TagAwareAdapterInterface $cache
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param array $options
     */
    public function __construct(
        MenuProvider $menuProvider,
        FactoryInterface $knpMenuFactory,
        TagAwareAdapterInterface $cache,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        array $options = []
    ) {
        $this->menuProvider = $menuProvider;
        $this->knpMenuFactory = $knpMenuFactory;
        $this->cache = $cache;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @param string $menuName
     *
     * @return ItemInterface
     *
     * @throws InvalidArgumentException
     * @throws CacheException
     * @throws ReflectionException
     */
    public function create($menuName)
    {
        $item = $this->cache->getItem($this->generateCacheKey($menuName));

        if ($item->isHit()) {
            $menus = $item->get();
        } else {
            $menus = $this->menuProvider->getMenu($menuName);

            $this->filterGrantedMenus($menus['children']);

            $item->set($menus);
            $item->tag([$this->getCacheGroupKey()]);
            $item->expiresAfter($this->getCacheExpirePeriod());
            $this->cache->save($item);
        }

        return $this->buildMenuItems($menus);
    }

    /**
     * @param string $menuName
     *
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws CacheException
     * @throws ReflectionException
     */
    private function generateCacheKey($menuName)
    {
        $menuVersion = $this->getMenuVersion($menuName);
        $token = $this->tokenStorage->getToken();

        if (method_exists($token, 'getRoleNames')) {
            $roles = $this->tokenStorage->getToken()->getRoleNames();
        } else {
            $roles = $this->tokenStorage->getToken()->getRoles();
            $roles = array_map(function ($role) {
                return $role->getRole();
            }, $roles);
        }

        sort($roles);

        return $menuName . '-' . md5($menuVersion . '-' . join(',', $roles));
    }

    /**
     * @param string $menuName
     *
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws CacheException
     * @throws ReflectionException
     */
    private function getMenuVersion($menuName)
    {
        $item = $this->cache->getItem($menuName);
        $checkItemChanges = $this->enableCheckItemChanges();

        if ($item->isHit() && !$checkItemChanges) {
            return $item->get();
        }

        $version = $this->menuProvider->getMenuVersion($menuName);

        $item->set($version);
        $item->tag([$this->getCacheGroupKey()]);
        $this->cache->save($item);

        return $version;
    }

    /**
     * @param array $menus
     */
    private function filterGrantedMenus(array &$menus)
    {
        foreach ($menus as $index => &$menu) {
            if (empty($menu['options']['roles'])) {
                continue;
            }

            $isGranted = false;

            foreach ($menu['options']['roles'] as $role) {
                if ($isGranted = $this->authorizationChecker->isGranted($role)) {
                    break;
                }
            }

            if (!$isGranted) {
                unset($menus[$index]);
            } elseif (!empty($menu['children'])) {
                $this->filterGrantedMenus($menu['children']);
            }
        }
    }

    /**
     * Build menu items
     *
     * @param array $menus
     *
     * @return ItemInterface
     */
    private function buildMenuItems(array $menus)
    {
        $rootMenu = $this->knpMenuFactory->createItem($menus['root_title'], $menus['root_options']);

        $build = function (array $menus, ItemInterface $rootMenu) use (&$build) {
            foreach ($menus as $menu) {
                $childMenu = $rootMenu->addChild($menu['title'], $menu['options']);

                if (!empty($menu['children'])) {
                    $build($menu['children'], $childMenu);
                }
            }
        };

        $build($menus['children'], $rootMenu);

        return $rootMenu;
    }

    /**
     * @return int
     */
    private function getCacheExpirePeriod()
    {
        return (int) $this->options['cache_ttl'];
    }

    /**
     * @return string
     */
    private function getCacheGroupKey()
    {
        return (string) $this->options['cache_group_key'];
    }

    /**
     * @return bool
     */
    private function enableCheckItemChanges()
    {
        return (bool) $this->options['check_item_changes'];
    }
}
