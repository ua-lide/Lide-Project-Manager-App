<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 03/10/18
 * Time: 13:09
 */

namespace Tests\DockerManagerBundle\WebSocketServer;

use DockerManagerBundle\WebSocketServer\ProcessManager;
use PHPUnit\Framework\TestCase;

class ProcessManagerTest extends TestCase
{

    public function testStart()
    {
        $processManager = new ProcessManager("echo 1", ".");

        $this->assertTrue($processManager->start());
    }

    /**
     * @depends testStart
     */
    public function testIsStarted()
    {
        $processManager = new ProcessManager("echo 1", ".");

        $this->assertFalse($processManager->isStarted());

        $processManager->start();

        $this->assertTrue($processManager->isStarted());
    }

    public function readOutputProvider()
    {
        return [
            'simple' => ['echo 1', -1, "1\n"],
            'limited' => ['echo 1234', 2, '12']
        ];
    }


    /**
     * @depends      testStart
     * @dataProvider readOutputProvider
     * @param string $command
     * @param int $size
     * @param string $expected
     */
    public function testReadOutput(string $command, int $size, string $expected)
    {
        $processManager = new ProcessManager($command, ".");

        $processManager->start();

        $out = $processManager->readOutput($size);

        $this->assertEquals($expected, $out);
    }

    public function readErrorOutputProvider()
    {
        return [
            'simple' => ['echo 1 1>&2', -1, "1\n"],
            'limited' => ['echo 1234 1>&2', 2, '12']
        ];
    }


    /**
     * @depends      testStart
     * @dataProvider readErrorOutputProvider
     * @param string $command
     * @param int $size
     * @param string $expected
     */
    public function testReadErrorOutput(string $command, int $size, string $expected)
    {
        $processManager = new ProcessManager($command, ".");

        $processManager->start();

        $out = $processManager->readErrorOutput($size);

        $this->assertEquals($expected, $out);

    }

    /**
     * @depends testReadOutput
     */
    public function testWriteInput()
    {
        //This test might lock all test if we don't work. Need to find a better way to do this so it won't

        //This command will require an input and then echo it. If we consider that readOutput is working we can check that the input is send to the program
        $processManager = new ProcessManager('read x && echo $x', ".");
        $processManager->start();

        $processManager->writeInput("1\n");

        //Yes the following two lines looks stupid, but if we set the streams to blocking and try to read directly a deadlock happen. I don't know why, it's weird
        $processManager->setStreamsBlockingMode(false);
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        while ($processManager->isRunning()) {
        }
        $out = $processManager->readOutput();

        $this->assertEquals("1\n", $out);
    }

    public function testKill()
    {
        //The ping process run indefintly
        $processManager = new ProcessManager("ping 127.0.0.1", ".");

        $processManager->start();

        //Get the pid to check if it was killed correctly after
        $pid = $processManager->getStatus()['pid'];

        $processManager->kill();

        //This command return the name of the process with the given pid
        exec("ps -p ${pid} --no-heading", $output);

        //Output should be empty since the process was killed
        $this->assertEquals(0, count($output));
    }
//
//    public function testStop()
//    {
//
//    }
}
