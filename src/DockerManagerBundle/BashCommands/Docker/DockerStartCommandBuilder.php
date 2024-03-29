<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 07/09/18
 * Time: 14:54
 */

namespace DockerManagerBundle\BashCommands\Docker;

use DockerManagerBundle\BashCommands\AbstractBashCommandBuilder;
use DockerManagerBundle\BashCommands\BashCommandBuilder;

class DockerStartCommandBuilder extends AbstractBashCommandBuilder
{
    private $addedHosts = [];
    private $inputFlag = false;
    private $pseudoTty = false;
    private $cpuCount;
    private $allocatedMemory;
    private $startCommand;
    private $dockerImageIdentifier;
    private $identifier;
    private $remove;
    private $withBindMount = false;
    private $hostPath;
    private $mountPath;

    /**
     * DockerCommandBuilder constructor.
     * @param string $imageIdentifier
     */
    public function __construct($imageIdentifier)
    {
        $this->dockerImageIdentifier = $imageIdentifier;
    }

    protected function buildCommand()
    {
        $builder = new BashCommandBuilder('docker run');

        if ($this->remove) {
            $builder->addRawArgument('--rm=true');
        }
        if ($this->identifier) {
            $builder->addFlagArgument('--name', $this->identifier);
        }
        foreach ($this->addedHosts as $addedHost) {
            $builder->addFlagArgument('--add-host', $addedHost);
        }

        if ($this->inputFlag) {
            $builder->addRawArgument('-a stdin');
        }

        if ($this->pseudoTty) {
            $builder->addRawArgument('-a stdout');
            $builder->addRawArgument('-a stderr');
            $builder->addRawArgument('-a stdin');
            $builder->addRawArgument('-i');
        }

        if (!is_null($this->cpuCount)) {
            $builder->addFlagArgument('--cpus', $this->cpuCount);
        }

        if (is_null($this->allocatedMemory)) {
            $builder->addFlagArgument('-m', $this->allocatedMemory);
        }

        if ($this->withBindMount) {
            $builder->addRawArgument('--mount type=bind,source=' . $this->hostPath . ',destination=' . $this->mountPath);
        }

        $builder->addRawArgument($this->dockerImageIdentifier);
        if ($this->startCommand) {
            $builder->addRawArgument($this->startCommand);
        }

        return $builder->build();
    }

    /**
     * Set the -i flag
     * @return $this|DockerStartCommandBuilder
     */
    public function withInput()
    {
        $this->inputFlag = true;
        return $this;
    }

    /**
     * Unset the -i flag
     * @return $this|DockerStartCommandBuilder
     */
    public function withoutInput()
    {
        $this->inputFlag = false;
        return $this;
    }

    public function withBindMount(string $hostPath, string $mountPath)
    {
        $this->withBindMount = true;
        $this->hostPath = $hostPath;
        $this->mountPath = $mountPath;
        return $this;
    }

    /**
     * @return $this
     */
    public function withRemove()
    {
        $this->remove = true;
        return $this;
    }

    /**
     * Set the -t flag (Allocate a pseudo-tty)
     * @return $this|DockerStartCommandBuilder
     */
    public function withPseudoTty()
    {
        $this->pseudoTty = true;
        return $this;
    }

    /**
     * Unset the -t flag (Allocate a pseudo-tty)
     * @return $this|DockerStartCommandBuilder
     */
    public function withoutPseudoTty()
    {
        $this->pseudoTty = false;
        return $this;
    }

    /**
     * @param integer $count
     * @return $this|DockerStartCommandBuilder
     */
    public function allocateCpu($count)
    {
        $this->cpuCount = $count;
        return $this;
    }

    /**
     * @param string $url
     * @param string $ip
     * @return $this|DockerStartCommandBuilder
     */
    public function addHost($url, $ip = null)
    {
        if (is_null($ip)) {
            $this->addedHosts[] = $url;
        } else {
            $this->addedHosts[] = "{$url}:{$ip}";
        }
        return $this;
    }

    /**
     * @param integer|string $memory
     * @return $this|DockerStartCommandBuilder
     */
    public function allocateMemory($memory)
    {
        $this->allocatedMemory = $memory;
        return $this;
    }


    /**
     * @param $startCommand
     * @return $this|DockerStartCommandBuilder
     */
    public function withStartCommand($startCommand)
    {
        $this->startCommand = $startCommand;
        return $this;
    }

    /**
     * @param $identifier
     * @return $this|DockerStartCommandBuilder
     */
    public function withIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }


    public static function newInstance($image)
    {
        return new static($image);
    }
}
