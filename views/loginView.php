<?php
// views/loginView.php
require __DIR__ . '/templates/header.php';
?>

<section class="section">
    <div class="container auth">
        <h1 class="section__title">Connexion</h1>

        <form method="post" action="index.php?action=login" class="auth__form">
            <label class="auth__field">
                <span>Email ou pseudo</span>
                <input type="text" name="login" required>
            </label>
            <label class="auth__field">
                <span>Mot de passe</span>
                <input type="password" name="password" required>
            </label>

            <button type="submit" class="btn btn--primary">Se connecter</button>

            <p class="auth__switch">
                Pas encore de compte ? <a href="index.php?action=register">S'inscrire</a>
            </p>
        </form>
    </div>
</section>

<?php
require __DIR__ . '/templates/footer.php';
