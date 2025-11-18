<?php
// views/editBookView.php

$bookData = $bookData ?? [];
$errors = $errors ?? [];
$genres = $genres ?? [];
$conditions = $conditions ?? [];
$isEdit = !empty($book ?? null);
?>

<section class="section">
    <div class="container">
        <h1 class="section__title"><?= $isEdit ? "Modifier mon livre" : "Ajouter un livre"; ?></h1>
        <p class="section__subtitle">
            <?= $isEdit ? "Mettez &agrave; jour les informations de votre livre pour les autres membres." : "Ajoutez un nouveau livre disponible pour &eacute;change."; ?>
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

        <div class="book-edit-layout">
            <form method="post" class="book-edit-form" enctype="multipart/form-data">
                <div class="book-edit-card">
                    <h3 class="book-edit-card__title">Informations principales</h3>
                    <div class="book-edit-fields book-edit-fields--two">
                        <div class="book-edit-field">
                            <label for="book-title">Titre</label>
                            <input id="book-title" type="text" name="title" required value="<?= htmlspecialchars($bookData["title"] ?? ""); ?>">
                        </div>
                        <div class="book-edit-field">
                            <label for="book-author">Auteur</label>
                            <input id="book-author" type="text" name="author" required value="<?= htmlspecialchars($bookData["author"] ?? ""); ?>">
                        </div>
                    </div>
                    <div class="book-edit-fields book-edit-fields--two">
                        <div class="book-edit-field">
                            <label for="book-genre">Genre</label>
                            <select id="book-genre" name="genre_id">
                                <option value="">-- S&eacute;lectionner un genre --</option>
                                <?php foreach ($genres as $genre) : ?>
                                    <option value="<?= $genre->getId(); ?>" <?= ((int) ($bookData["genre_id"] ?? 0) === $genre->getId()) ? "selected" : ""; ?>>
                                        <?= htmlspecialchars($genre->getName()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="book-edit-field">
                            <label for="book-condition">&Eacute;tat du livre</label>
                            <select id="book-condition" name="condition" required>
                                <?php foreach ($conditions as $key => $label) : ?>
                                    <option value="<?= $key; ?>" <?= ($bookData["condition"] ?? "") === $key ? "selected" : ""; ?>>
                                        <?= htmlspecialchars($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="book-edit-field">
                        <label for="book-description">Description</label>
                        <textarea id="book-description" name="description" rows="5" placeholder="Racontez l&rsquo;histoire, l&rsquo;&eacute;tat et pourquoi il plaira aux autres membres."><?= htmlspecialchars($bookData["description"] ?? ""); ?></textarea>
                    </div>
                </div>

                <div class="book-edit-card">
                    <h3 class="book-edit-card__title">Disponibilit&eacute; & visuel</h3>
                    <div class="book-edit-availability">
                        <label class="book-edit-checkbox">
                            <input type="checkbox" name="is_available" value="1" <?= !empty($bookData["is_available"]) ? "checked" : ""; ?>>
                            <span>Disponible pour &eacute;change</span>
                        </label>
                        <p class="book-edit-hint">D&eacute;cochez cette option si le livre est momentan&eacute;ment indisponible.</p>
                    </div>
                    <div class="book-edit-field">
                        <label for="book-cover-url">URL de l'image de couverture</label>
                        <?php if (!empty($bookData["cover_image_path"])) : ?>
                            <div class="book-edit-cover-preview" style="background-image:url('<?= htmlspecialchars($bookData["cover_image_path"]); ?>');"></div>
                        <?php endif; ?>
                        <input id="book-cover-url" type="url" name="cover_url" placeholder="https://..." value="<?= htmlspecialchars($bookData["cover_image_path"] ?? ""); ?>">
                        <small>Utilisez un lien direct (JPG, PNG, WebP) pour un rendu optimal.</small>
                    </div>
                </div>

                <div class="book-edit-actions">
                    <button type="submit" class="btn btn--primary">
                        <?= $isEdit ? "Enregistrer les modifications" : "Ajouter mon livre"; ?>
                    </button>
                    <a class="btn btn--outline" href="index.php?action=account">Annuler</a>
                </div>
            </form>

            <aside class="book-edit-aside">
                <div class="book-edit-aside__card">
                    <h3>Conseils rapides</h3>
                    <ul>
                        <li>Pr&eacute;cisez l&rsquo;&eacute;tat r&eacute;el pour gagner la confiance des lecteurs.</li>
                        <li>Une description chaleureuse donne envie d&rsquo;ouvrir le livre.</li>
                        <li>Privil&eacute;giez une image lumineuse et bien cadr&eacute;e.</li>
                    </ul>
                </div>
                <div class="book-edit-aside__card">
                    <h3>Envie d'inspiration ?</h3>
                    <p>Consultez vos autres titres sur la page &laquo; Mon compte &raquo; pour harmoniser vos fiches.</p>
                </div>
            </aside>
        </div>
    </div>
</section>
