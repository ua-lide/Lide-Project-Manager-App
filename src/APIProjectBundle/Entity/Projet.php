<?php

namespace APIProjectBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Projet
 *
 * @ORM\Table(name="projet")
 * @ORM\Entity(repositoryClass="APIProjectBundle\Repository\ProjetRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Projet
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="project_name", type="string")
     */
    private $project_name;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user_id;

    /**
     * @var int
     *
     * @ORM\Column(name="environnement_id", type="integer")
     */
    private $environnement_id;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_public", type="boolean", options={"default": false})
     */
    private $is_public;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_archived", type="boolean", options={"default": false})
     */
    private $is_archived;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updated_at;

//    /**
//     * @var Collection|Fichier[]
//     *
//     * @ORM\OneToMany(targetEntity="Fichier", mappedBy="project")
//     */
//    private $fichiers;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getProjectName()
    {
        return $this->project_name;
    }

    /**
     * @param string $name
     */
    public function setProjectName($name)
    {
        $this->project_name = $name;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return int
     */
    public function getEnvironnementId()
    {
        return $this->environnement_id;
    }

    /**
     * @param int $environnement_id
     */
    public function setEnvironnementId($environnement_id)
    {
        $this->environnement_id = $environnement_id;
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->is_public;
    }

    /**
     * @param bool $is_public
     */
    public function setIsPublic($is_public)
    {
        $this->is_public = $is_public;
    }

    /**
     * @return bool
     */
    public function isArchived()
    {
        return $this->is_archived;
    }

    /**
     * @param bool $is_archived
     */
    public function setIsArchived($is_archived)
    {
        $this->is_archived = $is_archived;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        if (!$this->created_at) {
            $this->created_at = new \DateTime();
        }
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTime $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

//    /**
//     * @return Collection|Fichier[]
//     */
//    public function getFichiers() {
//        return $this->fichiers;
//    }
//
//    /**
//     * @param Fichier $fichier
//     */
//    public function addFichier(Fichier $fichier) {
//        if (!$this->fichiers->contains($fichier)) {
//            $this->fichiers[] = $fichier;
//            $fichier->setProject($this);
//        }
//    }
//
//    /**
//     * @param Fichier $fichier
//     */
//    public function removeFichier(Fichier $fichier) {
//        if ($this->fichiers->contains($fichier)) {
//            $this->fichiers->removeElement($fichier);
//            if ($fichier->getProject() === $this) {
//                $fichier->setProject(null);
//            }
//        }
//    }

}