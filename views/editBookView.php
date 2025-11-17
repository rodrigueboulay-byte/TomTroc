<?php
// views/editBookView.php

$bookData = $bookData ?? [];
$errors = $errors ?? [];
$genres = $genres ?? [];
$conditions = $conditions ?? [];
$isEdit = !empty($book ?? null);

require __DIR__ . '/templates/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="section__title"><?= $isEdit ? 'Modifier mon livre' : 'Ajouter un livre'; ?></h1>
        <p class="section__subtitle">
            <?= $isEdit ? 'Mettez à jour les informations de votre livre pour les autres membres.' : 'Ajoutez un nouveau livre disponible pour échange.'; ?>
        </p>

        <?php if (!empty($errors)) : ?>
            <div class="alert alert--error">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" class="book-edit-form" enctype="multipart/form-data">
            <label class="auth__field">
                <span>Titre</span>
                <input type="text" name="title" required value="<?= htmlspecialchars($bookData['title'] ?? ''); ?>">
            </label>

            <label class="auth__field">
                <span>Auteur</span>
                <input type="text" name="author" required value="<?= htmlspecialchars($bookData['author'] ?? ''); ?>">
            </label>

            <label class="auth__field">
                <span>Genre</span>
                <select name="genre_id">
                    <option value="">-- Sélectionner un genre --</option>
                    <?php foreach ($genres as $genre) : ?>
                        <option value="<?= $genre->getId(); ?>" <?= ((int) ($bookData['genre_id'] ?? 0) === $genre->getId()) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($genre->getName()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="auth__field">
                <span>Description</span>
                <textarea name="description" rows="4"><?= htmlspecialchars($bookData['description'] ?? ''); ?></textarea>
            </label>

            <label class="auth__field">
                <span>État du livre</span>
                <select name="condition" required>
                    <?php foreach ($conditions as $key => $label) : ?>
                        <option value="<?= $key; ?>" <?= ($bookData['condition'] ?? '') === $key ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label class="auth__checkbox">
                <input type="checkbox" name="is_available" value="1" <?= !empty($bookData['is_available']) ? 'checked' : ''; ?>>
                <span>Disponible pour échange</span>
            </label>

            <label class="auth__field">
                <span>URL de l'image de couverture</span>
                <?php if (!empty($bookData['cover_image_path'])) : ?>
                    <div class="book-edit-cover-preview" style="background-image:url('<?= htmlspecialchars($bookData['cover_image_path']); ?>');"></div>
                <?php endif; ?>
                <input type="url" name="cover_url" placeholder="https://..." value="<?= htmlspecialchars($bookData['cover_image_path'] ?? ''); ?>">
                <small>Utilise un lien direct vers l'image (JPG, PNG, WebP)</small>
            </label>

            <button type="submit" class="btn btn--primary">
                <?= $isEdit ? 'Enregistrer les modifications' : 'Ajouter mon livre'; ?>
            </button>
        </form>
    </div>
</section>

<?php
require __DIR__ . '/templates/footer.php';
