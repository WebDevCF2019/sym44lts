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
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        // Doctrine récupère tous les champs de la table Categ
        $recupMenu = $this->getDoctrine()->getRepository(Categ::class)->findAll();

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
            "suitemenu"=>$recupMenu,
            "articles"=>$recupArticles,
        ]);
    }
    /**
    * @Route("/categ/{slug}", name="categ")
    */
    public function detailCateg($slug){
        return new Response($slug);
    }

    /**
     * @Route("/article/{slug}", name="article")
     */
    public function detailArticle($slug){
        return new Response($slug);
    }

}
