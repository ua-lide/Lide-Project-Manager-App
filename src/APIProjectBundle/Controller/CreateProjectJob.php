<?php

namespace APIProjectBundle\Controller;


class CreateProjectJob
{

    private $projet;

    /**
     * CreateProjectJob constructor.
     * @param $projet
     */
    public function __construct($projet)
    {
        $this->projet = $projet;
    }


    public function handle() {

    }

}