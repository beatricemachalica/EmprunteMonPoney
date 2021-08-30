<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        // create a French faker

        for ($nbUser = 1; $nbUser <= 25; $nbUser++) {

            $user = new User;

            // pseudo
            $user->setPseudo($faker->lastName);

            // email
            $user->setEmail($faker->email);

            // roles
            if ($nbUser === 1) {
                // make one admin
                $user->setRoles(['ROLE_ADMIN']);
            }
            if ($nbUser < 12) {
                // make 9 owner (ROLE_PROPRIO)
                $user->setRoles(['ROLE_PROPRIO']);

                // reference
                $this->addReference('user_proprio_' . $nbUser, $user);
            } else {
                // make 10 borrower (ROLE_EMPRUNT)
                $user->setRoles(['ROLE_EMPRUNT']);

                // reference
                $this->addReference('user_emprunt_' . $nbUser, $user);
            }

            // password
            $user->setPassword($this->hasher->hashpassword($user, "test1234"));

            // email isVerified
            $user->setIsVerified($faker->numberBetween(0, 1));

            // departement
            $user->setDepartement($faker->state);

            // cp
            $user->setDepartement($faker->postcode);

            // city
            $user->setDepartement($faker->city);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
