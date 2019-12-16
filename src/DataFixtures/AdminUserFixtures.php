<?php

namespace App\DataFixtures;

use App\Entity\AdminUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
// on veut que Symfony encode les mots de passes
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserFixtures extends Fixture
{
    // création d'un attribut privé
    private $pwdEncoder;

    // lorsqu'on instancie la Fixture on charge l'encoder de mots de passe
    public function __construct(UserPasswordEncoderInterface $encodeur)
    {
        // on met cet encodeur dans notre attribut privé
        $this->pwdEncoder = $encodeur;
    }

    public function load(ObjectManager $manager)
    {
        // création de l'Admin
        $adminUser = new AdminUser();

        // on remplit les setters
        $adminUser
            ->setThename("Michaël")
            ->setUsername("Admin")
            ->setThemail("michaeljpit@gmail.com")
            ->setRoles(['ROLE_USER','ROLE_ADMIN'])
            ->setPassword($this->pwdEncoder->encodePassword($adminUser,"Admin"));

        $manager->persist($adminUser);

        // création d'un utilisateur
        $adminUser = new AdminUser();

        // on remplit les setters
        $adminUser
            ->setThename("Pierre")
            ->setUsername("Collaborateur")
            ->setThemail("grandpierret@gmail.com")
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->pwdEncoder->encodePassword($adminUser,"Collaborateur"));

        $manager->persist($adminUser);

        $manager->flush();
    }
}
