<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 01/10/18
 * Time: 15:45
 */

namespace Tests\DockerManagerBundle\WebSocketServer\MessageHandler;

use DockerManagerBundle\Exceptions\WrongMessageTypeException;
use DockerManagerBundle\WebSocketServer\MessageHandlers\GetStatusMessageHandler;
use DockerManagerBundle\WebSocketServer\UserManager;
use PHPUnit\Framework\TestCase;
use Tests\DockerManagerBundle\WebSocketServer\Mocks\ConnectionInterfaceMock;

class GetStatusMessageHandlerTest extends TestCase
{
    public function testHandle()
    {
        $handler = new GetStatusMessageHandler();

        $userManagerStub = $this->createMock(UserManager::class);

        $userManagerStub->method('isContainerRunning')
            ->willReturn(true);

        $userManagerStub->expects($this->once())
            ->method('sendJson')
            ->withConsecutive([
                $this->equalTo([// Expected message to be sent
                    'type' => 'status',
                    'data' => [
                        'is_running' => true
                    ]
                ])
            ]);
        $data = [];
        /** @noinspection PhpParamsInspection */
        $handler->handle($userManagerStub, GetStatusMessageHandler::$Type, $data);
    }


    /**
     */
    public function testHandleThrowExceptionOnWrongType()
    {
        $handler = new GetStatusMessageHandler();

        $userManager = new UserManager(new ConnectionInterfaceMock(), "/home");

        $data = [];
        $this->expectException(WrongMessageTypeException::class);
        $handler->handle($userManager, "not_a_valid_type", $data);
    }
}
