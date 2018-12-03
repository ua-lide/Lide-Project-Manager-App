<?php

namespace APIProjectBundle\Controller;


use APIProjectBundle\Entity\Fichier;
use Symfony\Component\Filesystem\Filesystem;

class FichierService {

    private $filesystem;
    private $filesystemPath;

    function __construct($filesystemPath) {
        $this->filesystem = new Filesystem();
        $this->filesystemPath = $filesystemPath;
        if (!$this->filesystem->exists($this->filesystemPath)) {
            $this->filesystem->mkdir($this->filesystemPath);
        }
    }

//    /**
//     * @param Projet $projet
//     */
//    public function createProjectFileSystem($projet) {
//        $projectPath = $this->filesystemPath. '/'. $projet->getUserId().'/'.$projet->getId();
//        $this->filesystem->mkdir($projectPath);
//    }

    /**
     * @param Fichier $fichier
     * @param $userId
     * @param $projetId
     *
     * Supprime le fichier $fichier du systeme de fichier
     */
    public function  deleteFile($fichier, $userId, $projetId) {
        $filePath = $this->filesystemPath. '/'. $userId. '/'. $projetId. '/src/'. $fichier->getPath(). '/'. $fichier->getFileName();
        $this->filesystem->remove($filePath);
    }

    /**
     * @param Fichier $fichier
     * @param $userId
     * @param $projetId
     *
     * @return String :le contenu du fichier $fichier
     */
    public function getFileContent($fichier, $userId, $projetId) {
        $filePath = $this->filesystemPath. '/'. $userId. '/'. $projetId. '/src/'. $fichier->getPath(). '/'. $fichier->getFileName();
        if ($this->filesystem->exists($filePath)) {
            $content = file_get_contents($filePath);
        } else {
            $content = "No content";
        }
        return $content;
    }

}