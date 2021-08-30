<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = [
            1 => [
                'name' => "profil d'emprunteur"
            ],
            2 => [
                'name' => "profil d'un cheval"
            ]
        ];

        foreach ($categories as $key => $value) {
            $categorie = new Category();
            $categorie->setName($value['name']);
            $manager->persist($categorie);

            // reference
            $this->addReference('category_' . $key, $categorie);
        }

        $manager->flush();
    }
}
