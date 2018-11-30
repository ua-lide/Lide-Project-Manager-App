<?php

namespace Tests\APIProjectBundle\Controller;


use AppBundle\Security\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase {


    public static function createAuthentificatedAdminClient(){

        $user = new User(1, 'admin', ['ROLE_ADMIN', 'ROLE_USER']);

        $client = static::createClient();
        $jwtAuth = $client->getContainer()->get('lexik_jwt_authentication.jwt_manager');
        $token = $jwtAuth->create($user);

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token));

        return $client;
    }

    public function testGetExistingProject() {
        $client = static::createAuthentificatedAdminClient();
        $client->request('GET', '/api/project/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testGetNonExistingProject() {
        $client = static::createClient();
        $client->request('GET', '/api/project/1564');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('Project not found', $client->getResponse()->getContent());
    }

    public function testGetProjects() {
        $client = static::createClient();
        $client->request('GET', '/api/projects');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetProjectsByUserAndName() {
        $client = static::createClient();
        $client->request('GET', '/api/projects?name=projet1&user_id=1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetProjectsByUser() {
        $client = static::createClient();
        $client->request('GET', '/api/projects?user_id=1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetProjectsByNonExistingUser() {
        $client = static::createClient();
        $client->request('GET', '/api/projects?user_id=2671');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('Projects not found', $client->getResponse()->getContent());
    }

    public function testGetProjectsByName() {
        $client = static::createClient();
        $client->request('GET', '/api/projects?name=projet1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetProjectsByNonExistingName() {
        $client = static::createClient();
        $client->request('GET', '/api/projects?name=WrongName');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('Projects not found', $client->getResponse()->getContent());
    }

    public function testSetProject() {
        $client = static::createClient();
        $client->request('PUT',
            '/api/project/1',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"project_name":"newname"}'
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('"projectName":"newname"', $client->getResponse()->getContent());
    }
}