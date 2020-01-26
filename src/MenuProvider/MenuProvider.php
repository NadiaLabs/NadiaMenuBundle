<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\MenuProvider;

use Nadia\Bundle\NadiaMenuBundle\Builder\MenuBuilder;

/**
 * Class MenuProvider
 */
class MenuProvider
{
    /**
     * @var string
     */
    private $staticMenuCacheDir;

    /**
     * @var string[]
     */
    private $staticMenuNames;

    /**
     * @var callable[]
     */
    private $buildCallbacks;

    /**
     * MenuProvider constructor.
     *
     * @param string     $staticMenuCacheDir
     * @param string[]   $staticMenuNames
     * @param callable[] $buildCallbacks
     */
    public function __construct($staticMenuCacheDir, array $staticMenuNames, array $buildCallbacks)
    {
        $this->staticMenuNames = $staticMenuNames;
        $this->staticMenuCacheDir = $staticMenuCacheDir;
        $this->buildCallbacks = $buildCallbacks;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getMenu($name)
    {
        if (in_array($name, $this->staticMenuNames) && file_exists($this->getStaticMenuCacheFilepath($name))) {
            return $this->getStaticMenu($name);
        }

        if (isset($this->buildCallbacks[$name])) {
            return $this->getCallbackMenu($name);
        }

        throw new \InvalidArgumentException(sprintf('The menu "%s" is not exists!', $name));
    }

    /**
     * @param string $name
     *
     * @return string The MD5 hash of the menu configuration
     *
     * @throws \ReflectionException
     */
    public function getMenuVersion($name)
    {
        if (in_array($name, $this->staticMenuNames) && file_exists($this->getStaticMenuCacheFilepath($name))) {
            return md5_file($this->getStaticMenuCacheFilepath($name));
        }

        if (isset($this->buildCallbacks[$name])) {
            if (!is_callable($this->buildCallbacks[$name], false, $callbackName)) {
                throw new \InvalidArgumentException(
                    sprintf('The callback "%s" is not callable!', $callbackName)
                );
            }

            $ref = new \ReflectionMethod($callbackName);

            return md5_file($ref->getFileName());
        }

        throw new \InvalidArgumentException(
            sprintf('Cannot get menu version, the menu "%s" is not exists!', $name)
        );
    }

    /**
     * Get menu configuration from menu cache files
     *
     * @param string $name Menu name
     *
     * @return array
     */
    private function getStaticMenu($name)
    {
        return unserialize(file_get_contents($this->getStaticMenuCacheFilepath($name)));
    }

    /**
     * @param string $name Menu name
     *
     * @return string
     */
    private function getStaticMenuCacheFilepath($name)
    {
        return $this->staticMenuCacheDir . '/' . $name . '.cache';
    }

    /**
     * Get menu configuration by build callbacks
     *
     * @param string $name Menu name
     *
     * @return array
     */
    private function getCallbackMenu($name)
    {
        $builder = $this->createMenuBuilder();

        call_user_func($this->buildCallbacks[$name], $builder);

        return $builder->getMenu();
    }

    /**
     * @return MenuBuilder
     */
    private function createMenuBuilder()
    {
        return new MenuBuilder();
    }
}
