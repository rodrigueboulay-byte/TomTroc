<?php
// index.php

session_start();

require_once __DIR__ . '/config/config.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/controllers/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/services/' . $class . '.php',
        __DIR__ . '/core/' . $class . '.php',
        __DIR__ . '/helpers/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

$action = $_GET['action'] ?? 'home';

try {
    switch ($action) {
        case 'home':
            $controller = new HomeController();
            $controller->home();
            break;

        case 'books':             // Nos livres
            $controller = new BooksController();
            $controller->list();
            break;

        case 'book':              // Single livre (id en GET)
            $controller = new BooksController();
            $controller->show();
            break;

        case 'register':          // Inscription
            $controller = new AuthController();
            $controller->register();
            break;

        case 'login':             // Connexion
            $controller = new AuthController();
            $controller->login();
            break;

        case 'logout':            // Déconnexion
            $controller = new AuthController();
            $controller->logout();
            break;

        case 'account':           // Mon compte (compte perso connecté)
            $controller = new AccountController();
            $controller->account();
            break;

        case 'public-account':    // Compte public
            $controller = new AccountController();
            $controller->publicAccount();
            break;

        case 'edit-book':         // Edition livre
            $controller = new AccountController();
            $controller->editBook();
            break;

        case 'add-book':          // Ajout livre
            $controller = new AccountController();
            $controller->addBook();
            break;

        case 'delete-book':       // Suppression livre
            $controller = new AccountController();
            $controller->deleteBook();
            break;

        case 'messages':          // Messagerie
            $controller = new MessageController();
            $controller->inbox();
            break;

        default:
            // 404 simple
            $pageTitle = 'Page introuvable';
            $view = new View($pageTitle);
            $view->render('errorView', [
                'title' => '404',
                'message' => 'Page introuvable.',
            ]);
            break;
    }
} catch (Exception $e) {
    $pageTitle = 'Erreur';
    $view = new View($pageTitle);
    $view->render('errorView', [
        'title' => 'Une erreur est survenue',
        'message' => $e->getMessage(),
    ]);
}
