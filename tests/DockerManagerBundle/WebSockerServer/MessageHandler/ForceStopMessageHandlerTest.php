<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 01/10/18
 * Time: 16:34
 */

namespace Tests\DockerManagerBundle\WebSockerServer\MessageHandler;

use DockerManagerBundle\Exceptions\WrongMessageTypeException;
use DockerManagerBundle\WebSocketServer\MessageHandlers\ForceStopMessageHandler;
use DockerManagerBundle\WebSocketServer\UserManager;
use PHPUnit\Framework\TestCase;
use Tests\DockerManagerBundle\WebSockerServer\Mocks\ConnectionInterfaceMock;

class ForceStopMessageHandlerTest extends TestCase
{

    public function testHandle()
    {

        $userManagerStub = $this->createMock(UserManager::class);


        $userManagerStub->expects($this->once())
            ->method('stopContainer')
            ->willReturn(true);

        $handler = new ForceStopMessageHandler();

        $data = [];
        /** @noinspection PhpParamsInspection */
        $this->assertEquals(true, $handler->handle($userManagerStub, 'force_stop', $data));
    }

    public function testHandleThrowExceptionOnWrongType()
    {
        $handler = new ForceStopMessageHandler();

        $userManager = new UserManager(new ConnectionInterfaceMock());

        $data = [];
        $this->expectException(WrongMessageTypeException::class);
        $handler->handle($userManager, "not_a_valid_type", $data);
    }

}
