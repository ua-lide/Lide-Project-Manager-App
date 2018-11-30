<?php

namespace APIProjectBundle\Controller;


use APIProjectBundle\Entity\Projet;
use APIProjectBundle\Form\ProjetType;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
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

        $conn = $this->getDoctrine()->getConnection();
        $conn->beginTransaction();
        $conn->setAutoCommit(false);

        try {

            $projet = new Projet();

            $form = $this->createForm(ProjetType::class, $projet, array('method' => 'POST'));

            $form->submit($request->request->all(), false);

            $projet->setUserId($this->getUser()->getId());

            // le nom d'un projet est unique par utilisateur
            if (!empty($this->getDoctrine()->getRepository('APIProjectBundle:Projet')
                ->findBy(array('project_name' => $projet->getName(),
                    'user_id' => $projet->getUserId())))) {
                throw new \Exception("Le projet existe deja");
            }

            $em = $this->getDoctrine()->getManager();

            $em->persist($projet);

            $filesystem = $this->container->getParameter('root_users_filesystem');
            $projetService = new ProjetService($filesystem);

            $em->flush();

            $projetService->createProjectFileSystem($projet);

            $conn->commit();

            return $projet;

        } catch (\Exception $exception) {
            $conn->rollback();
            return new JsonResponse(['message' => 'Erreur lors de la creation du projet', $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

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
                array('project_name' => $name, 'user_id' => $user_id)
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

        $conn = $this->getDoctrine()->getConnection();
        $conn->beginTransaction();
        $conn->setAutoCommit(false);

        try {
            $projet = $this->getDoctrine()->getRepository('APIProjectBundle:Projet')
                ->find($request->get('idProject'));

            if (empty($projet)) {
                return new JsonResponse(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
            }

            $files = $this->getDoctrine()->getRepository('APIProjectBundle:Fichier')
                ->findBy(array('project' => $request->get('idProject')));

            $em = $this->getDoctrine()->getManager();

            if (!empty($files)) {
                foreach ($files as $file) {
                    $em->remove($file);
                }
            }

            $filesystem = $this->container->getParameter('root_users_filesystem');
            $projetService = new ProjetService($filesystem);
            $projetService->deleteProjectFileSystem($projet);

            $em->remove($projet);
            $em->flush();

            $conn->commit();

        } catch (\Exception $exception) {
            $conn->rollback();
            return new JsonResponse(['message' => 'Erreur lors de la suppression du projet', $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}