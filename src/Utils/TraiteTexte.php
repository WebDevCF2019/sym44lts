<?php


namespace App\Utils;


class TraiteTexte
{

    /**
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

        return $coupefinal;

    }

}