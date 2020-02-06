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

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class TestUsernamePasswordToken
 */
class TestUsernamePasswordToken extends UsernamePasswordToken
{
    public function getRoleNames()
    {
        return array_map(function ($role) {
            return $role->getRole();
        }, $this->getRoles());
    }
}
