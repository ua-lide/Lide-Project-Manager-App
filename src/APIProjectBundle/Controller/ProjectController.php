<?php

namespace APIProjectBundle\Controller;


use APIProjectBundle\Entity\Fichier;
use APIProjectBundle\Entity\Projet;
use APIProjectBundle\Form\ProjetType;
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
        } else if (empty($name)) {
            $projects = $projectRepository->findBy(
                array('user_id' => $user_id)
            );
        } else if (empty($user_id)) {
            $projects = $projectRepository->findBy(
                array('project_name' => $name)
            );
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

        $form = $this->createForm(ProjetType::class, $projet, array('method' => 'PUT'));

        $form->submit($request->request->all(), false);

        // $form est toujours null mais les données sont bien maj dans $projet, une donnée invalide n'étant pas ajoutée
        $projet->setUpdatedAt(new \DateTime());
        $this->getDoctrine()->getManager()->flush();

        return $projet;
    }

    /**
     * @Rest\Delete("/api/project/{idProject}")
     * @Rest\View(
     *     statusCode = 200
     * )
     */
    public function deleteProjectAction(Request $request) {
        $projet = $this->getDoctrine()->getRepository('APIProjectBundle:Projet')
            ->find($request->get('idProject'));

        if (empty($projet)) {
            return new JsonResponse(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        $files = $this->getDoctrine()->getRepository('APIProjectBundle:Fichier')
            ->findBy(array('project'=>$request->get('idProject')));

        $em = $this->getDoctrine()->getManager();

        // TODO: transaction pour vérifier la suppression dans le systeme de fichiers
        if (!empty($files)) {
            foreach ($files as $file) {
                $em->remove($file);
            }
        }
        $em->remove($projet);
        $em->flush();
    }
}