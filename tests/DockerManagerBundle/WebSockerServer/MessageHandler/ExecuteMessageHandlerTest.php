<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 01/10/18
 * Time: 14:42
 */

namespace Tests\DockerManagerBundle\WebSocketServer\MessageHandler;

use DockerManagerBundle\Exceptions\WrongMessageTypeException;
use DockerManagerBundle\WebSocketServer\MessageHandlers\ExecuteMessageHandler;
use DockerManagerBundle\WebSocketServer\UserManager;
use PHPUnit\Framework\TestCase;
use Tests\DockerManagerBundle\WebSocketServer\Mocks\ConnectionInterfaceMock;

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

        /** @noinspection PhpParamsInspection */
        $this->assertEquals(true, $handler->handle($userManagerStub, ExecuteMessageHandler::$Type, $data));
    }

    public function testHandleNoStart()
    {
        $userManagerStub = $this->createMock(UserManager::class);


        $userManagerStub->expects($this->once())
            ->method('startContainer')
            ->willReturn(false);

        $userManagerStub->method('getProjetAbsolutePath')
            ->willReturn('/');
        $userManagerStub->method('getProjectsEnvironmentsDockerImage')
            ->willReturn("test");
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
        $userManagerStub->method('isContainerRunning')
            ->willReturn(true);

        $handler = new ExecuteMessageHandler();

        $data = [
            'compile_options' => '-Wall',
        ];

        /** @noinspection PhpParamsInspection */
        $this->assertEquals(true, $handler->handle($userManagerStub, ExecuteMessageHandler::$Type, $data));
    }


    public function handleNonValidDataProvider()
    {
        return [
            'compile_option is not string' => [
                ['compile_options' => ['a']]
            ],
            'launch_options is not string' => [
                ['launch_options' => ['a']]
            ]
        ];
    }

    /**
     * @param $data
     * @dataProvider handleNonValidDataProvider
     */
    public function testHandleNonValidData(array $data)
    {
        $userManagerStub = $this->createMock(UserManager::class);


        $userManagerStub->expects($this->never())
            ->method('writeInput');

        $handler = new ExecuteMessageHandler();

        /** @noinspection PhpParamsInspection */
        $this->assertEquals(false, $handler->handle($userManagerStub, 'execute', $data));

    }

    /**
     */
    public function testHandleThrowExceptionOnWrongType()
    {
        $handler = new ExecuteMessageHandler();

        $userManager = new UserManager(new ConnectionInterfaceMock(), "/home");

        $data = [];
        $this->expectException(WrongMessageTypeException::class);
        $handler->handle($userManager, "not_a_valid_type", $data);
    }
}
