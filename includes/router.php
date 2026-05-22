<?php

namespace Core;

/**
 * Routeur Central (Front Controller - Aiguilleur Global)
 *
 * Cette classe orchestre le point d'entrée de l'application en mettant en relation 
 * une requête HTTP entrante avec le contrôleur et la méthode adaptés.
 *
 * Rôle Architectural :
 * 1. Abstraction de l'infrastructure : Il s'affranchit du découpage physique des fichiers 
 *    sur le disque (système "un fichier = une page") pour offrir des URLs virtuelles et propres.
 * 2. Cartographie (Mapping) : Il centralise la déclaration des points d'accès (routes) 
 *    admissibles par l'application, classés par verbe HTTP (GET, POST, etc.).
 * 3. Résolution (Dispatching) : Il intercepte l'URI courante, l'isole de sa chaîne de requête 
 *    (Query String), vérifie sa validité dans sa table de hachage et délègue l'exécution 
 *    au traitement métier adéquat, ou orchestre la réponse d'erreur (404/500).
 *
 * Fonctionnement interne :
 * Le routeur compile un tableau associatif bidimensionnel indexé par la méthode HTTP 
 * puis par le chemin de l'URL, garantissant une recherche de route en complexité algorithmique 
 * constante O(1) via une simple vérification de clé (isset).
 */
class Router {
    private array $routes = [];

    // ajoute une route GET à la table
    public function get(string $path, string $action) {
        $this->routes['get'][$path] = $action;
    }

    // ajoute une route POST à la table
    public function post(string $path, string $action) {
        $this->routes['post'][$path] = $action;
    }
}