<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 01/10/18
 * Time: 15:42
 */

namespace DockerManagerBundle\WebSocketServer\MessageHandlers;

use DockerManagerBundle\Exceptions\WrongMessageTypeException;
use DockerManagerBundle\WebSocketServer\MessageHandler;
use DockerManagerBundle\WebSocketServer\ResponseBuilder;
use DockerManagerBundle\WebSocketServer\UserManager;

class GetStatusMessageHandler implements MessageHandler
{
    public static $Type = "get_status";


    public function handle(UserManager $sender, string $type, array &$data): bool
    {
        if ($type != self::$Type) {
            throw new WrongMessageTypeException(self::$Type, $type);
        }

        $isRunning = $sender->isContainerRunning();

        $responseBuilder = new ResponseBuilder(ResponseBuilder::$STATUS);

        $responseBuilder->addDataField('is_running', $isRunning);

        $sender->sendJson($responseBuilder->buildArray());

        return true;
    }
}
