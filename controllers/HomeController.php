<?php
// controllers/HomeController.php

class HomeController
{
    private BookRepository $bookRepository;

    public function __construct(?BookRepository $bookRepository = null)
    {
        $this->bookRepository = $bookRepository ?? new BookRepository();
    }

    public function home(): void
    {
        $pageTitle = 'TomTroc - Accueil';
        $latestBooks = $this->bookRepository->findLatest(4);
        $heroCovers = $this->bookRepository->findLatest(10);

        require __DIR__ . '/../views/homeView.php';
    }
}
