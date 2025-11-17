<?php
// views/editBookView.php
require __DIR__ . '/templates/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="section__title">Éditer un livre</h1>

        <form method="post" action="index.php?action=edit-book&id=1" class="book-edit-form">
            <label class="auth__field">
                <span>Titre</span>
                <input type="text" name="title" value="Titre existant">
            </label>
            <label class="auth__field">
                <span>Auteur</span>
                <input type="text" name="author" value="Auteur existant">
            </label>
            <label class="auth__field">
                <span>Genre</span>
                <input type="text" name="genre" value="Genre">
            </label>
            <label class="auth__field">
                <span>État du livre</span>
                <input type="text" name="state" value="Bon état">
            </label>

            <button type="submit" class="btn btn--primary">Enregistrer les modifications</button>
        </form>
    </div>
</section>

<?php
require __DIR__ . '/templates/footer.php';
