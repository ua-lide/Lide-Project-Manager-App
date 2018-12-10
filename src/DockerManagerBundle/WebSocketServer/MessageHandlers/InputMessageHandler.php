<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 01/10/18
 * Time: 16:12
 */

namespace DockerManagerBundle\WebSocketServer\MessageHandlers;

use DockerManagerBundle\Exceptions\WrongMessageTypeException;
use DockerManagerBundle\WebSocketServer\MessageHandler;
use DockerManagerBundle\WebSocketServer\UserManager;

class InputMessageHandler implements MessageHandler
{
    public static $Type = 'input';

    public static $DATA_INPUT_KEY = 'input';

    public function handle(UserManager $sender, string $type, array &$data): bool
    {
        if ($type !== self::$Type) {
            throw new WrongMessageTypeException(self::$Type, $type);
        }

        if (!array_key_exists(self::$DATA_INPUT_KEY, $data)) {
            return false;
        }

        $input =& $data[self::$DATA_INPUT_KEY];

        if (!is_string($input)) {
            return false;
        }

        if ($sender->isContainerRunning()) {
            $sender->writeInput($input);
        } else {
            $sender->sendJson([
                'type' => 'error',
                'date' => [
                    'message' => 'No program is running'
                ]
            ]);
        }

        return true;
    }
}
