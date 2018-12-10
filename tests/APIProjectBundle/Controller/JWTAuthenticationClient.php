<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 30/11/18
 * Time: 15:04
 */

namespace Tests\APIProjectBundle\Controller;


use AppBundle\Security\User;

use Symfony\Bundle\FrameworkBundle\Client;
trait JWTAuthenticationClient
{
    public static function createAuthentificatedAdminClient(): Client{

        $user = new User(1, 'admin', ['ROLE_ADMIN', 'ROLE_USER']);

        /** @var Client $client */
        $client = static::createClient();
        $jwtAuth = $client->getContainer()->get('lexik_jwt_authentication.jwt_manager');
        $token = $jwtAuth->create($user);

        /** @var Client $client */
        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        return $client;
    }
}