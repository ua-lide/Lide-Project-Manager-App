<?php

namespace APIProjectBundle\Controller;


use APIProjectBundle\Entity\Projet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{

    /**
     * @Rest\Post("/api/project")
     * @Rest\View(
     *     statusCode = 200
     * )
     * @ParamConverter("projet", converter="fos_rest.request_body")
     */
    public function createProject(Projet $projet) {

        $em = $this->getDoctrine()->getManager();

        $em->persist($projet);
        $em->flush();

        //traitement des données (classe CreateProjectJob)

        return $projet;

    }

    /**
     * @Rest\Get("/api/project/{idProject}/")
     */
    public function getProject() {

    }

    /**
     * @Rest\Get("/api/projects?query")
     */
    public function getProjects() {

    }

    /**
     * @Rest\Put("/api/project/{idProject}")
     */
    public function setProject() {

    }

    /**
     * @Rest\Delete("/api/project/{idProject}")
     */
    public function deleteProject() {

    }
}