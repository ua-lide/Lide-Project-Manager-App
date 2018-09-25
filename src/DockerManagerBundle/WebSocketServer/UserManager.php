<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 24/09/18
 * Time: 22:06
 */

namespace DockerManagerBundle\WebSocketServer;


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

    public function readOutput(){
        if($this->process){
            return stream_get_contents($this->pipes[1]);
        }
        return null;
    }

    public function readStderr(){
        if($this->process){
            return stream_get_contents($this->pipes[2]);
        }
        return null;
    }

    public function writeInput(string $input){
        if($this->isContainerRunning()){
            fwrite($this->pipes[0], $input);
        }
    }

    public function getStatus(){
        if(is_null($this->process)){
            return null;
        }
        return proc_get_status($this->process);
    }

    public function startContainer(){
        //Build Command
        // TODO

        $cmd = "docker run -a stdin -a stdout -a stderr gpp /bin/bash -c \"ping 8.8.8.8\"";

        $descriptorspec = [
            0 => ["pipe", "r"],  // // stdin est un pipe où le processus va lire
            1 => ["pipe", "w"],  // stdout est un pipe où le processus va écrire
            2 => ["pipe", "w"] // stdout est un pipe où le processus va écrire
        ];

        // Then start it with proc_open
        $this->process= proc_open($cmd, $descriptorspec, $this->pipes, "/home", null);

        //Set the pipe in non blocking
        stream_set_blocking($this->pipes[1], false);
        stream_set_blocking($this->pipes[2], false);
    }

    /**
     * @return mixed
     */
    public function getLastReturnValue()
    {
        return $this->lastReturnValue;
    }

    public function isContainerRunning() : bool {
        if(!is_null($this->process)){
            $statusArray = proc_get_status($this->process);
            if($statusArray['running']){
                return true;
            }else{
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
}