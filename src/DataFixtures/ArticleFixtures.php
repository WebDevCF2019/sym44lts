<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
// pour faire communiquer les fichiers de fixtures entre eux
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use App\Utils\Slug;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        // chargement de Faker
        $fake = Factory::create("fr_BE");

        // on stocke dans la session le nombre d'articles qu'on veut insérer
        $_SESSION['nb_article']=400;

        // Autant d'articles que l'on souhaite
        for($i=0;$i<$_SESSION['nb_article'];$i++) {

            // création d'une instance de Entity/User
            $article = new Article();

            // on crée autant de références que d'articles que l'on souhaite créer, il seront utilisés dans CategFixtures.php
            $this->addReference("mes_articles_".$i,$article);

            // création des variables via Faker
            // phrase de 1 à 8 mots
            $titre = $fake->sentence(8, true);
            // slug
            $slug = Slug::slugletexte($titre);
            $text = $fake->text(500);
            $date = $fake->dateTime();

            // on prend un utilisateur au hasard entre 0 et le nombre stocké dans $_SESSION['nb_users'] => ici 150
            $nbuser = random_int(0,$_SESSION['nb_users']-1);

            // on récupère la référence de l'utilisateur
            $iduser = $this->getReference("mes_users_$nbuser");

            // utilisation des setters pour remplir l'instance
            $article->setTitre($titre)
                ->setSlug($slug)
                ->setTexte($text)
                ->setThedate($date)
                ->setUserIduser($iduser);

            // on sauvegarde l'article dans doctrine
            $manager->persist($article);
        }
        // doctrine enregistre les articles dans la table article
        $manager->flush();
    }

    /**
     * On met ici les classes de fixtures qui doivent être chargées avant celle-ci (un article sans auteur nous enverra une faute sql)
     */
    public function getDependencies()
    {
        // liste des classes nécessairement exécutées avant la classe actuel
        return array(
            UserFixtures::class,
        );
    }
}
