<?php

namespace APIProjectBundle\Controller;


use APIProjectBundle\Entity\Projet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use APIProjectBundle\Controller\CreateProjectJob;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $job = new CreateProjectJob($projet);
        $job->handle();

        return $projet;

    }

    /**
     * @Rest\Get("/api/project/{idProject}/")
     * @Rest\View()
     */
    public function getProject(Request $request) {
        $projet = $this->getDoctrine()->getRepository('APIProjectBundle:Projet')
            ->find($request->get('id'));

        if (empty($projet)) {
            return new JsonResponse(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        return $projet;
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