<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 24/09/18
 * Time: 22:06
 */

namespace DockerManagerBundle\WebSocketServer;


class UserManager
{
    /**
     * @var resource $process
     */
    protected $process;

    protected $pipes;

    private $lastReturnValue;

    public function readOutput(){
        if($this->process){
            return stream_get_contents($this->pipes[1]);
        }
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

    public function startContainer(){
        // TODO
    }

    /**
     * @return mixed
     */
    public function getLastReturnValue()
    {
        return $this->lastReturnValue;
    }
}