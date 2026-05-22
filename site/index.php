<?php
require_once __DIR__ . '/includes/router.php';


use Core\Router; // On importe la classe Router du namespace Core

$router = new Router();
$router->get('/', 'HomeController', 'index'); // Route pour la page d'accueil
$router->get('/about', 'AboutController', 'index'); // Route pour la page "À propos"

$router->post('/contact', 'ContactController', 'submit'); // Route pour le formulaire de contact
$router->post('/login', 'AuthController', 'login'); // Route pour le formulaire de connexion
$router->put('/users/{id}', 'UserController', 'update'); // Route pour mettre à jour un utilisateur

$router->display(); // Affiche la configuration des routes pour vérification (optionnel)