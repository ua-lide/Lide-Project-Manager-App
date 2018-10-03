<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 02/10/18
 * Time: 16:43
 */

namespace DockerManagerBundle\WebSocketServer;


use Ratchet\ConnectionInterface;

class UserManagerFactory
{
    public function create(ConnectionInterface $connection, string $projectPath)
    {
        return new UserManager($connection, $projectPath);
    }
}