<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class KnpMenuFactory
{
    /**
     * @var FactoryInterface
     */
    private $knpMenuFactory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(FactoryInterface $knpMenuFactory, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->knpMenuFactory = $knpMenuFactory;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function create(KnpMenuBuilderInterface $builder): ItemInterface
    {
        $menus = $builder->build()->toArray();

        $this->filterGrantedMenus($menus);

        return $this->buildMenu($menus);
    }

    private function filterGrantedMenus(array &$menus)
    {
        if (empty($menus['children'])) {
            return;
        }

        foreach ($menus['children'] as $index => &$menu) {
            if (empty($menu['options']['roles'])) {
                continue;
            }

            $isGranted = true;

            foreach ($menu['options']['roles'] as $role) {
                if ($isGranted != $this->authorizationChecker->isGranted($role)) {
                    $isGranted = false;
                    break;
                }
            }

            if (!$isGranted) {
                unset($menus[$index]);
            } elseif (!empty($menu['children'])) {
                $this->filterGrantedMenus($menu);
            }
        }
    }

    private function buildMenu(array &$menus): ItemInterface
    {
        $root = $this->knpMenuFactory->createItem($menus['title'], $menus['options']);

        $this->buildMenuChildItems($root, $menus['children']);

        return $root;
    }

    private function buildMenuChildItems(ItemInterface $rootMenu, array &$menus)
    {
        foreach ($menus as $menu) {
            $child = $rootMenu->addChild($menu['title'], $menu['options']);

            if (!empty($menu['children'])) {
                $this->buildMenuChildItems($child, $menu['children']);
            }
        }
    }
}
