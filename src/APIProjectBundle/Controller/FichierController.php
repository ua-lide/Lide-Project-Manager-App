<?php

namespace APIProjectBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FichierController extends Controller {

    /**
     * @Rest\Get("/api/project/{idProject}/files/{idFile}")
     * @Rest\View()
     */
    public function getFileAction(Request $request) {
        $file = $this->getDoctrine()->getRepository('APIProjectBundle:Fichier')
            ->find($request->get('idFile'));

        if (empty($file)) {
            return new JsonResponse(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        }

        return $file;
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
        $fileRepository = $this->getDoctrine()->getRepository('APIProjectBundle:Fichier');
        $file = $fileRepository->find($request->get('idFile'));

        if (empty($file)) {
            return new JsonResponse(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        } else {
            $fileRepository->deleteFile($file);
        }
    }

}