<?php

namespace App\DataFixtures;

use App\Entity\Festival;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FestivalFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i=0;$i<50;$i++){
            $festival = new Festival();
            $festival->setName("Festival{$i}");
            $festival->setPrice(mt_rand(10,100));
            $festival->setLocation("Romania");
            $festival->setStartDate(new \DateTime());
            $festival->setEndDate(new \DateTime());
            $manager->persist($festival);
            $this->addReference('festival_' . $i, $festival);
        }

        $manager->flush();
    }
}
