<?php

namespace APIProjectBundle\DataFixtures\ORM;

use APIProjectBundle\Entity\Fichier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class FichierFixtures extends Fixture {

    public function load(ObjectManager $manager) {

        $fichier1 = new Fichier();

        $fichier1->setName("fichier1");
        $fichier1->setPath("/");
        $fichier1->setCreatedAt(date_create_from_format(DATE_ISO8601, strtotime('2018-10-17 15:01:00')));
        $fichier1->setUpdatedAt(date_create_from_format(DATE_ISO8601, strtotime('2018-10-17 15:01:00')));
        $manager->persist($fichier1);

        $fichier2 = new Fichier();

        $fichier2->setName("fichier2");
        $fichier2->setPath("/");
        $fichier2->setCreatedAt(date_create_from_format(DATE_ISO8601, strtotime('2018-10-17 15:01:00')));
        $fichier2->setUpdatedAt(date_create_from_format(DATE_ISO8601, strtotime('2018-10-17 15:01:00')));
        $manager->persist($fichier2);

        $fichier3 = new Fichier();

        $fichier3->setName("fichier3");
        $fichier3->setPath("/");
        $fichier3->setCreatedAt(date_create_from_format(DATE_ISO8601, strtotime('2018-10-17 15:01:00')));
        $fichier3->setUpdatedAt(date_create_from_format(DATE_ISO8601, strtotime('2018-10-17 15:01:00')));
        $manager->persist($fichier3);


        $manager->flush();

    }

}