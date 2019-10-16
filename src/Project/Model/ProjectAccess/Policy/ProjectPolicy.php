<?php

namespace App\Project\Model\ProjectAccess\Policy;

use App\Project\Model\ProjectAccess\Limitation\MemberLimitation;
use BearIt\Access\Model\Policy\PolicyInterface;
use BearIt\Access\Model\AccessFunction\AccessFunction;
use BearIt\Access\Model\Policy\PolicyTrait;

class ProjectPolicy implements PolicyInterface
{
    use PolicyTrait;

    /**
     * @return AccessFunction
     */
    public static function removeFunction(): AccessFunction
    {
        return AccessFunction::fromString('project/remove');
    }

    /**
     * @return AccessFunction
     */
    public static function editFunction(): AccessFunction
    {
        return AccessFunction::fromString('project/edit');
    }

    /**
     * @return AccessFunction
     */
    public static function memberManageFunction(): AccessFunction
    {
        return AccessFunction::fromString('project/member-manage');
    }

    /**
     * @param array $functions
     * @param array $limitations
     */
    private function __construct(array $functions, array $limitations = [])
    {
        $this->functions = $functions;
        $this->limitations = $limitations;
    }

    /**
     * @return ProjectPolicy[]
     */
    public static function admin()
    {
        return [
            new self([
                self::editFunction(),
                self::memberManageFunction()
            ], [
                new MemberLimitation()
            ])
        ];
    }

    /**
     * @return ProjectPolicy[]
     */
    public static function owner()
    {
        return [
            new self([
                self::editFunction(),
                self::removeFunction(),
                self::memberManageFunction()
            ], [
                new MemberLimitation()
            ])
        ];
    }
}
