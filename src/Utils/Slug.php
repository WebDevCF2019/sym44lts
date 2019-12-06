<?php


namespace App\Utils;

// création d'une classe pour sluggifier du texte (pour les URL)
class Slug
{
    public static function slugletexte(string $string): string
    {
        return preg_replace('/\s+/', '-', mb_strtolower(trim(strip_tags($string)), 'UTF-8'));
    }
}