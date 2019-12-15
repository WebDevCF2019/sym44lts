<?php

namespace App\DataFixtures;

use App\Entity\AdminUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        $adminUser = new AdminUser();
        $login = "Admin";
        $mdp = "Admin";

        $adminUser
            ->setUsername($login)
            ->setRoles(['ROLE_USER','ROLE_ADMIN'])
            ->setPassword($this->passwordEncoder->encodePassword($adminUser,$mdp));

        $manager->persist($adminUser);

        $adminUser = new AdminUser();
        $login = "Lulu";
        $mdp = "Lulu";

        $adminUser
            ->setUsername($login)
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->passwordEncoder->encodePassword($adminUser,$mdp));

        $manager->persist($adminUser);

        $manager->flush();
    }
}
