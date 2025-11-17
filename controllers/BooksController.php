<?php
// controllers/BooksController.php

class BooksController
{
    // Nos livres (liste)
    public function list(): void
    {
        $pageTitle = 'TomTroc – Nos livres';

        // plus tard : récup des livres en BDD
        $books = []; 

        require __DIR__ . '/../views/booksListView.php';
    }

    // Page d’un seul livre
    public function show(): void
    {
        $pageTitle = 'TomTroc – Détail du livre';

        // plus tard : $id = (int)($_GET['id'] ?? 0); + fetch BDD
        $book = null;

        require __DIR__ . '/../views/bookSingleView.php';
    }
}
