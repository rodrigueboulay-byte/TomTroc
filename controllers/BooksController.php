<?php
// controllers/BooksController.php

class BooksController
{
    private BookManager $bookManager;

    public function __construct(?BookManager $bookManager = null)
    {
        $this->bookManager = $bookManager ?? new BookManager();
    }

    // Nos livres (liste)
    public function list(): void
    {
        $pageTitle = 'TomTroc - Nos livres';
        $books = $this->bookManager->findAll();

        require __DIR__ . '/../views/booksListView.php';
    }

    // Page d'un seul livre
    public function show(): void
    {
        $pageTitle = 'TomTroc - Détail du livre';
        $bookId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($bookId <= 0) {
            throw new InvalidArgumentException('Identifiant de livre invalide.');
        }

        $book = $this->bookManager->find($bookId);

        if ($book === null) {
            throw new RuntimeException('Livre introuvable.');
        }

        require __DIR__ . '/../views/bookSingleView.php';
    }
}

