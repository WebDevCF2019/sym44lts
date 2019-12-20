<?php

namespace App\Controller;


use App\Utils\TraiteTexte;
// Connecion PDO
use Doctrine\DBAL\Driver\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
// nécessaire pour la requête du menu
use App\Entity\Categ;
// nécessaire pour les articles
use App\Entity\Article;
// nécessaire pour les users
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

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




    // pour éviter d'avoir des centaines de requêtes, on va faire nous même nos jointures (rapidité)
    private function recupTous( $idcateg = 74)
    {

        dump($this->getDoctrine()->getConnections());

        $select = $this->getDoctrine()->getConnection('default')->prepare("SELECT
    a.*,
    GROUP_CONCAT(c2.titre SEPARATOR '|||') AS categtitre,
    GROUP_CONCAT(c2.slug SEPARATOR '|||') AS categslug,
    u.thename,u.thelogin
FROM
    categ c
INNER JOIN categ_has_article cha ON
    cha.categ_idcateg = c.idcateg
INNER JOIN article a ON
    a.idarticle = cha.article_idarticle
INNER JOIN categ_has_article cha2 ON
    a.idarticle = cha2.article_idarticle
INNER JOIN categ c2 ON
    c2.idcateg = cha2.categ_idcateg
INNER JOIN user u  ON
	a.user_iduser = u.iduser
WHERE
    c.idcateg = :idcateg
GROUP BY a.idarticle
ORDER BY a.thedate DESC;");
        $select->bindValue("idcateg",$idcateg);
        $select->execute();
        return $select->fetchAll();

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
        $idcateg = $recupCateg->getIdcateg();

        /* METHODE SIMPLE mais crée un nombre trop élevé de requêtes
        // grâce à la recupération de la categ, on prend tous les articles contenu dans celles-ci... grâce aux id liées
        $recupArticles = $recupCateg->getArticleIdarticle();
        */

        // Appel de notre méthode
        $recupArticles = $this->recupTous($idcateg);

        //dump($recupArticles);


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
            ->findBy(["userIduser"=>$iduser],["thedate"=>"DESC"]);

        // on va faire une boucle tant qu'on a des articles pour raccourcir le texte tant que le plugin composer require twig/extensions (pas encore compatibler Twig 3)
        foreach ($article as $valeur){
            // on récupère le texte
            $txt = $valeur->getTexte();
            // on remet le texte raccourci par notre service TraiteTexte
            $valeur->setTexte(TraiteTexte::Raccourci($txt,250));
        }

        // appel de la vue
        return $this->render("home/user.html.twig",
            [
                "suitemenu"=>$this->menuHaut(),
                "article"=>$article,
                "utilisateur"=>$user,
            ]);
    }



}
