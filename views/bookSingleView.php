<?php
// views/bookSingleView.php
require __DIR__ . '/templates/header.php';

$owner = $book->getOwner();
$coverStyle = '';
if ($book->getCoverImagePath()) {
    $coverStyle = 'style="background-image:url(' . htmlspecialchars($book->getCoverImagePath(), ENT_QUOTES) . ');"';
}
?>

<section class="section">
    <div class="container">
        <h1 class="section__title"><?= htmlspecialchars($book->getTitle()); ?></h1>

        <div class="book-single">
            <div class="book-single__left">
                <div class="book-single__cover" <?= $coverStyle; ?>></div>
            </div>
            <div class="book-single__right">
                <p class="book-single__author">&Eacute;crit par <?= htmlspecialchars($book->getAuthor()); ?></p>

                <ul class="book-single__meta">
                    <?php if ($book->getGenre()) : ?>
                        <li>Genre : <?= htmlspecialchars($book->getGenre()->getName()); ?></li>
                    <?php endif; ?>
                    <li>&Eacute;tat : <?= htmlspecialchars($book->getCondition()); ?></li>
                    <li>
                        Disponibilit&eacute; :
                        <?= $book->isAvailable() ? '<span class="badge badge--success">Disponible</span>' : '<span class="badge badge--muted">Indisponible</span>'; ?>
                    </li>
                    <li>Ajout&eacute; le <?= $book->getCreatedAt()->format('d/m/Y'); ?></li>
                </ul>

                <?php if ($book->getDescription()) : ?>
                    <div class="book-single__description">
                        <h2>Description</h2>
                        <p><?= nl2br(htmlspecialchars($book->getDescription())); ?></p>
                    </div>
                <?php endif; ?>

                <p class="book-single__owner">
                    Propos&eacute; par :
                    <a href="index.php?action=public-account&id=<?= $owner->getId(); ?>">
                        <?= htmlspecialchars($owner->getUsername()); ?>
                    </a>
                    <?php if ($owner->getCity()) : ?>
                        <span class="book-single__owner-city"> - <?= htmlspecialchars($owner->getCity()); ?></span>
                    <?php endif; ?>
                </p>

                <a href="index.php?action=messages" class="btn btn--primary">
                    Contacter pour un &eacute;change
                </a>
            </div>
        </div>
    </div>
</section>

<?php
require __DIR__ . '/templates/footer.php';
