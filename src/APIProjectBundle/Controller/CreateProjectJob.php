<?php

namespace APIProjectBundle\Controller;


class CreateProjectJob
{

    private $name;
    private $environnement_id;
    private $is_public;

    /**
     * CreateProjectJob constructor.
     * @param $name
     * @param $environnement_id
     * @param $is_public
     */
    public function __construct($name, $environnement_id, $is_public)
    {
        $this->name = $name;
        $this->environnement_id = $environnement_id;
        if ($this->is_public == null) {
            $this->is_public = false;
        }
        else {
            $this->is_public = $is_public;
        }
    }


    public function handle() {

    }

}