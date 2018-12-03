<?php

namespace APIProjectBundle\Controller;


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
            $filesystem = new Filesystem();
            $path = $this->container->getParameter('root_users_filesystem')."/".
                $this->getUser()->getId()."/".
                $request->get('idProject')."/src/".
            $file->getPath().$file->getFileName();
            if ($filesystem->exists($path)) {
                $content = file_get_contents($path);
            } else {
                $content = "No content";
            }
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

        if (empty($files)) {
            return new JsonResponse(['message' => 'Files not found'], Response::HTTP_NOT_FOUND);
        }

        return $files;
    }

    /**
     * @Rest\Put("/api/project/{idProject}/files/{idFile}")
     * @Rest\View(
     *     statusCode = 200
     * )
     */
    public function setFileAction(Request $request) {
        $file = $this->getDoctrine()->getRepository('APIProjectBundle:Fichier')
            ->find($request->get('idFile'));

        if (empty($file)) {
            return new JsonResponse(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm($file);
        $form->submit($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($file);
            $this->getDoctrine()->getManager()->flush();

            return $file;
        } else {
            return new JsonResponse(['message' => 'Données invalides'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @Rest\Delete("/api/project/{idProject}/files{idFile}")
     * @Rest\View(
     *     statusCode = 200
     * )
     */
    public function deleteFileAction(Request $request) {
        $file = $this->getDoctrine()->getRepository('APIProjectBundle:Fichier')
            ->find($request->get('idFile'));

        if (empty($file)) {
            return new JsonResponse(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($file);
        $em->flush();
    }

}