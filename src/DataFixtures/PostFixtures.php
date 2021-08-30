<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Post;
use App\Entity\Photo;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        // create a French faker

        for ($nbPost = 1; $nbPost <= 30; $nbPost++) {

            $post = new Post;

            if ($nbPost < 15) {

                // reference
                $user = $this->getReference('user_proprio_' . $faker->numberBetween(2, 11));
                // user
                $post->setUser($user);

                // reference
                $categorie = $this->getReference('category_' . 2);
                // category
                $post->setCategory($categorie);

                // reference
                // equid
            } else {

                // reference
                $user = $this->getReference('user_emprunt_' . $faker->numberBetween(12, 30));
                // user
                $post->setUser($user);

                // reference
                $categorie = $this->getReference('category_' . 1);
                // category
                $post->setCategory($categorie);
            }

            // text
            $post->setText($faker->realText(35));

            // price
            $post->setPrice($faker->randomFloat(2, 10, 150));

            // pictures
            for ($image = 1; $image < 3; $image++) {
                $img = $faker->image('public/uploads');
                // $imgName = str_replace('public/uploads\\', '', $img);
                $imgName = basename($img);
                $imgPost = new Photo();
                $imgPost->setName($imgName);
                $post->addPhoto($imgPost);
            }
            $manager->persist($post);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoriesFixtures::class,
            UserFixtures::class,
            EquidFixtures::class
        ];
    }
}
