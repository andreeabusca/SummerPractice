<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArtistFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i=0;$i<30;$i++){
            $artist = new Artist();
            $artist->setName("Artist Name{$i}");
            $artist->setMusicalGenre("Musical Genre{$i}");
            $manager->persist($artist);
            $this->addReference('Artist Name' . $i, $artist);
        }
        $manager->flush();
    }
}
