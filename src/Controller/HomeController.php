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
        return new Response($slug);
    }

    /**
     * @Route("/user/{thelogin}", name="user")
     */
    public function detailUser($thelogin){
        return new Response($thelogin);
    }

}
