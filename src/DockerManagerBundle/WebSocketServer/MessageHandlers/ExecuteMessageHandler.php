<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 01/10/18
 * Time: 14:18
 */

namespace DockerManagerBundle\WebSocketServer\MessageHandlers;

use DockerManagerBundle\Exceptions\WrongMessageTypeException;
use DockerManagerBundle\WebSocketServer\MessageHandler;
use DockerManagerBundle\WebSocketServer\ResponseBuilder;
use DockerManagerBundle\WebSocketServer\UserManager;
use DockerManagerBundle\BashCommands\Docker\DockerStartCommandBuilder;

class ExecuteMessageHandler implements MessageHandler
{

    /**
     * @var string
     */
    private $dockerMemoryAllocation;
    /**
     * @var int
     */
    private $dockerCpuAllocation;

    public static $Type = "execute";

    public static $CompileOptionsKey = "compile_options";
    public static $LaunchOptionsKey = "launch_options";
    public static $ImageOptionsKey = "image";


    public function __construct(string $dockerMemoryAllocation = "10M", int $dockerCpuAllocation = 1)
    {
        $this->dockerMemoryAllocation = $dockerMemoryAllocation;
        $this->dockerCpuAllocation = $dockerCpuAllocation;
    }

    public function handle(UserManager $sender, string $type, array &$data): bool
    {
        if ($type != self::$Type) {
            throw new WrongMessageTypeException(self::$Type, $type);
        }

        if (array_key_exists(self::$LaunchOptionsKey, $data)) {
            if (!is_string($data[self::$LaunchOptionsKey])) {
                return false;
            }
        }
        if (array_key_exists(self::$CompileOptionsKey, $data)) {
            if (!is_string($data[self::$CompileOptionsKey])) {
                return false;
            }
        }

        if (!array_key_exists(self::$ImageOptionsKey, $data) || !is_string($data[self::$ImageOptionsKey])) {
            return false;
        }
        $image = $data[self::$ImageOptionsKey];


        if (is_null($image)) {
            return false;
        }

        $commandBuilder = DockerStartCommandBuilder::newInstance($image)
            ->withPseudoTty()
            ->withBindMount($sender->getProjetAbsolutePath(), '/home/code')
            ->allocateMemory($this->dockerMemoryAllocation)
            ->allocateCpu($this->dockerCpuAllocation)
            ->withStartCommand($this->buildDockerEntryCommand($data));

        $started = $sender->startContainer($commandBuilder);

        $responseBuilder = new ResponseBuilder(ResponseBuilder::$STATUS);
        $responseBuilder->addDataField('is_running', $sender->isContainerRunning());

        $sender->sendJson($responseBuilder->buildArray());

        return true;
    }

    /**
     * @param array $options
     * @return string
     */
    protected function buildDockerEntryCommand(array $options) : string
    {
        //TODO real implementation
        return "/bin/bash -c \"cd code/src && g++ main.cpp && ./a.out\"";
    }
}
