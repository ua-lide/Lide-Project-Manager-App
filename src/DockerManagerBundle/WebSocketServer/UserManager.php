<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 24/09/18
 * Time: 22:06
 */

namespace DockerManagerBundle\WebSocketServer;

use DockerManagerBundle\BashCommands\Docker\DockerStartCommandBuilder;
use Namshi\JOSE\JWS;
use Ratchet\ConnectionInterface;
use Symfony\Component\Filesystem\Filesystem;

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

    private $isAuthentificated;
    /**
     * @var string
     */
    private $jwtKeyPath;

    /**
     * UserManager constructor.
     * @param ConnectionInterface $connection
     * @param string $projectPath
     * @param string $dockerExecutionDirectory
     * @param string $jwtKeyPath
     */
    public function __construct(ConnectionInterface $connection, string $projectPath, string $dockerExecutionDirectory = ".", $jwtKeyPath = '')
    {
        $this->connection = $connection;
        $this->projectPath = $projectPath;
        $this->dockerExecutionDirectory = $dockerExecutionDirectory;
        $this->isAuthentificated = false;
        $this->jwtKeyPath = $jwtKeyPath;
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

    public function setProjectId()
    {
        return $this->project_id;
    }

    public function sendJson(array $data)
    {
        $this->connection->send(json_encode($data));
    }

    public function startContainer(DockerStartCommandBuilder $commandBuilder)
    {
        $cmd = $commandBuilder->build();
        echo "Starting container with command : {$cmd}\n";
        $this->processManager = new ProcessManager($cmd, $this->dockerExecutionDirectory);

        $this->processManager->start();
        $this->processManager->setStreamsBlockingMode(false);
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
            $this->connection->send(json_encode([
                'type' => 'end',
                'data' => [
                    'return' => 0
                ]
            ]));
            $this->processManager->close();
            $this->processManager = null;
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

    /**
     * Return the absolute path of the selected project
     * @return string
     */
    public function getProjetAbsolutePath() : string
    {
        return $this->projectPath . "/" . $this->user_id . "/" . $this->project_id;
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

    public function isAuthentificated() : bool
    {
        return $this->isAuthentificated;
    }

    public function authenticate(string $jwt, int $projectId) : bool
    {
        echo "Authenticate user\n";
        try {
            echo "Loading jws\n";
            $jws = JWS::load($jwt);
        } catch (\InvalidArgumentException $e) {
            return false;
        }
        echo "Verify\n";
        if (!$jws->verify(file_get_contents($this->jwtKeyPath))) {
            echo "Verify not ok\n";
            return false;
        }
        echo "Verify Ok\n";

        $payload = $jws->getPayload();
        var_dump($payload);
        $this->user_id = $payload['id'];

        $this->project_id = $projectId;
        if($this->doesProjectExistsAndBelongToUser()){
            echo "Check project ok\n";
            $this->isAuthentificated = true;
        }else{
            echo "Check project not ok\n";
        }

        return $this->isAuthentificated;
    }

    public function doesProjectExistsAndBelongToUser(): bool{
        $fileSystem = new Filesystem();
        echo "Checking project {$this->getProjetAbsolutePath()}";
        return $fileSystem->exists($this->getProjetAbsolutePath());
    }
}
