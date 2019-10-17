<?php

namespace BearIt\Access\Model\User;

use BearIt\Access\Model\AccessFunction\AccessFunction;
use BearIt\Access\Model\AccessFunction\PermissionSet;
use BearIt\Access\Model\Role\RoleInterface;
use BearIt\User\Model\UserId;

class User
{
    /**
     * @var RoleInterface[]
     */
    private $roles;

    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var AccessMap
     */
    private $accessMap;

    /**
     * @param UserId $userId
     * @param array $roles
     */
    public function __construct(UserId $userId, array $roles)
    {
        $this->roles = $roles;
        $this->userId = $userId;
    }

    public function isGranted(AccessFunction $function, $subject = null)
    {
        $accessMap = $this->getAccessMap();

        return $accessMap->grantsAccess($function, $this->userId, $subject);
    }

    /**
     * @param AccessFunction[] $accessFunctions
     * @param $subject
     * @return bool
     */
    public function allGranted(array $accessFunctions, $subject = null)
    {
        $accessMap = $this->getAccessMap();
        foreach ($accessFunctions as $function) {
            if (!$accessMap->grantsAccess($function, $this->userId, $subject)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param AccessFunction[] $accessFunctions
     * @param $subject
     * @return bool
     */
    public function anyGranted(array $accessFunctions, $subject = null)
    {
        $accessMap = $this->getAccessMap();
        foreach ($accessFunctions as $function) {
            if ($accessMap->grantsAccess($function, $this->userId, $subject)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return AccessMap
     */
    private function getAccessMap()
    {
        if (!$this->accessMap) {
            $this->accessMap = new AccessMap($this->roles);
        }

        return $this->accessMap;
    }
}
