<?php
// controllers/HomeController.php

class HomeController
{
    private BookManager $bookManager;

    public function __construct(?BookManager $bookManager = null)
    {
        $this->bookManager = $bookManager ?? new BookManager();
    }

    public function home(): void
    {
        $pageTitle = 'TomTroc - Accueil';
        $latestBooks = $this->bookManager->findLatest(4);
        $heroCovers = $this->bookManager->findLatest(10);

        require __DIR__ . '/../views/homeView.php';
    }
}

