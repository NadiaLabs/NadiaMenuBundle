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

class KnpMenu
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var KnpMenu
     */
    protected $parent;

    /**
     * @var KnpMenu[]
     */
    protected $children = [];

    public function __construct(string $title, array $options = [], KnpMenu $parent = null)
    {
        $this->title = $title;
        $this->options = $options;
        $this->parent = $parent;
    }

    /**
     * Add child menu to current node
     */
    public function child(string $title, array $options = []): KnpMenu
    {
        $menu = new static($title, $options, $this);

        $this->children[] = $menu;

        return $menu;
    }

    /**
     * End setting up the menu options, and return parent menu node.
     */
    public function end(): KnpMenu
    {
        return $this->parent;
    }

    public function uri(string $uri): KnpMenu
    {
        $this->options['uri'] = $uri;

        return $this;
    }

    public function label(string $label): KnpMenu
    {
        $this->options['label'] = $label;

        return $this;
    }

    /**
     * @param string $key
     * @param string|bool $value
     *
     * @return $this
     */
    public function attribute(string $key, $value): KnpMenu
    {
        $this->options['attributes'][$key] = $value;

        return $this;
    }

    public function attributes(array $attributes): KnpMenu
    {
        $this->options['attributes'] = $attributes;

        return $this;
    }

    /**
     * @param string $key
     * @param string|bool $value
     *
     * @return $this
     */
    public function linkAttribute(string $key, $value): KnpMenu
    {
        $this->options['linkAttributes'][$key] = $value;

        return $this;
    }

    public function linkAttributes(array $attributes): KnpMenu
    {
        $this->options['linkAttributes'] = $attributes;

        return $this;
    }

    /**
     * @param string $key
     * @param string|bool $value
     *
     * @return $this
     */
    public function childrenAttribute(string $key, $value): KnpMenu
    {
        $this->options['childrenAttributes'][$key] = $value;

        return $this;
    }

    public function childrenAttributes(array $attributes): KnpMenu
    {
        $this->options['childrenAttributes'] = $attributes;

        return $this;
    }

    /**
     * @param string $key
     * @param string|bool $value
     *
     * @return $this
     */
    public function labelAttribute(string $key, $value): KnpMenu
    {
        $this->options['labelAttributes'][$key] = $value;

        return $this;
    }

    public function labelAttributes(array $attributes): KnpMenu
    {
        $this->options['labelAttributes'] = $attributes;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function extra(string $key, $value): KnpMenu
    {
        $this->options['extras'][$key] = $value;

        return $this;
    }

    public function extras(array $extras): KnpMenu
    {
        $this->options['extras'] = $extras;

        return $this;
    }

    public function removeExtra(string $key): KnpMenu
    {
        if (isset($this->options['extras'][$key])) {
            unset($this->options['extras'][$key]);
        }

        return $this;
    }

    public function current(bool $isCurrent): KnpMenu
    {
        $this->options['current'] = $isCurrent;

        return $this;
    }

    public function display(bool $display): KnpMenu
    {
        $this->options['display'] = $display;

        return $this;
    }

    public function displayChildren(bool $displayChildren): KnpMenu
    {
        $this->options['displayChildren'] = $displayChildren;

        return $this;
    }

    /**
     * Mark current node as a leaf node, a leaf node won't render child nodes.
     */
    public function leaf(bool $leaf): KnpMenu
    {
        if (true === $leaf) {
            $this->displayChildren(false);
        } else {
            $this->displayChildren(true);
        }

        return $this;
    }

    /**
     * Hide current node and child nodes.
     */
    public function hide(bool $hide): KnpMenu
    {
        $this->display(!$hide);
        $this->displayChildren(!$hide);

        return $this;
    }

    public function route(string $routeName, array $routeParameters = [], bool $routeAbsolute = false): KnpMenu
    {
        $this->options['route'] = $routeName;
        $this->options['routeParameters'] = $routeParameters;
        $this->options['routeAbsolute'] = $routeAbsolute;

        return $this;
    }

    public function exRoute(string $routeName, array $routeParameters = []): KnpMenu
    {
        $this->options['extras']['routes'][] = [
            'route' => $routeName,
            'routeParameters' => $routeParameters,
        ];

        return $this;
    }

    public function roles(string ...$roles): KnpMenu
    {
        $this->options['extras']['roles'] = $roles;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function option(string $key, $value): KnpMenu
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function removeOption(string $key): KnpMenu
    {
        if (isset($this->options[$key])) {
            unset($this->options[$key]);
        }

        return $this;
    }

    public function toArray(): array
    {
        $menu = [
            'title' => $this->title,
            'options' => $this->options,
            'children' => [],
        ];

        foreach ($this->children as $child) {
            $menu['children'][] = $child->toArray();
        }

        return $menu;
    }
}
