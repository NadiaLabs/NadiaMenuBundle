<?php

/**
 * This file is part of the NadiaMenuBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaMenuBundle\Tests\Config\Definition\Builder;

use Nadia\Bundle\NadiaMenuBundle\Config\Definition\Builder\MenuOptionsNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Class MenuNodeArrayNormalizerTest
 */
class MenuOptionsNormalizerTest extends TestCase
{
    /**
     * @dataProvider getTestData
     *
     * @param MenuOptionsNormalizer $normalizer
     * @param array                 $value
     * @param array|mixed           $normalizedValue
     */
    public function testNormalize(MenuOptionsNormalizer $normalizer, $value, $normalizedValue)
    {
        $this->assertEquals($normalizedValue, $normalizer->normalize($value));
    }

    public function getTestData()
    {
        $normalizer = new MenuOptionsNormalizer();

        return [
            [$normalizer, ['foo' => 'bar'], ['foo' => 'bar']],
            [$normalizer, [], []],
            [$normalizer, [1, 2, 3], [1, 2, 3]],
            [$normalizer, ['target' => ''], ['target' => '']],
            [$normalizer, ['target' => 'foo'], []],
            [$normalizer, ['target' => 'foo', 'foo' => []], []],
            [$normalizer, ['target' => 'foo', 'foo' => [1, 2, 3]], [1, 2, 3]],
            [$normalizer, ['target' => 'foo', 'foo' => ''], ''],
            [$normalizer, ['target' => 'bar', 'bar' => 'foobar'], 'foobar'],
            [$normalizer, ['target' => 'bar', 'bar' => 'foobar', 'type' => 'array'], ['foobar']],
            [$normalizer, ['target' => 'bar', 'bar' => '', 'type' => 'array'], []],
            [$normalizer, ['target' => 'bar', 'bar' => false, 'type' => 'array'], []],
            [$normalizer, ['target' => 'bar', 'bar' => [1, 2, 3], 'type' => 'array'], [1, 2, 3]],
            [$normalizer, ['target' => 'bar', 'bar' => (object) ['foo' => 'bar'], 'type' => 'array'], ['foo' => 'bar']],
        ];
    }
}
