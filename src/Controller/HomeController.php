<?php

namespace App\Controller;


use App\Utils\TraiteTexte;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// nécessaire pour la requête du menu
use App\Entity\Categ;
// nécessaire pour les articles
use App\Entity\Article;
// nécessaire pour les users
use App\Entity\User;

class HomeController extends AbstractController
{

    // charge le menu
    public function menuHaut()
    {
            // Doctrine récupère tous les champs de la table Categ
            return  $this->getDoctrine()->getRepository(Categ::class)->findAll();

    }

    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {

        // Doctrine récupère les 10 derniers articles
        $recupArticles = $this->getDoctrine()->getRepository(Article::class)->findBy([],["thedate"=>"DESC"],10);

        // on va faire une boucle tant qu'on a des articles pour raccourcir le texte tant que le plugin composer require twig/extensions (pas encore compatibler Twig 3)
        foreach ($recupArticles as $valeur){
            // on récupère le texte
            $txt = $valeur->getTexte();
            // on remet le texte raccourci par notre service TraiteTexte
            $valeur->setTexte(TraiteTexte::Raccourci($txt,250));
        }

        // chargement du template
        return $this->render('home/index.html.twig', [
            // envoi du résultat de la requête à twig sous le nom "suitemenu"
            "suitemenu"=>$this->menuHaut(),
            "articles"=>$recupArticles,
        ]);
    }
    /**
    * @Route("/categ/{slug}", name="categ")
    */
    public function detailCateg($slug){

        // Doctrine récupère tous les champs de Categ en utilisant le slug (champs unique) - 1 ou 0 résultat
        $recupCateg =  $this->
                getDoctrine()->
                getRepository(Categ::class)->
                findOneBy(['slug'=>$slug]);

        // grâce à la recupération de la categ, on prend tous les articles contenu dans celles-ci... grâce aux id liées
        $recupArticles = $recupCateg->getArticleIdarticle();


        // on va faire une boucle tant qu'on a des articles pour raccourcir le texte tant que le plugin composer require twig/extensions (pas encore compatibler Twig 3)
        foreach ($recupArticles as $valeur){
            // on récupère le texte
            $txt = $valeur->getTexte();
            // on remet le texte raccourci par notre service TraiteTexte
            $valeur->setTexte(TraiteTexte::Raccourci($txt,250));
        }


        // chargement du template
        return $this->render('home/categ.html.twig', [
            // envoi du résultat de la requête à twig sous le nom "suitemenu"
            "suitemenu"=>$this->menuHaut(),
            "categ"=>$recupCateg,
            "articles"=>$recupArticles,
        ]);


    }

    /**
     * @Route("/article/{slug}", name="article")
     */
    public function detailArticle($slug){

        $article = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->findOneBy(['slug'=>$slug]);

        return $this->render("home/article.html.twig",
            [
                "suitemenu"=>$this->menuHaut(),
                "article"=>$article,
            ]);
    }

    /**
     * @Route("/user/{thelogin}", name="user")
     */
    public function detailUser($thelogin){
        // récupération de l'utilisateur
        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(["thelogin"=>$thelogin]);

        // récupération de l'id pour la requête suivante
        $iduser = $user->getIduser();

        // récupération de ses articles
        $article = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->findBy(["user_iduser"=>$iduser],["thedate"=>"DESC"]);

        // appel de la vue
        return $this->render("home/user.html.twig",
            [
                "suitemenu"=>$this->menuHaut(),
                "article"=>$article,
                "utilisateur"=>$user,
            ]);
    }

}
