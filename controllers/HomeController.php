<?php
// controllers/HomeController.php

class HomeController
{
    private BookManager $BookManager;

    public function __construct(?BookManager $BookManager = null)
    {
        $this->BookManager = $BookManager ?? new BookManager();
    }

    public function home(): void
    {
        $pageTitle = 'TomTroc - Accueil';
        $latestBooks = $this->BookManager->findLatest(4);
        $heroCovers = $this->BookManager->findLatest(10);

        require __DIR__ . '/../views/homeView.php';
    }
}

