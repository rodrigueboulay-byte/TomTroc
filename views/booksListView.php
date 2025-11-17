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
            <?php if (!empty($books)) : ?>
                <?php foreach ($books as $book) : ?>
                    <?php $owner = $book->getOwner(); ?>
                    <article class="book-card book-card--list">
                        <a href="index.php?action=book&id=<?= $book->getId(); ?>" class="book-card__cover-link">
                            <div class="book-card__cover">
                                <?php if ($book->getCoverImagePath()) : ?>
                                    <img src="<?= htmlspecialchars($book->getCoverImagePath()); ?>" alt="Couverture de <?= htmlspecialchars($book->getTitle()); ?>">
                                <?php else : ?>
                                    <span class="book-card__cover-placeholder">Pas d'image</span>
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="book-card__body">
                            <h3 class="book-card__title">
                                <a href="index.php?action=book&id=<?= $book->getId(); ?>">
                                    <?= htmlspecialchars($book->getTitle()); ?>
                                </a>
                            </h3>
                            <p class="book-card__author"><?= htmlspecialchars($book->getAuthor()); ?></p>
                            <p class="book-card__seller">
                                <span class="book-card__seller-label">Proposé par :</span>
                                <span class="book-card__seller-name"><?= htmlspecialchars($owner->getUsername()); ?></span>
                                <?php if ($owner->getCity()) : ?>
                                    <span class="book-card__seller-city"> - <?= htmlspecialchars($owner->getCity()); ?></span>
                                <?php endif; ?>
                            </p>
                            <?php if ($book->getGenre()) : ?>
                                <p class="book-card__genre">Genre : <?= htmlspecialchars($book->getGenre()->getName()); ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Aucun livre n'a encore été ajouté.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
require __DIR__ . '/templates/footer.php';
