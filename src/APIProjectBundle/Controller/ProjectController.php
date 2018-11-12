<?php

namespace APIProjectBundle\Controller;


use APIProjectBundle\Entity\Projet;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use APIProjectBundle\Controller\CreateProjectJob;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


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
            ->find($request->get('idProject'));

        if (empty($projet)) {
            return new JsonResponse(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        return $projet;
    }

    /**
     * @Rest\Get("/api/projects")
     * @Rest\View()
     * @Rest\QueryParam(name="name")
     * @Rest\QueryParam(name="user")
     * @Rest\QueryParam(name="page")
     */
    public function getProjects(ParamFetcherInterface $paramFetcher) {
        $projects = $this->getDoctrine()->getRepository('APIProjectBundle:Project')
            ->findBy(
                array('name'=>$paramFetcher->get('name')),
                array('user_id'=>$paramFetcher->get('user'))
            );

        return $projects;
    }

    /**
     * @Rest\Put("/api/project/{idProject}")
     * @Rest\View(
     *     statusCode = 200
     * )
     */
    public function setProject(Request $request) {
        $projet = $this->getDoctrine()->getRepository('APIProjectBundle:Projet')
            ->find($request->get('idProject'));

        if (empty($projet)) {
            return new JsonResponse(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm($projet);
        $form->submit($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($projet);
            $this->getDoctrine()->getManager()->flush();

            return $projet;
        }
    }

    /**
     * @Rest\Delete("/api/project/{idProject}")
     * @Rest\View(
     *     statusCode = 200
     * )
     */
    public function deleteProject(Request $request) {
        $projectRepository = $this->getDoctrine()->getRepository('APIProjectBundle:Project');
        $projet = $projectRepository->find($request->get('idProject'));

        if (empty($projet)) {
            return new JsonResponse(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        } else {
            $projectRepository->deleteProject($projet);
        }
    }
}