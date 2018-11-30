<?php

namespace APIProjectBundle\Controller;

use APIProjectBundle\Entity\Projet;
use Symfony\Component\Filesystem\Filesystem;

class ProjetService {

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
     * @param Projet $projet
     */
    public function createProjectFileSystem($projet) {
        $projectPath = $this->filesystemPath. '/'. $projet->getUserId().'/'.$projet->getId();
        $this->filesystem->mkdir($projectPath);
    }
}