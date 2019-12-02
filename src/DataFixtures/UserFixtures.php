<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // création d'une instance de Entity/User
        $user = new User();

        // Autant d'utilisateurs que l'on souhaite
        for($i=0;$i<50;$i++) {

            // utilisation des setters pour remplir l'instance
            $user->setThelogin("Lulu$i")
                ->setThename("Lulu Poilu$i")
                ->setThepwd("Lulu$i");

            // on sauvegarde l'utilisateur dans doctrine
            $manager->persist($user);
        }
        // doctrine enregistre l'utilisateur dans la table user
        $manager->flush();
    }
}
