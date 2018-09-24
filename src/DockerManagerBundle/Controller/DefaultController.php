<?php

namespace DockerManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DockerManagerBundle:Default:index.html.twig');
    }
}
