<?php

namespace App\DataFixtures;

use App\Entity\Festival;
use App\Entity\Purchase;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PurchaseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 15; $i++) {
            $purchase = new Purchase();

            // getReference uses ONE string param here
            $user = $this->getReference('user_' . rand(1, 10),User::class);
            $festival = $this->getReference( 'festival_' . rand(0, 49),Festival::class);
            $purchase->setUser($user);
            $purchase->setFestival($festival);

            $manager->persist($purchase);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            FestivalFixtures::class,
        ];
    }
}
