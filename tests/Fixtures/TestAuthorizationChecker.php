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

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class TestAuthorizationChecker
 */
class TestAuthorizationChecker implements AuthorizationCheckerInterface
{
    private $grantedRoles;
    private $notGrantedRoles;

    public function __construct(array $grantedRoles, array $notGrantedRoles)
    {
        $this->grantedRoles = $grantedRoles;
        $this->notGrantedRoles = $notGrantedRoles;
    }

    /**
     * @inheritDoc
     */
    public function isGranted($attributes, $subject = null)
    {
        $attributes = (array) $attributes;

        foreach ($attributes as $attribute) {
            if (in_array($attribute, $this->grantedRoles)) {
                return true;
            }

            if (in_array($attribute, $this->notGrantedRoles)) {
                return false;
            }
        }

        return false;
    }
}
