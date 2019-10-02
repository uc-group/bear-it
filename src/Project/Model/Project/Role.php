<?php

namespace App\Project\Model\Project;

class Role
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    private function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return Role
     */
    public static function owner()
    {
        return new self('owner');
    }

    /**
     * @return Role
     */
    public static function admin()
    {
        return new self('admin');
    }

    /**
     * @return Role
     */
    public static function member()
    {
        return new self('member');
    }

    /**
     * @return Role
     */
    public static function none()
    {
        return new self('');
    }

    /**
     * @param string $name
     * @return Role
     */
    public static function fromString(string $name)
    {
        return new self($name);
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->name;
    }

    /**
     * @param $other
     * @return bool
     */
    public function equals($other)
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->name === $other->name;
    }
}