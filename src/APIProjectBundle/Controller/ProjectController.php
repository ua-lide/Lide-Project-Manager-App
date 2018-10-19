<?php

namespace APIProjectBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{

    /**
     * @Post("/api/project")
     */
    public function createProject(Request $request) {

    }

}