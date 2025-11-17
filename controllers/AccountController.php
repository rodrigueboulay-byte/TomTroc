<?php
// controllers/AccountController.php

class AccountController
{
    // Mon compte (privé, utilisateur connecté)
    public function account(): void
    {
        $pageTitle = 'TomTroc – Mon compte';
        // plus tard : vérifier $_SESSION, récupérer infos utilisateur, ses livres, etc.
        require __DIR__ . '/../views/accountView.php';
    }

    // Profil public d’un utilisateur
    public function publicAccount(): void
    {
        $pageTitle = 'TomTroc – Profil public';

        // plus tard : $userId = (int)($_GET['id'] ?? 0); + fetch public
        $publicUser = null;

        require __DIR__ . '/../views/publicAccountView.php';
    }

    // Edition d’un livre (par son propriétaire)
    public function editBook(): void
    {
        $pageTitle = 'TomTroc – Éditer un livre';

        // plus tard : $bookId = (int)($_GET['id'] ?? 0); + fetch BDD
        $book = null;

        require __DIR__ . '/../views/editBookView.php';
    }
}
