<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 01/10/18
 * Time: 16:32
 */

namespace DockerManagerBundle\WebSocketServer\MessageHandlers;


use DockerManagerBundle\Exceptions\WrongMessageTypeException;
use DockerManagerBundle\WebSocketServer\MessageHandler;
use DockerManagerBundle\WebSocketServer\UserManager;

class ForceStopMessageHandler implements MessageHandler
{

    public static $Type = 'force_stop';

    public function handle(UserManager $sender, string $type, array &$data): bool
    {
        if (self::$Type !== $type) {
            throw new WrongMessageTypeException(self::$Type, $type);
        }

        return $sender->stopContainer();
    }
}