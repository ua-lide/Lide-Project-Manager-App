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

    /**
     * @param Fichier $fichier
     * @param $userId
     * @param $projetId
     *
     * Supprime le fichier $fichier du systeme de fichier
     */
    public function  deleteFile($fichier, $userId, $projetId) {
        $filePath = $this->filesystemPath. '/'. $userId. '/'. $projetId. '/src/'. $fichier->getPath(). '/'. $fichier->getName();
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
        $filePath = $this->filesystemPath. '/'. $userId. '/'. $projetId. '/src/'. $fichier->getPath(). '/'. $fichier->getName();
        if ($this->filesystem->exists($filePath)) {
            $content = file_get_contents($filePath);
        } else {
            $content = "";
        }
        return $content;
    }

    /**
     * @param Fichier $fichier
     * @param $userId
     * @param $projetId
     * @param String $content
     * @param String $oldPath
     *
     * Modifie le contenu du fichier $fichier
     */
    public function setFile($fichier, $userId, $projetId, $content, $path) {
        $oldPath = $this->filesystemPath. '/'. $userId. '/'. $projetId. '/src'. $path;
        $filePath = $this->filesystemPath. '/'. $userId. '/'. $projetId. '/src'. $fichier->getPath(). '/';

        // la méthode rename ne fonctionne pas si le répertoire n'existe pas
        if (!$this->filesystem->exists($filePath)) {
            $this->filesystem->mkdir($filePath);
        }

        if ($oldPath !== $filePath.$fichier->getName()) {
            $this->filesystem->rename($oldPath, $filePath . $fichier->getName());
        }

        if (!empty($content)) {
            $this->filesystem->dumpFile($filePath.$fichier->getName(), $content);
        }
    }

    /**
     * @param Fichier $fichier
     * @param $userId
     * @param $projetId
     * @param String $content
     *
     * Ajoute le fichier $fichier au projet d'id $projetId
     */
    public function addFile($fichier, $userId, $projetId, $content) {
        $filePath = $this->filesystemPath. '/'. $userId. '/'. $projetId. '/src'. $fichier->getPath(). '/';

        // la méthode touch ne fonctionne pas si le répertoire n'existe pas
        if (!$this->filesystem->exists($filePath)) {
            $this->filesystem->mkdir($filePath);
        }

        $this->filesystem->touch($filePath.$fichier->getName());

        if (!empty($content)) {
            $this->filesystem->dumpFile($filePath.$fichier->getName(), $content);
        }
    }

}