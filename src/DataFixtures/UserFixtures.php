<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserDetails;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Random\RandomException;

class UserFixtures extends Fixture
{
    /**
     * @throws RandomException
     */
    public function load(ObjectManager $manager): void
    {
        for($i = 1; $i <= 10; $i++){

            $user = new User();
            $user->setEmail("user{$i}@gmail.com");
            $user->setPassword("user{$i}password");
            if ($i % 2 == 0) {
                $user->setRole('admin');
            } else {
                $user->setRole('normal');
            }
            $user->setToken(bin2hex(string: random_bytes(16)));

            $details = new UserDetails();
            $details->setName("user{$i}");
            $details->setAge("{$i}");
            $details->setIsStudent(false);
            $details->setUserId($user);

            $manager->persist($user);
            $manager->persist($details);
            $this->addReference('user_' . $i, $user);

        }
        $manager->flush();
    }
}
