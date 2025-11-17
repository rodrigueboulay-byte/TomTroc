<?php
// views/bookSingleView.php
require __DIR__ . '/templates/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="section__title">Détail du livre</h1>

        <div class="book-single">
            <div class="book-single__left">
                <div class="book-single__cover"></div>
            </div>
            <div class="book-single__right">
                <h2 class="book-single__title">Titre du livre</h2>
                <p class="book-single__author">Auteur du livre</p>
                <p class="book-single__meta">Genre, état, etc.</p>

                <p class="book-single__owner">
                    Proposé par : <a href="index.php?action=public-account&id=1">Pseudo</a>
                </p>

                <a href="index.php?action=messages" class="btn btn--primary">
                    Contacter pour un échange
                </a>
            </div>
        </div>
    </div>
</section>

<?php
require __DIR__ . '/templates/footer.php';
