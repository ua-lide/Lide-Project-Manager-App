<?php

namespace APIProjectBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FichierController extends Controller {

    /**
     * @Rest\Delete("/api/project/{idProject}/files{idFile}")
     * @Rest\View(
     *     statusCode = 200
     * )
     */
    public function deleteFile(Request $request) {
        $fileRepository = $this->getDoctrine()->getRepository('APIProjectBundle:Fichier');
        $file = $fileRepository->find($request->get('idFile'));

        if (empty($file)) {
            return new JsonResponse(['message' => 'File not found'], Response::HTTP_NOT_FOUND);
        } else {
            $fileRepository->deleteFile($file);
        }
    }

}