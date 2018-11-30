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
     * @ORM\Column(name="is_public", type="boolean")
     */
    private $is_public;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_archived", type="boolean")
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
        return (boolean)$this->is_public;
    }

    /**
     * @param bool $is_public
     * @ORM\PrePersist
     */
    public function setIsPublic($is_public)
    {
        if (!empty($is_public)) {
            $this->is_public = $is_public;
        } else {
            $this->is_public = false;
        }
    }

    /**
     * @return bool
     */
    public function isArchived()
    {
        return (boolean)$this->is_archived;
    }

    /**
     * @param bool $is_archived
     * @ORM\PrePersist
     */
    public function setIsArchived($is_archived)
    {
        if (!empty($is_archived)) {
            $this->is_archived = $is_archived;
        } else {
            $this->is_archived = false;
        }
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $created_at
     * @ORM\PrePersist
     */
    public function setCreatedAt($created_at)
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

}