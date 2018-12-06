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
    /**
     * @var string
     */
    private $jwtKeyPath;
    private $baseProjectPath;


    /**
     * UserManagerFactory constructor.
     * @param string $jwtKeyPath
     * @param string $baseProjectPath
     */
    public function __construct(string $jwtKeyPath, string $baseProjectPath)
    {
        $this->jwtKeyPath = $jwtKeyPath;
        $this->baseProjectPath = $baseProjectPath;
    }

    public function create(ConnectionInterface $connection)
    {
        return new UserManager($connection, $this->baseProjectPath, ".", $this->jwtKeyPath);
    }
}