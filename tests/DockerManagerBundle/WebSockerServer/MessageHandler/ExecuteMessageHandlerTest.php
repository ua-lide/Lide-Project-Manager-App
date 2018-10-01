<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 01/10/18
 * Time: 14:42
 */

namespace Tests\DockerManagerBundle\WebSockerServer\MessageHandler;

use DockerManagerBundle\Exceptions\WrongMessageTypeException;
use DockerManagerBundle\WebSocketServer\MessageHandlers\ExecuteMessageHandler;
use DockerManagerBundle\WebSocketServer\UserManager;
use PHPUnit\Framework\TestCase;
use Tests\DockerManagerBundle\WebSockerServer\Mocks\ConnectionInterfaceMock;

class ExecuteMessageHandlerTest extends TestCase
{

    /**
     *
     */
    public function testHandle()
    {

        $userManagerStub  = $this->createMock(UserManager::class);

        $userManagerStub->expects($this->once())
            ->method('startContainer')
            ->willReturn(true);

        $userManagerStub->method('getProjetAbsolutePath')
            ->willReturn('/');
        $userManagerStub->method('getProjectsEnvironmentsDockerImage')
            ->willReturn("test");

        $handler = new ExecuteMessageHandler();

        $data = [
            'compile_options' => '-Wall',
        ];
        $handler->handle($userManagerStub, ExecuteMessageHandler::$Type, $data);
    }

    /**
     */
    public function testHandleThrowExceptionOnWrongType()
    {
        $handler = new ExecuteMessageHandler();

        $userManager = new UserManager(new ConnectionInterfaceMock());

        $data = [];
        $this->expectException(WrongMessageTypeException::class);
        $handler->handle($userManager, "not_a_valid_type", $data);
    }
}
