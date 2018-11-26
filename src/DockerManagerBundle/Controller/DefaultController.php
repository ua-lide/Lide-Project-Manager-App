<?php

namespace DockerManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction()
    {
        // replace this example code with whatever you need
        dump($this->getUser());
        die();
        return new JsonResponse([
            'user' => $this->getUser()
        ]);
    }
}
