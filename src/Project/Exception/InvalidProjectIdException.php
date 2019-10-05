<?php


namespace App\Project\Exception;


class InvalidProjectIdException extends \Exception
{
    /**
     * @param string $projectId
     * @return InvalidProjectIdException
     */
    public static function forIdString(string $projectId)
    {
        return new self(sprintf('Project ID "%s" has invalid format.', $projectId));
    }

}