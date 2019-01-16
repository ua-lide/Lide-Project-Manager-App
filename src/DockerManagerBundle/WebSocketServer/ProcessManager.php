<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 03/10/18
 * Time: 12:29
 */

namespace DockerManagerBundle\WebSocketServer;

use DockerManagerBundle\Exceptions\ProcessAlreadyStartedException;
use DockerManagerBundle\Exceptions\ProcessNotStartedException;
use DockerManagerBundle\Exceptions\ProcessStoppedException;

/**
 * Class ProcessManager
 * Used to manage a process
 * @package DockerManagerBundle\WebSocketServer
 */
class ProcessManager
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

    /**
     * @var string
     */
    private $command;
    /**
     * @var string
     */
    private $directory;

    /**
     * @var bool
     */
    private $started = false;
    private $stopped = false;

    public function __construct(string $command, string $directory)
    {
        $this->command = $command;
        $this->directory = $directory;
    }

    /**
     * Start the process. Throw a ProcessAlreadyStartedException if the process is already started
     * @return bool
     * @throws ProcessAlreadyStartedException
     */
    public function start()
    {
        if ($this->started) {
            throw new ProcessAlreadyStartedException();
        }

        $descriptorSpec = [
            0 => ["pipe", "r"], // stdin est un pipe où le processus va lire
            1 => ["pipe", "w"], // stdout est un pipe où le processus va écrire
            2 => ["pipe", "w"]  // stdout est un pipe où le processus va écrire
        ];

        // Then start it with proc_open
        if (($this->process = proc_open($this->command, $descriptorSpec, $this->pipes, $this->directory, null)) === false) {
            return false;
        }


        $this->started = true;

        return true;
    }

    /**
     * Stop the process nicel
     */
    public function close()
    {
        if (!$this->started) {
            throw new ProcessNotStartedException();
        }

        if ($this->stopped) {
            return;
        }

        fclose($this->pipes[0]);
        fclose($this->pipes[1]);
        fclose($this->pipes[2]);

        proc_close($this->process);
        $this->stopped = true;
    }

    /**
     * Kill the process and all its children
     */
    public function kill()
    {
        if (!$this->started) {
            throw new ProcessNotStartedException();
        }
        if ($this->stopped) {
            return;
        }

        //Can't use proc_terminate because it might not kill all child process

        $status = proc_get_status($this->process);
        if ($status['running'] == true) { //check it's running
            //close all pipes that are still open
            fclose($this->pipes[1]); //stdout
            fclose($this->pipes[2]); //stderr
            //get the parent pid of the process we want to kill
            $ppid = $status['pid'];
            //use ps to get all the children of this process, and kill them
            $pids = preg_split('/\s+/', `ps -o pid --no-heading --ppid $ppid`);
            foreach ($pids as $pid) {
                if (is_numeric($pid)) {
                    posix_kill($pid, 9); //9 is the SIGKILL signal
                }
            }

            proc_close($this->process);
        }
        $this->stopped = true;
    }

    /**
     * Read $size bytes on the process output. Throw a ProcessNotStartedException if the process was not started beforehand
     * @param int $size the number of bytes to read. Set to -1 to read all available output
     * @return bool|string
     * @throws ProcessNotStartedException if the process is not started
     * @throws ProcessStoppedException if the process was stopped
     */
    public function readOutput(int $size = -1)
    {
        if (!$this->started) {
            throw new ProcessNotStartedException();
        }
        if ($this->stopped) {
            throw new ProcessStoppedException();
        }

        return stream_get_contents($this->pipes[1], $size);
    }

    /**
     * Read $size bytes on the process error output. Throw a ProcessNotStartedException if the process was not started beforehand
     * @param int $size the number of bytes to read. Set to -1 to read all available output
     * @return bool|string
     * @throws ProcessNotStartedException if the process is not started
     * @throws ProcessStoppedException if the process was stopped
     */
    public function readErrorOutput(int $size = -1)
    {
        if (!$this->started) {
            throw new ProcessNotStartedException();
        }
        if ($this->stopped) {
            throw new ProcessStoppedException();
        }

        return stream_get_contents($this->pipes[2], $size);
    }

    /**
     * Set the blocking mode of the output and error pipes to the process.
     * @param bool $mode If FALSE, the output streams will be switched to non-blocking mode, and if TRUE, it will be switched to blocking mode
     */
    public function setStreamsBlockingMode(bool $mode)
    {
        //Set the pipe in non blocking
        stream_set_blocking($this->pipes[1], $mode);
        stream_set_blocking($this->pipes[2], $mode);
    }

    /**
     * Write to the process
     * @param string $input
     * @throws ProcessNotStartedException if the process is not started
     * @throws ProcessStoppedException if the process was stopped
     */
    public function writeInput(string $input)
    {
        if (!$this->started) {
            throw new ProcessNotStartedException();
        }
        if ($this->stopped) {
            throw new ProcessStoppedException();
        }

        fwrite($this->pipes[0], $input);
    }

    /**
     * Get information about the process.
     * @return array|bool  An array of collected information on success, and FALSE on failure. The returned array contains the following elements:
     *  * command (string) : The command string that was passed in the constructor
     *  * pid (int) : The process id
     *  * running (bool) :  TRUE if the process is still running, FALSE if it has terminated.
     *  * signaled (bool) :  TRUE if the child process has been terminated by an uncaught signal. Always set to FALSE on Windows.
     *  * stopped (bool) :   TRUE if the child process has been stopped by a signal. Always set to FALSE on Windows.
     *  * exitcode (int) :  The exit code returned by the process (which is only meaningful if running is FALSE). Only first call of this function return real value, next calls return -1.
     *  * termsig (int) :  The number of the signal that caused the child process to terminate its execution (only meaningful if signaled is TRUE).
     *  * stopsig (int) :   The number of the signal that caused the child process to stop its execution (only meaningful if stopped is TRUE).
     * @throws ProcessNotStartedException if the process is not started
     * @see proc_get_status()
     */
    public function getStatus(): array
    {
        if (!$this->started) {
            throw new ProcessNotStartedException();
        }

        return proc_get_status($this->process);
    }

    /**
     * @return bool TRUE if the process has been started, false otherwise
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * Is the process running
     * @return bool TRUE if the process is running, FALSE if it wasn't started or it has terminated
     */
    public function isRunning(): bool
    {
        if (!$this->started) {
            return false;
        }

        return $this->getStatus()['running'];
    }
}
