<?php
$isLoggedIn = !empty($_SESSION['user']);
$currentUser = $_SESSION['user'] ?? null;
$unreadMessages = 0;

if ($isLoggedIn) {
    try {
        $messageManager = new MessageManager();
        $unreadMessages = $messageManager->countUnreadForUser((int) $currentUser['id']);
    } catch (Exception $e) {
        $unreadMessages = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'TomTroc'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Playfair+Display:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL; ?>css/style.css">
</head>
<body class="page">
<header class="header">
    <div class="container header__inner">
        <a href="<?= BASE_URL; ?>index.php?action=home" class="logo">
            <div class="logo__icon">
                <span class="logo__letter"></span>
                <span class="logo__letter"></span>
            </div>
            <span class="logo__text">Tom Troc</span>
        </a>

        <nav class="nav">
            <a href="<?= BASE_URL; ?>index.php?action=home" class="nav__link<?= ($_GET['action'] ?? 'home') === 'home' ? ' nav__link--active' : ''; ?>">Accueil</a>
            <a href="<?= BASE_URL; ?>index.php?action=books" class="nav__link<?= ($_GET['action'] ?? '') === 'books' ? ' nav__link--active' : ''; ?>">Nos livres</a>
            <a href="<?= BASE_URL; ?>index.php?action=messages" class="nav__link<?= ($_GET['action'] ?? '') === 'messages' ? ' nav__link--active' : ''; ?>">Messagerie</a>
            <a href="<?= BASE_URL; ?>index.php?action=account" class="nav__link<?= ($_GET['action'] ?? '') === 'account' ? ' nav__link--active' : ''; ?>">Mon compte</a>
        </nav>

        <div class="header__actions">
            <?php if ($isLoggedIn) : ?>
                <span class="header__welcome">Bonjour <?= htmlspecialchars($currentUser['username'] ?? ''); ?></span>
                <form method="post" action="<?= BASE_URL; ?>index.php?action=logout" class="header__logout-form">
                    <button type="submit" class="header__auth-link header__auth-link--logout">D&eacute;connexion</button>
                </form>
            <?php else : ?>
                <a href="<?= BASE_URL; ?>index.php?action=login" class="header__auth-link">Connexion</a>
                <a href="<?= BASE_URL; ?>index.php?action=register" class="header__auth-link">Inscription</a>
            <?php endif; ?>

            <a href="<?= BASE_URL; ?>index.php?action=messages" class="header__icon header__icon--messages">
                <?php if ($unreadMessages > 0) : ?>
                    <span class="header__icon-badge"><?= $unreadMessages; ?></span>
                <?php endif; ?>
                <span class="sr-only">Messagerie</span>
            </a>

            <a href="<?= BASE_URL; ?>index.php?action=account" class="header__icon header__icon--account">
                <span class="sr-only">Mon compte</span>
            </a>
        </div>
    </div>
</header>

<main class="main">
    <?= $content; ?>
</main>

<footer class="footer">
    <div class="container footer__inner">
        <div class="footer__brand-block">
            <div class="footer__logo">
                <span class="footer__logo-text">TT</span>
            </div>
            <div>
                <p class="footer__brand-name">Tom Troc</p>
                <p class="footer__brand-tagline">La communaut&eacute; d'&eacute;change de livres</p>
            </div>
        </div>

        <div class="footer__links">
            <a href="#" class="footer__link">Politique de confidentialit&eacute;</a>
            <a href="#" class="footer__link">Mentions l&eacute;gales</a>
            <a href="#" class="footer__link">Contact</a>
        </div>

        <div class="footer__socials">
            <a href="#" aria-label="Instagram" class="footer__social">IG</a>
            <a href="#" aria-label="Facebook" class="footer__social">FB</a>
            <a href="#" aria-label="Twitter" class="footer__social">TW</a>
        </div>

        <div class="footer__copyright">
            &copy; <?= date('Y'); ?> Tom Troc. Tous droits r&eacute;serv&eacute;s.
        </div>
    </div>
</footer>
</body>
</html>
