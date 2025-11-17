<?php
// views/booksListView.php
require __DIR__ . '/templates/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="section__title">Nos livres</h1>
        <p class="section__subtitle">
            Découvrez tous les livres disponibles à l’échange sur TomTroc.
        </p>

        <div class="books-grid">
            <!-- plus tard : boucle sur $books -->
            <p>Liste des livres à venir (BDD)...</p>
        </div>
    </div>
</section>

<?php
require __DIR__ . '/templates/footer.php';
