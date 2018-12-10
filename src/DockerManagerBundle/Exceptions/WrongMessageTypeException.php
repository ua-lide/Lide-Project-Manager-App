<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 01/10/18
 * Time: 14:19
 */

namespace DockerManagerBundle\Exceptions;

class WrongMessageTypeException extends \RuntimeException
{
    public function __construct(string $expectedType, string $actualType)
    {
        parent::__construct("Expected ${expectedType}, got ${actualType}", 0, null);
    }
}
