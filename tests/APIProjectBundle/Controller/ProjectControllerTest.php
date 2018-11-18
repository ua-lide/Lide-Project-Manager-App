<?php

namespace Tests\APIProjectBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase {


    public function testGetExistingProject() {
        $client = static::createClient();
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
}