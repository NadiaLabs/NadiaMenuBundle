<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Config\Definition\Builder;

/**
 * Class MenuNodeArrayNormalizer
 */
class MenuOptionsNormalizer
{
    /**
     * @param array $value
     *
     * @return array|mixed
     */
    public function normalize(array $value)
    {
        if (empty($value['target'])) {
            return $value;
        }
        if (!array_key_exists($value['target'], $value)) {
            return [];
        }

        $type = empty($value['type']) ? '' : $value['type'];

        if ('array' === $type) {
            if (empty($value[$value['target']])) {
                return [];
            }

            return (array) $value[$value['target']];
        }

        return $value[$value['target']];
    }
}
