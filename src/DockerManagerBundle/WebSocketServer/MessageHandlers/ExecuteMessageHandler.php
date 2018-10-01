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

    public function __construct(string $dockerMemoryAllocation = "10M", int $dockerCpuAllocation = 1)
    {
        $this->dockerMemoryAllocation = $dockerMemoryAllocation;
        $this->dockerCpuAllocation = $dockerCpuAllocation;
    }

    public function handle(UserManager $sender, string $type, array &$data)
    {
        if ($type != self::$Type) {
            throw new WrongMessageTypeException(self::$Type, $type);
        }

        if (array_key_exists(self::$LaunchOptionsKey, $data)) {
            if (!is_string($data[self::$LaunchOptionsKey])) {
                unset($data[self::$LaunchOptionsKey]);
            }
        }
        if (array_key_exists(self::$CompileOptionsKey, $data)) {
            if (!is_string($data[self::$CompileOptionsKey])) {
                unset($data[self::$CompileOptionsKey]);
            }
        }


        $image = $sender->getProjectsEnvironmentsDockerImage();

        if (is_null($image)) {
            return false;
        }

        $commandBuilder = DockerStartCommandBuilder::newInstance($image)
            ->withPseudoTty()
            ->allocateMemory($this->dockerMemoryAllocation)
            ->allocateCpu($this->dockerCpuAllocation)
            ->withStartCommand($this->buildDockerEntryCommand($data));

        $sender->startContainer($commandBuilder);

        return true;
    }

    /**
     * @return string
     */
    protected function buildDockerEntryCommand(array $options) : string
    {
        //TODO real implementation
        return "/bin/bash -c \"ping 8.8.8.8\"";
    }
}
