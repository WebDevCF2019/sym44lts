# sym44lts
### Long term support (LTS) symfony 4.4.*
### Installation
Chargez l'exécutable pour windows à cette adresse: https://symfony.com/download

Installation de la version LTS de symfony 4, valable au moins 3 ans:

https://symfony.com/releases

Avec la commande:

    symfony new sym44lts --full --version=lts

### Démarrer le serveur en ligne de commandes sous windows:

    symfony server:start
    
ctrl + c permet de quitter le serveur

Si un [WARNING] vous déclare que le certificat ssl n'est pas installé, vous pouvez utiliser cette commande:

    symfony server:ca:install    
Vous pourrez alors, après redémmarage du serveur, utiliser https:
https://127.0.0.1:8000

### Vérifier que tout est à jour:

    composer update
On voit qu'on a pas de vérification de sécurité des dépendances, on va l'installer
#### security-checker
Cette bibliothèque regarde votre configuration, intérroge des bases de données pour vérifier que vos dépendances sont sécurisées:

    composer require security-check 
    
Elle est appelée à chaque composer update, ou on l'utilise comme ceci:

    php bin/console security:check    
### Utiliser Apache pour faire tourner Symfony
Si on fait un lien vers /public et que l'on souhaite rester en mode débuggage (que la toolbar reste active), on peut installer une bibliothèque pour ça:

    composer require symfony/apache-pack
  
  Elle fonctionnera en local comme sur nimporte quel serveur web. 
  
### Créons notre contrôleur général

    php bin/console make:controller
### création des templates
On utilise bootstrap 4, ici grâce aux CDN (Les fichiers sont en ligne)     

dans base.html.twig on rajoute le meta pour le responsive de bootstrap

    <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <title>{% block title %}Welcome!{% endblock %}</title>

On crée les blocks complémentaires dans bootstrap4.html.twig qui hérite de base.html.twig

/templates/home/index.twig hérite de bootstrap4.html.twig
### Chemins depuis un template
En interne, on utilise la fonction twig path() pour chercher dans Symfony le nom (ancre) du chemin voulu

pour l'accueil dans bootstrap4.html.twig:

    <a href="{{path('homepage')}}">Home</a>   
##### Astuce, pour trouver tous vos chemin depuis la console

    php bin/console debug:router
          
#### On change notre HomeController
pour tester le menu dans home/index.html.twig

    // création d'un tableau pour l'envoyer à twig
            $menu = ["Actualités"=>"/rubrique/actualites",
                    "Qui sommes-nous"=>"/rubrique/whois",
                    "Nous contacter"=>"/rubrique/contact",
                ];
            return $this->render('home/index.html.twig', [
                // envoi du tableau à twig sous le nom "suitemenu"
                "suitemenu"=>$menu,
            ]);    
#### Puis dans index.html.twig      
    {% block menuhaut %}
        {% for clef, valeur in suitemenu %}
        <li class="nav-item">
            <a class="nav-link" href="{{ valeur }}">{{ clef }}</a>
        </li>
        {% endfor %}
    {% endblock %}
    
#### on affiche sous forme de réponse http le titre

    ....
    use Symfony\Component\HttpFoundation\Response;
    ....
    /**
     * @Route("/rubrique/{titre}", name="rubriques")
     */
    public function showRubrique(string $titre){
        return new Response($titre);
    }
#### on modifie bles liens dans index.html.twig

     {% for clef, valeur in suitemenu %}
        <li class="nav-item">
            <a class="nav-link" href="{{ path('rubriques',{titre: valeur} ) }}">{{ clef }}</a>
        </li>
        {% endfor %}           
### Création de la DB
création du dossier datas dans lequel on met un fichier créé avec workbench (sym44lts.mwb)

#### Création d'un utilisateur 
dans PhpMyAdmin on crée un utilisateur nommé "sym44lts" avec comme mot de passe : "44lts", on coche :
 
 "Créer une base portant son nom et donner à cet utilisateur tous les privilèges sur cette base."
 
 #### création de .env.local
 On duplique .env sous le nom .env.local, ce fichier n'ira pas sur github, c'est généralement plus sécure, sauf quand on l'écrit manuellement dans readme.md ;-)
 
    DATABASE_URL=mysql://sym44lts:44lts@127.0.0.1:3306/sym44lts?serverVersion=5.7
  
  #### Importation de la DB vers notre dossier src/Entity 
  
    php bin/console doctrine:mapping:import "App\Entity" annotation --path=src/Entity
     
 Les fichiers sont créés dans src/Entity
    
#### Ajoutons les getters et setters
Et autres méthodes avec la commande:

    php bin/console make:entity --regenerate App
                  
#### Pour vérifier à tout moment si vos bibliothèques sont sécurisées
                      
    php bin/console security:check    
    
#### Création de fausses données
On charge les Fixtures

    composer require orm-fixtures --dev
Pour remplir nos tables avec des Fixtures, on va générer des fichiers pour le faire   

    php bin/console make:fixtures     
    
#### Dans la page de UserFixture
Insertion d'un utilisateur
    
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
    
            // utilisation des setters pour remplir l'instance
            $user->setThelogin("Lulu")
                ->setThename("Lulu Poilu")
                ->setThepwd("Lulu");
    
            // on sauvegarde l'utilisateur dans doctrine
            $manager->persist($user);
    
            // doctrine enregistre l'utilisateur dans la table user
            $manager->flush();
        }
    }
On va charger cette fixture vers la DB:

    php bin/console doctrine:fixtures:load
    
! ça vide la base de donnée     

#### Boucle pour en insérer plusieurs
La boucle for nous permet d'en insérer plusieurs, on utilise $i pour éviter le DUPLICATE CONTENT

    public function load(ObjectManager $manager)
        {
    
            // Autant d'utilisateurs que l'on souhaite
            for($i=0;$i<50;$i++) {
    
                // création d'une instance de Entity/User
                $user = new User();
    
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
Le $manager->persist reste dans la boucle.

Le $manager->flush() effectue réellement la requête (un seul insert de 50 lignes)     

#### Chargement d'une bibliothèque dédiée aux Fixtures

    composer require fzaninotto/faker
La documentation : https://packagist.org/packages/fzaninotto/faker

#### Utilisation de Faker dans notre fixture

    // chargement de Faker
            $fake = Factory::create("fr_BE");
    
            // Autant d'utilisateurs que l'on souhaite
            for($i=0;$i<50;$i++) {
    
                // création d'une instance de Entity/User
                $user = new User();
    
                // création des variables via Faker
                $login = $fake->userName;
                $name = $fake->name;
                $pwd = $fake->password(12);
    
                // utilisation des setters pour remplir l'instance
                $user->setThelogin($login)
                    ->setThename($name)
                    ->setThepwd($pwd);
    
                // on sauvegarde l'utilisateur dans doctrine
                $manager->persist($user);
            }
            // doctrine enregistre l'utilisateur dans la table user
            $manager->flush();
        }
puis :

        php bin/console doctrine:fixtures:load

 #### Partage des objets de fixtures pour les relations entre eux
 Pour charger d'abord les utilisateurs dans ArticleFixtures.php :
 
    ...
    // pour charger d'abord UserFixtures.php
    use Doctrine\Common\DataFixtures\DependentFixtureInterface;
    ...
    class ArticleFixtures extends Fixture implements DependentFixtureInterface{
    ...
    $a=0;
    // Autant d'articles que l'on souhaite
    for($i=0;$i<100;$i++) {
    
       // création d'une instance de Entity/User
       $article = new Article();
    
       // création des variables via Faker
       // phrase de 1 à 8 mots
       $titre = $fake->sentence(8, true);
       // slug
       $slug = $fake->slug;
       $text = $fake->text(500);
       $date = $fake->dateTime();
       // chargement des objets users tant qu'il y en a (0 à 49)
       if($a>49) $a=0;
       $iduser = $this->getReference("user_reference_" . $a);
       $a++;
    
       // utilisation des setters pour remplir l'instance
       $article->setTitre($titre)
           ->setSlug($slug)
           ->setTexte($text)
           ->setThedate($date)
           ->setUserIduser($iduser);
        ...
        // les utilisateurs sont chargés en premier
            public function getDependencies()
            {
                return array(
                    UserFixtures::class,
                );
            }            
créons ces références dans UserFixtures.php

     // Autant d'utilisateurs que l'on souhaite
            for($i=0;$i<50;$i++) {
    
                // création d'une instance de Entity/User
                $user = new User();
                $this->addReference("user_reference_".$i, $user);
                
Ce n'est pas encore vraiment au hasard... mais ça fait ce que l'on veut                                    
#### Pour le hasard, on peut utiliser les variables de sessions pour stocker le nombre de chaques entités générées:

UserFixtures:

    ...
    // création d'une variable de session contenant le nombre d'utilisateur que l'on souhaite créer
     $_SESSION['nb_users']=150;
    
     // Autant d'utilisateurs que l'on souhaite
     for($i=0;$i<$_SESSION['nb_users'];$i++) {
    
         // création d'une instance de Entity/User
         $user = new User();
    
         // on crée autant de références que d'utilisateurs que l'on souhaite créer, il seront utilisés dans ArticleFixtures.php
         $this->addReference("mes_users_".$i,$user);
         ...     
ArticleFixtures ! Il faut implémenter la classe ArticleFixtures pour l'obliger à charger les utilisateurs en premier lieu:

    ...
    // pour faire communiquer les fichiers de fixtures entre eux
    use Doctrine\Common\DataFixtures\DependentFixtureInterface;
    ...
    class ArticleFixtures extends Fixture implements DependentFixtureInterface
    ...
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
     $slug = $fake->slug;
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
    ...
    ...
    // Comme on ajoute une interface, on doit suivre ses règles, donc ajouter la méthode:
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
Et de même pour CategFixtures.php

    <?php
    
    namespace App\DataFixtures;
    
    
    use App\Entity\Categ;
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Common\DataFixtures\DependentFixtureInterface;
    use Doctrine\Common\Persistence\ObjectManager;
    use Faker\Factory;
    
    class CategFixtures extends Fixture implements DependentFixtureInterface
    {
        public function load(ObjectManager $manager)
        {
            $fake = Factory::create("fr_BE");
    
            // nombre de catégories
            $_SESSION['nb_categ']=8;
    
            // on récupère le nombre d'articles
            $nb_article = $_SESSION['nb_article'];
    
            for($i=0;$i<$_SESSION['nb_categ'];$i++) {
    
                $categ = new Categ();
    
                $this->addReference("mes_categs_".$i,$categ);
    
    
    
                // setters de la table categ
                $categ->setTitre($fake->sentence(8,true))
                ->setSlug($fake->slug(6,true))
                ->setDescr($fake->sentence(25,true));
    
                // nombre d'articles se trouvant dans cette rubrique (entre 1 et 20)
                $nbArticle = random_int(1,50);
    
                // tant qu'on doit rajouter des articles
                for($b=0;$b<$nbArticle;$b++) {
                    $recupArticle = $this->getReference("mes_articles_".random_int(0,$nb_article-1));
                    $categ->addArticleIdarticle($recupArticle);
                }
    
    
                $manager->persist($categ);
            }
    
            $manager->flush();
        }
    
        /**
         * Dépendances pour le manyTomany (addArticleIdarticle), on doit déjà avoir des articles pour faire le lien dans categ_has_article
         */
        public function getDependencies()
        {
            return array(
                ArticleFixtures::class,
            );
        }
    }
Et pour exécuter l'insertion dans la DB:

    php bin/console doctrine:fixtures:load

### Première requête avec Doctrine
On effectue cette requête depuis HomeController.php, comme on récupère une menu (table categ) on fait un use de son entité

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Annotation\Route;
    // nécessaire pour la requête du menu
    use App\Entity\Categ;
Doctrine est chargé depuis src/Entity/Categ.php 
   
Pour récupérer toutes les categ, on utilise le findall sur la classe Categ:

    // Doctrine récupère tous les champs de la table Categ
    $recupMenu = $this->getDoctrine()->getRepository(Categ::class)->findAll();     
    
Puis on passe cette variable à la vue (findall crée un tableau indexé contenant toutes les réponses à notre requête)  

    // chargement du template
    return $this->render('home/index.html.twig', [
    // envoi du résultat de la requête à twig sous le nom "suitemenu"
          "suitemenu"=>$recupMenu,
    ]);
Dans templates/home/index.html?twig

    {% block menuhaut %}
        {% for item in suitemenu %}
        <li class="nav-item">
            <a class="nav-link" href="?rubrique={{ item.slug }}">{{ item.titre }}</a>
        </li>
        {% endfor %}
    {% endblock %}       
#### Création de la route vers les catégories
Dans HomeController.php

    /**
     * @Route("/categ/{slug}", name="categ")
     */
    public function detailCateg($slug){
        return new Response($slug);
    } 
                     
Puis dans la vue templates/home/index.html :
    
    {% block menuhaut %}
        {% for item in suitemenu %}
        <li class="nav-item">
            {# chemin vers la route nommée "categ" avec son paramètre obligatoire {slug auquel on passe le vrai slug venant de la requête #}
            <a class="nav-link" href="{{ path("categ",{slug:item.slug}) }}">{{ item.titre }}</a>
        </li>
        {% endfor %}
    {% endblock %}
#### On récupère tous les articles
homeController:
    
    ...
    // nécessaire pour les articles
    use App\Entity\Article;    
    ...
    // Doctrine récupère les articles
    $recupArticles = $this->getDoctrine()->getRepository(Article::class)->findAll();
    
    //dump($recupMenu);
    
    // chargement du template
    return $this->render('home/index.html.twig', [
        // envoi du résultat de la requête à twig sous le nom "suitemenu"
        "suitemenu"=>$recupMenu,
        "articles"=>$recupArticles,
     ]);   
C'est la manère dont on appel ce qu'on veut voir dans twig qui va changer la requête, ici on joint la table user automatiquement grâce à l'id :

    {{ item.userIduser.thename }}
    
home/index.html.twig

    {% block content %}
            <!-- Begin page content -->
            <main role="main" class="flex-shrink-0">
                <div class="container">
                    <h1 class="mt-5">Nos articles</h1>
                    <p class="lead">Nos 10 derniers articles</p>
                    {% for item in articles %}
                    <hr>
                    <h3>{{ item.titre }}</h3>
                    <p>{{ item.texte }}</p>
                    <p>{{ item.userIduser.thename }}</p>
                    {% endfor %}
                </div>
            </main>
        {% endblock %}   
#### récupération de 10 articles avec findBy

    // Doctrine récupère les 10 derniers articles
    $recupArticles = $this->getDoctrine()->getRepository(Article::class)->findBy([],["thedate"=>"DESC"],10);       
                   
### jointures automatiques depuis twig !
dans /home/index.html.twig   

        <div class="container">
           <h1 class="mt-5">Nos articles</h1>
           <p class="lead">Nos 10 derniers articles</p>
           {% for item in articles %}
           <hr>
           <h3>{{ item.titre }}</h3>
           <h6>Catégories:
           
               {# Tant que l'on a des catégories pour cet article#}
               {% for cat in item.categIdcateg %}
                   <a href="{{ path("categ",{slug:cat.slug}) }}">{{ cat.titre }}</a>
                   
                   {# si on est pas au dernier tour, on rajoute un | #}
                   {% if not loop.last %} | {% endif %}
                   
               {# Cet article n'est dans aucune catégorie #}
               {% else %}
                Aucune catégorie
               {% endfor %}
           </h6>
           <p>{{ item.texte }}</p>
           <p>{{ item.userIduser.thename }}</p>
           {% endfor %}
         </div>
                      
#### Création d'un service personnel
Créons un dossier Utils dans src, puis une classe Slug.php avec une métode statique pour transformer les titres en slug pour les URL

    namespace App\Utils;
    
    // création d'une classe pour sluggifier du texte (pour les URL)
    class Slug
    {
        public static function slugletexte(string $string): string
        {
            return preg_replace('/\s+/', '-', mb_strtolower(trim(strip_tags($string)), 'UTF-8'));
        }
    }
Pour vérifier que le Slug est activé:

    php bin/console debug:autowiring --all
#### Utilisation du Slug dans les fixtures
Dans CategFixtures.php

    ...
    // on a besoin de sluggifier les titres des catégories
    use App\Utils\Slug;
    ...
    // setters de la table categ
    $titre = $fake->words(2);
    // titre
    $categ->setTitre($titre)
    
    // titre slugifié par notre classe Slug avec la méthode static ::slugletexte
    ->setSlug(Slug::slugletexte($titre))
    ->setDescr($fake->sentence(25,true));
et dans ArticleFixtures.php

    // phrase de 1 à 8 mots
    $titre = $fake->sentence(8, true);
    // slug
    $slug = Slug::slugletexte($titre);
    
### création d'une classe (un service)
Dans src/Utils/TraiteTexte.php

    namespace App\Utils;
    
    
    class TraiteTexte
    {
    
        /*
         * méthode statique permettant grâce à ces arguments de couper un texte sans couper les mots
         * Raccourci(string $texte, int $length): string
         */
        public static function Raccourci(string $texte,int $length): string {
    
            // texte plus cours que la longueur de découpe, on renvoie le texte non modifié
            if(strlen($texte)<=$length) return $texte;
    
            // on coupe à la longueur indiquée, sans tenir compte de la césure des mots
            $coupetexte = substr($texte,0,$length);
    
            // on recoupe la chaîne au dernier espace trouvé
            $coupefinal = substr($coupetexte,0,strrpos($coupetexte," ") );
    
            return $coupefinal."...";
    
        }
    
    }
Et on va l'utiliser dans HomeController tant que truncate n'est pas fonctionel:

    // Doctrine récupère les 10 derniers articles
            $recupArticles = $this->getDoctrine()->getRepository(Article::class)->findBy([],["thedate"=>"DESC"],10);
    
     // on va faire une boucle tant qu'on a des articles pour raccourcir le texte tant que le plugin composer require twig/extensions (pas encore compatibler Twig 3)
     foreach ($recupArticles as $valeur){
         // on récupère le texte
         $txt = $valeur->getTexte();
         // on remet le texte raccourci par notre service TraiteTexte
         $valeur->setTexte(TraiteTexte::Raccourci($txt,200));
     }                                 

 ### création des liens sur articles
 dans HomeController.php
 
    /**
     * @Route("/article/{slug}", name="article")
     */
    public function detailArticle($slug){
        return new Response($slug);
    }    
 #### Dans /home/index.html.twig
 
    <p>{{ item.texte }}
     <br><a href="{{ path("article",{slug:item.slug}) }}">Lire la suite</a></p>   
 pour afficher la date, on a une erreur si on la met telle quelle, on doit ajouter la filtre Twig date:
 
    {{ item.thedate|date('d/m/Y \à H\\hi') }}</p>
    
 ### Création des autres vues      
 
 #### templates/home/categ.html.twig
 c'est une copie exacte de index.html.twig que l'on va modifier
 
 ##### On change dans homeController:
 
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
    
 ##### On change dans categ.html.twig:   
 
    {% extends 'bootstrap4.html.twig' %}
    
    {% block title %}{{ parent() }} | Catégorie | {{ categ.titre}}{% endblock %}
    
    {% block menuhaut %}
            {% include'home/menuhaut.html.twig' %}
    {% endblock %}
    
        {% block content %}
            <!-- Begin page content -->
            <main role="main" class="flex-shrink-0">
                <div class="container"><hr>
                    <h1 class="mt-5"><small>Catégorie :</small> {{ categ.titre}}</h1><hr>
                    <p class="lead">{{ categ.descr }}</p>
                    {% for item in articles %}
                        <hr>
                        <h3>{{ item.titre }}</h3>
                        <h6>Catégories:
                            {# Tant que l'on a des catégories pour cet article#}
                            {% for cat in item.categIdcateg %}
                                <a href="{{ path("categ",{slug:cat.slug}) }}">{{ cat.titre }}</a>
                                {# si on est pas au dernier tour, on rajoute un | #}
                                {% if not loop.last %} | {% endif %}
                                {# Cet article n'est dans aucune catégorie #}
                            {% else %}
                                Aucune catégorie
                            {% endfor %}
                        </h6>
                        <p>{{ item.texte }}
                            <br><a href="{{ path("article",{slug:item.slug}) }}">Lire la suite</a></p>
                        <p><a href="{{ path("user",{thelogin:item.userIduser.thelogin}) }}">{{ item.userIduser.thename }}</a> le {{ item.thedate|date('d/m/Y \à H\\hi') }}</p>
                    {% endfor %}
                </div>
            </main>
        {% endblock %}
        
#### Création de la vue article.html.twig
Dans Templates/home/

    {% extends 'bootstrap4.html.twig' %}
    
    {% block title %}{{ parent() }} | Article | {{ article.titre }} {% endblock %}
    
    {% block menuhaut %}
            {% include'home/menuhaut.html.twig' %}
    {% endblock %}
    
        {% block content %}
            <!-- Begin page content -->
            <main role="main" class="flex-shrink-0">
                <div class="container"><hr>
                    <h1 class="mt-5"><small>Article : {{ article.titre }} </small> </h1><hr>
                    <p class="lead">Voici le détail de cet article</p>
    
                        <hr>
                        <h3>{{ article.titre }}</h3>
                        <h6>Catégories:
                            {# Tant que l'on a des catégories pour cet article#}
                            {% for cat in article.categIdcateg %}
                                <a href="{{ path("categ",{slug:cat.slug}) }}">{{ cat.titre }}</a>
                                {# si on est pas au dernier tour, on rajoute un | #}
                                {% if not loop.last %} | {% endif %}
                                {# Cet article n'est dans aucune catégorie #}
                            {% else %}
                                Aucune catégorie
                            {% endfor %}
                        </h6>
                        <p>{{ article.texte|nl2br }}</p>
                        <p><a href="{{ path("user",{thelogin:article.userIduser.thelogin}) }}">{{ article.userIduser.thename }}</a> le {{ article.thedate|date('d/m/Y \à H\\hi') }}</p>
                </div>
            </main>
        {% endblock %}
    
    

    
#### Création du detailArticle
Dans homeController

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
    
#### Affichage du nombre d'articles par catégories
dans home/categ.html.twig

    <h1 class="mt-5"><small>Catégorie :</small> {{ categ.titre}} 
    <small class="text-success">({{ categ.articleIdarticle.count }} articles)</small></h1><hr>
    
#### Affichage des utilisateurs
dans HomeController.php

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
        
Et dans la vue user.html.twig: (utilisation de article|lenght pour connaître le nombre d'article)    
       
       {% extends 'bootstrap4.html.twig' %}
       
       {% block title %}{{ parent() }} | Utilisateur | {{ utilisateur.thename}}{% endblock %}
       
       {% block menuhaut %}
               {% include'home/menuhaut.html.twig' %}
       {% endblock %}
       
           {% block content %}
               <!-- Begin page content -->
               <main role="main" class="flex-shrink-0">
                   <div class="container"><hr>
                       <h1 class="mt-5"><small>Utilisateur :</small> {{ utilisateur.thename}} <small class="text-success">{{ article|length}} articles</small></h1><hr>
                       <p class="lead">Login de l'utilisateur: {{ utilisateur.thelogin }}</p>
                       {% for item in article %}
                           <hr>
                           <h3>{{ item.titre }}</h3>
                           <h6>Catégories:
                               {# Tant que l'on a des catégories pour cet article#}
                           {% for cat in item.categIdcateg %}
                               <a href="{{ path("categ",{slug:cat.slug}) }}">{{ cat.titre }}</a>
                               {# si on est pas au dernier tour, on rajoute un | #}
                               {% if not loop.last %} | {% endif %}
                               {# Cet article n'est dans aucune catégorie #}
                           {% else %}
                               Aucune catégorie
                           {% endfor %}
                       </h6>
                       <p>{{ item.texte }}
                           <br><a href="{{ path("article",{slug:item.slug}) }}">Lire la suite</a></p>
                       <p><a href="{{ path("user",{thelogin:item.userIduser.thelogin}) }}">{{ item.userIduser.thename }}</a> le {{ item.thedate|date('d/m/Y \à H\\hi') }}</p>
                   {% endfor %}
               </div>
           </main>
       {% endblock %}
       
#### CRUD pour article

    php bin/console make:crud    
    
Dans ArticleController.php, changeons l'url pour qu'elle fasse partie d'une section admin

    /**
     * @Route("/admin/article")
     */       
Pour accèder au crud des articles:
http://sym44lts/admin/article/

Pour ajouter et modifier, on obtient des erreurs de type __tostring:

    Catchable Fatal Error: Object of class 
    App\Entity\User could not be converted to string       
       
On ouvre le fichier user, et on crée la méthode mage __tostring, qui transformera l'objet User en chaîne de caractère à la demande  :

    // création d'une méthode __tostring qui affichera les texte voulu si on essaye 
    // de lire l'objet comme si c'était une chaîne de caractère
        public function __toString():string
        {
            return $this->getThename();
        }       
Même faute pour categ

    Catchable Fatal Error: Object of class App\Entity\Categ could not be converted to string   
Avec le choix d'un __tostring pour l'afficher:
        
        public function __toString():string
            {
                return $this->getTitre();
            }
Cette erreur est commune, elle vous permet de choisir un champs

#### Erreur d'attribution des categ aux articles

Symfony par défaut créer un sens pour les relation many to many

Dans Categ, nous avons le lien complet vers l'article :

     /**
      * @var \Doctrine\Common\Collections\Collection
      *
      * @ORM\ManyToMany(targetEntity="Article", inversedBy="categIdcateg")
      * @ORM\JoinTable(name="categ_has_article",
      *   joinColumns={
      *     @ORM\JoinColumn(name="categ_idcateg", referencedColumnName="idcateg")
      *   },
      *   inverseJoinColumns={
      *     @ORM\JoinColumn(name="article_idarticle", referencedColumnName="idarticle")
      *   }
      * )
      */
     private $articleIdarticle;      
     
Par contre dans Article.php, nous avons juste une référence:

     /**
      * @var \Doctrine\Common\Collections\Collection
      *
      * @ORM\ManyToMany(targetEntity="Categ", mappedBy="articleIdarticle")
      */
     private $categIdcateg;      
     
###### Ceci fonctionne très bien si on gère les categ et qu'on veut rajouter des articles... Mais ça peut poser des problèmes son on gère les articles et qu'on veut y rajouter des catégories.  

#### Pour ne jamais avoir de problèmes entre relation many to many (dans toutes les directions) 

     dans categ.php, on retire juste le inversedBy="categIdcateg":
     
     
          /**
           * @var \Doctrine\Common\Collections\Collection
           *
           * @ORM\ManyToMany(targetEntity="Article")
           * @ORM\JoinTable(name="categ_has_article",
           *   joinColumns={
           *     @ORM\JoinColumn(name="categ_idcateg", referencedColumnName="idcateg")
           *   },
           *   inverseJoinColumns={
           *     @ORM\JoinColumn(name="article_idarticle", referencedColumnName="idarticle")
           *   }
           * )
           */
          private $articleIdarticle;
Ensuite on copie uniquement l'annotation de /** à */   

Puis on ouvre Article.php  

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Categ", mappedBy="articleIdarticle")
     */
    private $categIdcateg;  
    
on copie les annotations de categ et on remplace celles de Article, en changeant le nom de la cible, puis on inverse les 2 joinColumns  :

    /**
    * @var \Doctrine\Common\Collections\Collection
    *
    * @ORM\ManyToMany(targetEntity="Categ")
    * @ORM\JoinTable(name="categ_has_article",
    *   joinColumns={
    *   @ORM\JoinColumn(name="article_idarticle", referencedColumnName="idarticle")
    *   },
    *   inverseJoinColumns={
    *     @ORM\JoinColumn(name="categ_idcateg", referencedColumnName="idcateg")
    *   }
    * )
    */  
Et le problème est résolu   

### Pour avoir un temps par défaut lors de la création d'un article

    dans Article.php:
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categIdcateg = new \Doctrine\Common\Collections\ArrayCollection();
        // on va mettre la date actuelle par défault si on crée un nouvel article (new Article)
        $this->setThedate(new \Datetime());
    }
#### Bug des dates inexistante en dehors de 2014 et 2024:

Dans src/Form/ArticleType.php

    public function buildForm(FormBuilderInterface $builder, array $options)
        {

            $builder
                ->add('titre')
                ->add('slug')
                ->add('texte')
                ->add('thedate',DateTimeType::class,[
                    'date_widget'=>'choice',
                    'required'=>true,
                    'years' => range((int) date('Y') - 50, (int) date('Y') + 50),

                ])
                ->add('userIduser')
                ->add('categIdcateg')

            ;
        }
Notre problème de date est réglé

#### Pour débugger les formulaires

    php bin/console debug:form ArticleType

#### Pour être obligé de choisir un utilisateur
Comme le champs est en NULL (pour le CASCADE SET NULL)

    public function buildForm(FormBuilderInterface $builder, array $options)
        {

            $builder
                ->add('titre')
                ->add('slug')
                ->add('texte')
                ->add('thedate',DateTimeType::class,[
                    'date_widget'=>'choice',
                    'required'=>true,
                    'years' => range((int) date('Y') - 50, (int) date('Y') + 50),

                ])
                ->add('userIduser',null,['required' => true])
                ->add('categIdcateg')

            ;
        }

#### Pour être obligé de choisir la rubrique avec des checkbox

    ->add('categIdcateg',null,['multiple'=>true,'expanded'=>true])

#### Pour remplacer le thème des formulaires
dans config/packages/twig.yaml ajoutez:

    twig:
        form_themes: ['bootstrap_4_layout.html.twig']

ce qui donnera:

    twig:
        default_path: '%kernel.project_dir%/templates'
        debug: '%kernel.debug%'
        strict_variables: '%kernel.debug%'
        exception_controller: null
        form_themes: ['bootstrap_4_layout.html.twig']


https://symfony.com/doc/4.4/form/bootstrap4.html