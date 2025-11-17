<?php
// controllers/BooksController.php

class BooksController
{
    private BookRepository $bookRepository;

    public function __construct(?BookRepository $bookRepository = null)
    {
        $this->bookRepository = $bookRepository ?? new BookRepository();
    }

    // Nos livres (liste)
    public function list(): void
    {
        $pageTitle = 'TomTroc - Nos livres';
        $books = $this->bookRepository->findAll();

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

        $book = $this->bookRepository->find($bookId);

        if ($book === null) {
            throw new RuntimeException('Livre introuvable.');
        }

        require __DIR__ . '/../views/bookSingleView.php';
    }
}
