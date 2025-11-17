<?php
// controllers/AuthController.php

class AuthController
{
    public function register(): void
    {
        $pageTitle = 'TomTroc – Inscription';
        // plus tard : gestion POST, création utilisateur etc.
        require __DIR__ . '/../views/registerView.php';
    }

    public function login(): void
    {
        $pageTitle = 'TomTroc – Connexion';
        // plus tard : gestion POST, login, $_SESSION...
        require __DIR__ . '/../views/loginView.php';
    }
}
