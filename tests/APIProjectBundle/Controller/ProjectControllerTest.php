<?php

namespace Tests\APIProjectBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase {

    use JWTAuthenticationClient;

    public function testGetExistingProject() {
        $client = static::createAuthentificatedAdminClient();
        $client->request('GET', '/api/project/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        dump($client->getResponse()->getContent());
    }

    public function testGetNonExistingProject() {
        $x = static::createClient();
        $client = static::createAuthentificatedAdminClient();
        $client->request('GET', '/api/project/1564');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('Project not found', $client->getResponse()->getContent());
    }

    public function testGetProjects() {
        $client = static::createAuthentificatedAdminClient();
        $client->request('GET', '/api/projects');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetProjectsByUserAndName() {
        $client = static::createAuthentificatedAdminClient();
        $client->request('GET', '/api/projects?name=newname&user_id=1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetProjectsByUser() {
        $client = static::createAuthentificatedAdminClient();
        $client->request('GET', '/api/projects?user_id=1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetProjectsByNonExistingUser() {
        $client = static::createAuthentificatedAdminClient();
        $client->request('GET', '/api/projects?user_id=2671');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('Projects not found', $client->getResponse()->getContent());
    }

    public function testGetProjectsByName() {
        $client = static::createAuthentificatedAdminClient();
        $client->request('GET', '/api/projects?name=newname');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetProjectsByNonExistingName() {
        $client = static::createAuthentificatedAdminClient();
        $client->request('GET', '/api/projects?name=WrongName');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('Projects not found', $client->getResponse()->getContent());
    }

    public function testSetProject() {
        $client = static::createAuthentificatedAdminClient();
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