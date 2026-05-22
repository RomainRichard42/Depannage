<?php

namespace Core;

/**
 * Routeur Central (L'Aiguilleur du site)
 *
 * Il reçoit l'URL demandée par le visiteur et la met en relation avec 
 * le Contrôleur et la méthode appropriés.
 *
 * Pour cela, il remplit un catalogue interne classé par type de requête 
 * (GET pour la lecture, POST pour les formulaires) associé aux actions à mener.
 */
class Router {
    private array $routes = [];

    // ajoute une route GET à la table
    public function get(string $path, string $controller, string $method): void {
        $this->routes['get'][$path] = [
            'controller' => $controller,
            'method'     => $method
        ];
    }

    // ajoute une route POST à la table
    public function post(string $path, string $controller, string $method): void {
        $this->routes['post'][$path] = [
            'controller' => $controller,
            'method'     => $method
        ];
    }

    // ajoute une route PUT à la table
    public function put(string $path, string $controller, string $method): void {
        $this->routes['put'][$path] = [
            'controller' => $controller,
            'method'     => $method
        ];
    }

    public function display(): void {
        echo '<pre>';
        print_r($this->routes);
        echo '</pre>';
    }
}