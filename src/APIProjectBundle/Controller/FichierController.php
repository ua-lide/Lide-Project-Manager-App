<?php

namespace APIProjectBundle\Controller;


use APIProjectBundle\Entity\Fichier;
use APIProjectBundle\Form\FichierType;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FichierController extends Controller {

    /**
     * @Rest\Get("/api/project/{idProject}/files/{idFile}")
     * @Rest\View()
     *
     * @Rest\QueryParam(name="withContent", nullable=true)
     */
    public function getFileAction(Request $request, ParamFetcherInterface $paramFetcher) {
        $file = $this->getDoctrine()->getRepository('APIProjectBundle:Fichier')
            ->find($request->get('idFile'));

        if (empty($file)) {
            return new JsonResponse(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        }

        if ($file->getProject()->getId()!=$request->get('idProject')) {
            return new JsonResponse(['message'=>'Le fichier n\'appartient pas au projet specifie'], Response::HTTP_NOT_FOUND);
        }

        $withContent = $paramFetcher->get('withContent');
        if (($withContent == "0") || ($withContent == "true")) {
            $filesystem = $this->container->getParameter('root_users_filesystem');
            $fichierService = new FichierService($filesystem);
            $content = $fichierService->getFileContent($file, $this->getUser()->getId(), $request->get('idProject'));

            return array('data'=>$file, 'content'=>$content);
        } else {
            return $file;
        }
    }

    /**
     * @Rest\Get("/api/project/{idProject}/files")
     * @Rest\View()
     */
    public function getFilesAction(Request $request) {
        $files = $this->getDoctrine()->getRepository('APIProjectBundle:Fichier')
            ->findBy(array('project'=>$request->get('idProject')));

        return $files;
    }

    /**
     * @Rest\Put("/api/project/{idProject}/files/{idFile}")
     * @Rest\View(
     *     statusCode = 200
     * )
     */
    public function setFileAction(Request $request) {

        $conn = $this->getDoctrine()->getConnection();
        $conn->beginTransaction();
        $conn->setAutoCommit(false);

        try {

            $file = $this->getDoctrine()->getRepository('APIProjectBundle:Fichier')
                ->find($request->get('idFile'));

            if (empty($file)) {
                return new JsonResponse(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
            }

            $oldPath = $file->getPath() . '/' . $file->getName();

            $form = $this->createForm(FichierType::class, $file, array('method' => 'PUT'));
            $form->submit($request->request->all(), false);

            $file->setUpdatedAt(new \DateTime());

            if (!empty($request->get('content'))) {
                $content = $request->get('content');
            } else {
                $content = null;
            }

            $filesystem = $this->container->getParameter('root_users_filesystem');
            $fichierService = new FichierService($filesystem);
            $fichierService->setFile($file, $this->getUser()->getId(), $request->get('idProject'), $content, $oldPath);

            // $form est toujours null mais les données sont bien maj dans $file, une donnée invalide n'étant pas ajoutée
            // donc $form->isValid() renvoie toujours faux
            $this->getDoctrine()->getManager()->flush();

            $conn->commit();

            return $file;

        } catch (\Exception $exception) {
            $conn->rollback();
            return new JsonResponse(['message' => 'Erreur lors de la modification du fichier', 'erreur' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/api/project/{idProject}/files")
     * @Rest\View(
     *     statusCode = 200
     * )
     * @param Request $request
     */
    public function addFileAction(Request $request) {

        $conn = $this->getDoctrine()->getConnection();
        $conn->beginTransaction();
        $conn->setAutoCommit(false);

        try {

            $file = new Fichier();

            $form = $this->createForm(FichierType::class, $file, array('method' => 'POST'));
            $form->submit($request->request->all(), false);

            $project = $this->getDoctrine()->getRepository('APIProjectBundle:Projet')
                ->find($request->get('idProject'));
            if (!empty($project)) {
                $file->setProject($project);
            }

            //tous les champs sont requis
            if (empty($request->get('content'))||empty($request->get('name'))||empty($request->get('path'))) {
                $conn->rollback();
                return new JsonResponse(['message' => 'Le formulaire n\'est pas valide'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // le couple (name, path) est unique dans un projet
            if (!empty($this->getDoctrine()->getRepository('APIProjectBundle:Fichier')
                ->findBy(array('name' => $file->getName(),
                    'path' => $file->getPath(),
                    'project' => $file->getProject())))) {
                $conn->rollback();
                return new JsonResponse(['message' => 'Ce fichier existe deja'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $em = $this->getDoctrine()->getManager();

            $em->persist($file);

            if (!empty($request->get('content'))) {
                $content = $request->get('content');
            } else {
                $content = null;
            }

            $filesystem = $this->container->getParameter('root_users_filesystem');
            $fichierService = new FichierService($filesystem);

            $em->flush();

            $fichierService->addFile($file, $this->getUser()->getId(), $request->get('idProject'), $content);

            $conn->commit();

            return $file;

        } catch (\Exception $exception) {
            $conn->rollback();
            return new JsonResponse(['message' => 'Erreur lors de l\'ajout du fichier au projet', 'erreur' => $exception->getMessage()]);
        }
    }

    /**
     * @Rest\Delete("/api/project/{idProject}/files/{idFile}")
     * @Rest\View(
     *     statusCode = 200
     * )
     */
    public function deleteFileAction(Request $request) {

        $conn = $this->getDoctrine()->getConnection();
        $conn->beginTransaction();
        $conn->setAutoCommit(false);

        try {

            $file = $this->getDoctrine()->getRepository('APIProjectBundle:Fichier')
                ->find($request->get('idFile'));

            if (empty($file)) {
                return new JsonResponse(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
            }

            $filesystem = $this->container->getParameter('root_users_filesystem');
            $fichierService = new FichierService($filesystem);
            $fichierService->deleteFile($file, $this->getUser()->getId(), $request->get('idProject'));

            $em = $this->getDoctrine()->getManager();

            $em->remove($file);
            $em->flush();

            $conn->commit();

        } catch (\Exception $exception) {
            $conn->rollback();
            return new JsonResponse(['message' => 'Erreur lors de la suppression du fichier', 'erreur' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}