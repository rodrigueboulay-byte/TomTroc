<?php
// views/errorView.php
$title = $title ?? 'Information';
$message = $message ?? '';
?>

<section class="section">
    <div class="container">
        <h1 class="section__title"><?= htmlspecialchars($title); ?></h1>
        <p><?= htmlspecialchars($message); ?></p>
        <a class="btn btn--primary" href="<?= BASE_URL; ?>index.php?action=home">Retour &agrave; l'accueil</a>
    </div>
</section>
