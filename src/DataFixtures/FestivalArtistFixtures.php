<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use App\Entity\Festival;
use App\Entity\FestivalArtist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

 class FestivalArtistFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $festivalArtist = new FestivalArtist();

            // getReference uses ONE string param here
            $artist = $this->getReference( 'Artist Name' . rand(1, 29),Artist::class);
            $festival = $this->getReference( 'festival_' . rand(0, 49),Festival::class);
            $festivalArtist->setArtist($artist);
            $festivalArtist->setFestival($festival);
            $festivalArtist->setDate(new \DateTime());
            $festivalArtist->setTimeSlot(new \DateTime('00:00'));
            $festivalArtist->setStage("Stage{$i}");

            $manager->persist($festivalArtist);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ArtistFixtures::class,
            FestivalFixtures::class,
        ];
    }
}
