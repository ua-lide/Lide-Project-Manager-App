<?php

namespace APIProjectBundle\DataFixtures\ORM;

use APIProjectBundle\Entity\Projet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProjetFixtures extends Fixture {

    public function load(ObjectManager $manager) {

        $projet1 = new Projet();

        $projet1->setName("projet1");
        $projet1->setUserId(1);
        $projet1->setEnvironnementId(1);
        $projet1->setIsPublic(true);
        $projet1->setIsArchived(false);
        $projet1->setCreatedAt(date_create_from_format(DATE_ISO8601, strtotime('2018-10-17 15:01:00')));
        $projet1->setUpdatedAt(date_create_from_format(DATE_ISO8601, strtotime('2018-10-17 15:01:00')));
        $manager->persist($projet1);

        $projet2 = new Projet();

        $projet2->setName("projet2");
        $projet2->setUserId(2);
        $projet2->setEnvironnementId(2);
        $projet2->setIsPublic(true);
        $projet2->setIsArchived(true);
        $projet2->setCreatedAt(date_create_from_format(DATE_ISO8601, strtotime('2018-10-15 15:01:00')));
        $projet2->setUpdatedAt(date_create_from_format(DATE_ISO8601, strtotime('2018-10-17 15:21:00')));
        $manager->persist($projet2);


        $manager->flush();

    }

}