<?php
// views/accountView.php
require __DIR__ . '/templates/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="section__title">Mon compte</h1>
        <p class="section__subtitle">Gérez vos informations et vos livres.</p>

        <div class="account">
            <div class="account__panel">
                <h2>Mes informations</h2>
                <p>Pseudo : <strong>MonPseudo</strong></p>
                <p>Email : mon.email@example.com</p>
            </div>

            <div class="account__panel">
                <h2>Mes livres</h2>
                <p>(liste à venir) – lien pour éditer un livre :</p>
                <a href="index.php?action=edit-book&id=1">Modifier un livre</a>
            </div>
        </div>
    </div>
</section>

<?php
require __DIR__ . '/templates/footer.php';
