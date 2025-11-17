<?php
// controllers/HomeController.php

class HomeController
{
    public function home(): void
    {
        $pageTitle = 'TomTroc – Accueil';
        require __DIR__ . '/../views/homeView.php';
    }
}
