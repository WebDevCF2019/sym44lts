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