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
     * @var ProcessManager
     */
    protected $processManager;


    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var string
     */
    private $projectPath;

    private $user_id;

    private $project_id;
    /**
     * @var string
     */
    private $dockerExecutionDirectory;

    /**
     * UserManager constructor.
     * @param ConnectionInterface $connection
     * @param string $projectPath
     * @param string $dockerExecutionDirectory
     */
    public function __construct(ConnectionInterface $connection, string $projectPath, string $dockerExecutionDirectory = ".")
    {
        $this->connection = $connection;
        $this->projectPath = $projectPath;
        $this->dockerExecutionDirectory = $dockerExecutionDirectory;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getProjectId()
    {
        return $this->project_id;
    }

    public function sendJson(array $data)
    {
        $this->connection->send(json_encode($data));
    }

    public function startContainer(DockerStartCommandBuilder $commandBuilder)
    {

        $this->processManager = new ProcessManager($commandBuilder->build(), $this->dockerExecutionDirectory);

        $this->processManager->start();

        return true;
    }

    /**
     * @return ProcessManager|null
     */
    public function getProcessManager()
    {
        return $this->processManager;
    }

    public function stopContainer(): bool
    {
        if (!is_null($this->processManager)) {
            $this->processManager->close();
        }
        return true;
    }


    public function isContainerRunning() : bool
    {
        if (!is_null($this->processManager)) {
            return $this->processManager->isRunning();
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

    public function writeInput(string $input)
    {
        $this->processManager->writeInput($input);
    }

}
