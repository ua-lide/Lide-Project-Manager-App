<?php

namespace APIProjectBundle\Controller;


use APIProjectBundle\Entity\Projet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{

    /**
     * @Rest\Post("/api/project")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProject(Request $request) {

        $projet = new Projet();

        $formBuilder = $this->createFormBuilder(FormType::class, $projet);

        $formBuilder
            ->add('id', IntegerType::class)
            ->add('name', TextType::class)
            ->add('user_id', IntegerType::class)
            ->add('environnement_id', IntegerType::class)
            ->add('is_public', TextType::class)
            ->add('is_archived', TextType::class)
            ->add('created_at', DateType::class)
            ->add('updated_at', DateType::class)
        ;

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //traitement des données (classe CreateProjectJob)
        }

        //exemple
        return $this->render('default/index.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Rest\Get("/api/project/{idProject}/")
     */
    public function getProject() {

    }

    /**
     * @Rest\Get("/api/projects?query")
     */
    public function getProjects() {

    }

    /**
     * @Rest\Put("/api/project/{idProject}")
     */
    public function setProject() {

    }

    /**
     * @Rest\Delete("/api/project/{idProject}")
     */
    public function deleteProject() {

    }
}