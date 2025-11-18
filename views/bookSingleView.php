<?php
// views/bookSingleView.php
$owner = $book->getOwner();
?>

<section class="section">
    <div class="container">
        <h1 class="section__title"><?= htmlspecialchars($book->getTitle()); ?></h1>

        <div class="book-single">
            <div class="book-single__left">
                <div class="book-single__cover">
                    <?php if ($book->getCoverImagePath()) : ?>
                        <img src="<?= htmlspecialchars($book->getCoverImagePath()); ?>" alt="Couverture de <?= htmlspecialchars($book->getTitle()); ?>">
                    <?php else : ?>
                        <span class="book-card__cover-placeholder">Pas d'image</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="book-single__right">
                <p class="book-single__author">&Eacute;crit par <?= htmlspecialchars($book->getAuthor()); ?></p>

                <ul class="book-single__meta">
                    <?php if ($book->getGenre()) : ?>
                        <li>Genre : <?= htmlspecialchars($book->getGenre()->getName()); ?></li>
                    <?php endif; ?>
                    <li>&Eacute;tat :
                        <span class="book-condition-badge">
                            <?= htmlspecialchars(StringHelper::bookConditionLabel($book->getCondition())); ?>
                        </span>
                    </li>
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

                <?php if (!empty($_SESSION['user']['id'])) : ?>
                    <?php if ((int) $_SESSION['user']['id'] !== $owner->getId()) : ?>
                        <a href="index.php?action=messages&partner=<?= $owner->getId(); ?>&requested_book_id=<?= $book->getId(); ?>" class="btn btn--primary">
                            Contacter pour un &eacute;change
                        </a>
                    <?php else : ?>
                        <p class="book-single__notice">Ceci est l'un de vos livres.</p>
                    <?php endif; ?>
                <?php else : ?>
                    <a href="index.php?action=login" class="btn btn--primary">
                        Connectez-vous pour proposer un &eacute;change
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
