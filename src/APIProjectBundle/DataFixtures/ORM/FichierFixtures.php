<?php

namespace APIProjectBundle\DataFixtures\ORM;

use APIProjectBundle\Entity\Fichier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class FichierFixtures extends Fixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {

        $fichier1 = new Fichier();

        $fichier1->setFileName("fichier1");
        $fichier1->setPath("/");
        $fichier1->setProject($this->getReference('projet1'));
        $manager->persist($fichier1);

        $fichier2 = new Fichier();

        $fichier2->setFileName("fichier2");
        $fichier2->setPath("/");
        $fichier1->setProject($this->getReference('projet1'));
        $manager->persist($fichier2);

        $fichier3 = new Fichier();

        $fichier3->setFileName("fichier3");
        $fichier3->setPath("/");
        $fichier1->setProject($this->getReference('projet2'));
        $manager->persist($fichier3);


        $manager->flush();

    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies() {
        return array(
            ProjetFixtures::class,
        );
    }
}