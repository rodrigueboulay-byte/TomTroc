<?php
// views/registerView.php

$errors = $errors ?? [];
$old = $old ?? [
    'username' => '',
    'email' => '',
];
?>

<section class="section">
    <div class="container auth">
        <h1 class="section__title">Inscription</h1>

        <?php if (!empty($errors)) : ?>
            <div class="alert alert--error">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php?action=register" class="auth__form">
            <label class="auth__field">
                <span>Pseudo</span>
                <input type="text" name="username" required value="<?= htmlspecialchars($old['username'] ?? ''); ?>">
            </label>
            <label class="auth__field">
                <span>Email</span>
                <input type="email" name="email" required value="<?= htmlspecialchars($old['email'] ?? ''); ?>">
            </label>
            <label class="auth__field">
                <span>Mot de passe</span>
                <input type="password" name="password" required>
            </label>
            <label class="auth__field">
                <span>Confirmation du mot de passe</span>
                <input type="password" name="password_confirm" required>
            </label>

            <button type="submit" class="btn btn--primary">Créer mon compte</button>

            <p class="auth__switch">
                Déjà un compte ? <a href="index.php?action=login">Se connecter</a>
            </p>
        </form>
    </div>
</section>
