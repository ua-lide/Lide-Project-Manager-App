<?php

namespace APIProjectBundle\DataFixtures\ORM;

use APIProjectBundle\Entity\Projet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProjetFixtures extends Fixture {

    public function load(ObjectManager $manager) {

        $projet1 = new Projet();

        $projet1->setProjectName("projet1");
        $projet1->setUserId(1);
        $projet1->setEnvironnementId(1);
        $projet1->setIsPublic(true);
        $projet1->setIsArchived(false);

        $this->addReference('projet1', $projet1);

        $manager->persist($projet1);

        $projet2 = new Projet();

        $projet2->setProjectName("projet2");
        $projet2->setUserId(2);
        $projet2->setEnvironnementId(2);
        $projet2->setIsPublic(true);
        $projet2->setIsArchived(true);

        $this->addReference('projet2', $projet2);

        $manager->persist($projet2);


        $manager->flush();

    }

}