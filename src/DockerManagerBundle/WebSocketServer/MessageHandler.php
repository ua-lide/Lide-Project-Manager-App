<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 25/09/18
 * Time: 17:44
 */

namespace DockerManagerBundle\WebSocketServer;


interface MessageHandler
{
    public function handle(UserManager $sender, string $type, array &$data);
}