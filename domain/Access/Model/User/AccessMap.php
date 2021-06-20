<?php

namespace BearIt\Access\Model\User;

use BearIt\Access\Model\AccessFunction\AccessFunction;
use BearIt\Access\Model\Limitation\LimitationCollection;
use BearIt\Access\Model\Role\RoleInterface;
use BearIt\User\Model\UserId;

class AccessMap
{
    /**
     * @var LimitationCollection[]
     */
    private $map;

    /**
     * @param RoleInterface[] $roles
     */
    public function __construct(array $roles)
    {
        $this->buildMap($roles);
    }

    /**
     * @param AccessFunction $function
     * @param UserId $userId
     * @param $subject
     * @return bool
     */
    public function grantsAccess(AccessFunction $function, UserId $userId, $subject)
    {
        $limitations = $this->map[$function->toString()] ?? null;
        if (!$limitations) {
            return false;
        }

        if ($limitations->isUnlimited()) {
            return true;
        }

        foreach ($limitations as $limitation) {
            if (!$limitation->supports($subject)) {
                continue;
            }

            if (!$limitation->isFulfilledBy($userId, $subject)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $roles
     */
    private function buildMap(array $roles)
    {
        // User has access if any policy grants function while all it's limitations are fulfilled
        foreach ($roles as $role) {
            foreach ($role->getPolicies() as $policy) {
                foreach ($policy->getFunctions() as $function) {
                    if (!isset($this->map[$function->toString()])) {
                        $this->map[$function->toString()] = $policy->getLimitations();
                    } else {
                        $this->map[$function->toString()]->combine($policy->getLimitations());
                    }
                }
            }
        }
    }
}
