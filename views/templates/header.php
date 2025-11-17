<?php
// views/templates/header.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'TomTroc'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fonts proches de la maquette -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:wght@400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
</head>
<body class="page">

<header class="header">
    <div class="container header__inner">
        <a href="index.php?action=home" class="logo">
            <div class="logo__icon">
                <span class="logo__letter"></span>
                <span class="logo__letter"></span>
            </div>
            <span class="logo__text">Tom Troc</span>
        </a>

        <nav class="nav">
            <a href="index.php?action=home" class="nav__link<?= ($_GET['action'] ?? 'home') === 'home' ? ' nav__link--active' : '' ?>">Accueil</a>
            <a href="index.php?action=books" class="nav__link<?= ($_GET['action'] ?? '') === 'books' ? ' nav__link--active' : '' ?>">Nos livres</a>
            <a href="index.php?action=messages" class="nav__link<?= ($_GET['action'] ?? '') === 'messages' ? ' nav__link--active' : '' ?>">Messagerie</a>
            <a href="index.php?action=account" class="nav__link<?= ($_GET['action'] ?? '') === 'account' ? ' nav__link--active' : '' ?>">Mon compte</a>
        </nav>

        <div class="header__actions">
            <a href="index.php?action=login" class="header__auth-link">Connexion</a>
            <a href="index.php?action=register" class="header__auth-link">Inscription</a>

            <a href="index.php?action=messages" class="header__icon header__icon--messages">
                <span class="header__icon-badge">1</span>
                <span class="sr-only">Messagerie</span>
            </a>

            <a href="index.php?action=account" class="header__icon header__icon--account">
                <span class="sr-only">Mon compte</span>
            </a>
        </div>
    </div>
</header>

<main class="main">
