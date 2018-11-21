<?php

namespace Tests\APIProjectBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FichierControllerTest extends WebTestCase {

    public function testGetExistingFile() {
        $client = static::createClient();
        $client->request('GET', '/api/project/2/files/4');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetNonExistingFile() {
        $client = static::createClient();
        $client->request('GET', '/api/project/1/files/6546');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('File not found', $client->getResponse()->getContent());
    }

    public function testGetExistingFileWithWrongProject() {
        $client = static::createClient();
        $client->request('GET', '/api/project/1/files/4');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertContains('appartient pas au projet specifie', $client->getResponse()->getContent());
    }

    public function testGetFiles() {
        $client = static::createClient();
        $client->request('GET', '/api/project/2/files');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}