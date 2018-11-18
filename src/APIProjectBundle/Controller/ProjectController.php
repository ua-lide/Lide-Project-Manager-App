<?php

namespace APIProjectBundle\Controller;


use APIProjectBundle\Entity\Projet;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use APIProjectBundle\Controller\CreateProjectJob;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     */
    public function createProjectAction(Request $request) {
        $body = json_decode($request->getContent(), true);

        $projet = new Projet();
        $projet->setProjectName($body['name']);
        $projet->setEnvironnementId($body['environnement_id']);
        //TODO: récupérer le user
        $projet->setUserId(1);
        if (empty($body['is_public'])) {
            $projet->setIsPublic(false);
        } else {
            $projet->setIsPublic($body['is_public']);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($projet);
        $em->flush();

//        $job = new CreateProjectJob($projet);
//        $job->handle();

        return $projet;

    }

    /**
     * @Rest\Get("/api/project/{idProject}")
     * @Rest\View()
     */
    public function getProjectAction(Request $request) {
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
     *
     * @Rest\QueryParam(name="name", nullable=true)
     * @Rest\QueryParam(name="user_id", nullable=true)
     * @Rest\QueryParam(name="page", nullable=true)
     */
    public function getProjectsAction(ParamFetcherInterface $paramFetcher) {
        $name = $paramFetcher->get('name');
        $user_id = $paramFetcher->get('user_id');

        $projectRepository = $this->getDoctrine()->getRepository('APIProjectBundle:Projet');

        if (empty($name) && empty($user_id)) {
            $projects = $projectRepository->findAll();
        } else {
            $projects = $projectRepository->findBy(
                array('project_name'=>$name, 'user_id'=>$user_id)
            );
        }

        if (empty($projects)) {
            return new JsonResponse(['message' => 'Projects not found'], Response::HTTP_NOT_FOUND);
        }

        return $projects;
    }

    /**
     * @Rest\Put("/api/project/{idProject}")
     * @Rest\View(
     *     statusCode = 200
     * )
     */
    public function setProjectAction(Request $request) {
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
    public function deleteProjectAction(Request $request) {
        $projectRepository = $this->getDoctrine()->getRepository('APIProjectBundle:Projet');
        $projet = $projectRepository->find($request->get('idProject'));

        if (empty($projet)) {
            return new JsonResponse(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        } else {
            $projectRepository->deleteProject($projet);
        }
    }
}