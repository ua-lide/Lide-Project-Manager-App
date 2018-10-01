<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 24/09/18
 * Time: 22:06
 */

namespace DockerManagerBundle\WebSocketServer;

use Lide\CommonsBundle\Entity\Environment;
use DockerManagerBundle\BashCommands\Docker\DockerStartCommandBuilder;
use Ratchet\ConnectionInterface;

class UserManager
{
    /**
     * @var resource $process
     */
    protected $process;

    /**
     * Index 0 for input, index 1 for output
     * @var resource[]
     */
    protected $pipes;

    private $lastReturnValue;

    /**
     * @var ConnectionInterface
     */
    private $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function readOutput()
    {
        if ($this->process) {
            return stream_get_contents($this->pipes[1]);
        }
        return null;
    }

    public function sendJson(array $data)
    {
        $this->connection->send(json_encode($data));
    }

    public function readStderr()
    {
        if ($this->process) {
            return stream_get_contents($this->pipes[2]);
        }
        return null;
    }

    public function writeInput(string $input)
    {
        if ($this->isContainerRunning()) {
            fwrite($this->pipes[0], $input);
        }
    }

    public function getStatus()
    {
        if (is_null($this->process)) {
            return null;
        }
        return proc_get_status($this->process);
    }

    public function startContainer(DockerStartCommandBuilder $commandBuilder)
    {
        $descriptorSpec = [
            0 => ["pipe", "r"], // stdin est un pipe où le processus va lire
            1 => ["pipe", "w"], // stdout est un pipe où le processus va écrire
            2 => ["pipe", "w"]  // stdout est un pipe où le processus va écrire
        ];

        // Then start it with proc_open
        $this->process= proc_open($commandBuilder->build(), $descriptorSpec, $this->pipes, "/home", null);

        //Set the pipe in non blocking
        stream_set_blocking($this->pipes[1], false);
        stream_set_blocking($this->pipes[2], false);

        return true;
    }

    public function stopContainer(): bool
    {
        // TODO
        return true;
    }

    /**
     * @return mixed
     */
    public function getLastReturnValue()
    {
        return $this->lastReturnValue;
    }

    public function isContainerRunning() : bool
    {
        if (!is_null($this->process)) {
            $statusArray = proc_get_status($this->process);
            if ($statusArray['running']) {
                return true;
            } else {
                // Clean up process
                fclose($this->pipes[0]);
                fclose($this->pipes[1]);
                $this->lastReturnValue = proc_close($this->process);
            }
        }
        return false;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    public function getEnvironment() : Environment
    {
        //TODO real stuff
        $env = new Environment();
        $env->setName("Test");
        $env->setImage("gpp");
        $env->setActivated(true);
        return $env;
    }

    /**
     * Return the absolute path of the selected project
     * @return string
     */
    public function getProjetAbsolutePath() : string
    {
        //TODO
        return "/";
    }

    /**
     * Get the docker image name for the environment of the user project
     * @return string
     */
    public function getProjectsEnvironmentsDockerImage() : string
    {
        //TODO implementation
        return "gpp";
    }
}
