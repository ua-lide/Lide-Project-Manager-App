<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 05/12/18
 * Time: 10:08
 */

namespace DockerManagerBundle\WebSocketServer\MessageHandlers;


use DockerManagerBundle\WebSocketServer\MessageHandler;
use DockerManagerBundle\WebSocketServer\UserManager;

class AuthentificationMessageHandler implements MessageHandler
{

    public static $Type = "auth";

    private static $JwtKey = "jwt";

    private static $ProjectIdKey = "project_id";

    public function handle(UserManager $sender, string $type, array &$data): bool
    {
        if($sender->isAuthentificated()){
            return true;
        }

        if(!array_key_exists(self::$JwtKey, $data) || !is_string($data[self::$JwtKey])){
            return false;
        }
        if(!array_key_exists(self::$ProjectIdKey, $data)
            || (!is_string($data[self::$ProjectIdKey]) && !is_int($data[self::$ProjectIdKey]))){
            return false;
        }

        if($sender->authenticate($data[self::$JwtKey], (int) $data[self::$ProjectIdKey])){
            $sender->sendJson([
                'type' => 'confirm_auth',
                'data' => [
                    'project_id' => $data[self::$ProjectIdKey],
                    'user_id' => $sender->getUserId(),
                ]
            ]);
            return true;
        }else{
            return false;
        }
    }
}