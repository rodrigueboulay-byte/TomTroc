<?php
// views/homeView.php
?>

<section class="hero">
    <div class="container hero__inner">
        <div class="hero__content">
            <h1 class="hero__title">Rejoignez nos lecteurs passionnés</h1>
            <p class="hero__text">
                Donnez une nouvelle vie à vos livres en les échangeant avec d'autres amoureux de la lecture.
                Nous croyons en la magie du partage de connaissances et d'histoires à travers les livres.
            </p>

            <div class="hero__actions">
                <a href="#last-books" class="btn btn--primary">Découvrir</a>
            </div>
        </div>

        <div class="hero__image">
            <div class="hero__image-inner">
                <?php
                $coverUrls = array_values(array_filter(
                    array_map(fn($book) => $book->getCoverImagePath(), $heroCovers ?? []),
                    fn($url) => !empty($url)
                ));
                ?>
                <?php if (!empty($coverUrls)) : ?>
                    <div class="hero__slideshow">
                        <?php foreach ($coverUrls as $index => $coverUrl) : ?>
                            <div class="hero__slide<?= $index === 0 ? ' hero__slide--active' : ''; ?>">
                                <img src="<?= htmlspecialchars($coverUrl); ?>" alt="Couverture mise en avant">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <script>
                        (function () {
                            const slides = document.querySelectorAll('.hero__slide');
                            let idx = 0;
                            setInterval(() => {
                                slides[idx].classList.remove('hero__slide--active');
                                idx = (idx + 1) % slides.length;
                                slides[idx].classList.add('hero__slide--active');
                            }, 3000);
                        })();
                    </script>
                <?php else : ?>
                    <div class="hero__placeholder">Ajoute des livres pour voir leurs couvertures ici</div>
                <?php endif; ?>
            </div>
            <p class="hero__image-credit">Couvertures des membres</p>
        </div>
    </div>
</section>

<section id="last-books" class="section section--light">
    <div class="container">
        <h2 class="section__title">Les derniers livres ajoutés</h2>

        <div class="books-grid">
            <?php if (!empty($latestBooks)) : ?>
                <?php foreach ($latestBooks as $book) : ?>
                    <?php $owner = $book->getOwner(); ?>
                    <article class="book-card">
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
                            <p class="book-card__condition-badge">
                                <?= htmlspecialchars(StringHelper::bookConditionLabel($book->getCondition())); ?>
                            </p>
                            <h3 class="book-card__title">
                                <?= htmlspecialchars($book->getTitle()); ?></h3>
                            <p class="book-card__author"><?= htmlspecialchars($book->getAuthor()); ?></p>
                            <p class="book-card__seller">
                                <span class="book-card__seller-label">Proposé par :</span>
                                <span class="book-card__seller-name"><?= htmlspecialchars($owner->getUsername()); ?></span>
                            </p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Aucun livre disponible pour le moment. Revenez bientôt !</p>
            <?php endif; ?>
        </div>

        <div class="section__cta">
            <a href="index.php?action=books" class="btn btn--primary">Découvrir tous les livres</a>
        </div>
    </div>
</section>

<section id="how-it-works" class="section">
    <div class="container">
        <h2 class="section__title">Comment ça marche ?</h2>
        <p class="section__subtitle">
            Échanger des livres avec TomTroc c’est simple et amusant ! Suivez ces étapes pour commencer :
        </p>

        <div class="steps-grid">
            <article class="step-card">
                <h3 class="step-card__title">1. Inscrivez-vous</h3>
                <p class="step-card__text">
                    Inscrivez-vous gratuitement sur notre plateforme.
                </p>
            </article>

            <article class="step-card">
                <h3 class="step-card__title">2. Ajoutez vos livres</h3>
                <p class="step-card__text">
                    Ajoutez les livres que vous souhaitez échanger à votre profil.
                </p>
            </article>

            <article class="step-card">
                <h3 class="step-card__title">3. Parcourez les livres</h3>
                <p class="step-card__text">
                    Parcourez les livres disponibles chez d'autres membres.
                </p>
            </article>

            <article class="step-card">
                <h3 class="step-card__title">4. Proposez un échange</h3>
                <p class="step-card__text">
                    Proposez un échange et discutez avec d'autres passionnés de lecture.
                </p>
            </article>
        </div>
    </div>
</section>

<section id="values" class="section section--values">
    <div class="section--values__overlay"></div>
    <div class="container section--values__content">
        <div class="values-intro">
            <h2 class="section__title">Nos valeurs</h2>
            <p>
                Chez Tom Troc, nous mettons l'accent sur le partage, la d&eacute;couverte et la communaut&eacute;. Nos valeurs
                sont ancr&eacute;es dans notre passion pour les livres et notre d&eacute;sir de cr&eacute;er des liens entre les lecteurs.
                Nous croyons en la puissance des histoires pour rassembler les gens et inspirer des conversations
                enrichissantes.
            </p>
            <p>
                Notre association a &eacute;t&eacute; fond&eacute;e avec une conviction profonde&nbsp;: chaque livre m&eacute;rite d'&ecirc;tre lu et
                partag&eacute;. Nous sommes passionn&eacute;s par la cr&eacute;ation d'une plateforme conviviale qui permet aux lecteurs de
                se connecter, de partager leurs d&eacute;couvertes litt&eacute;raires et d'&eacute;changer des livres qui attendent
                patiemment sur les &eacute;tag&egrave;res.
            </p>
        </div>

        <div class="values-grid">
            <article class="value-card">
                <h3>Partage</h3>
                <p>Chaque ouvrage dormant sur une &eacute;tag&egrave;re peut illuminer la journ&eacute;e d'un autre lecteur.</p>
            </article>
            <article class="value-card">
                <h3>Confiance</h3>
                <p>Des fiches compl&egrave;tes et des profils transparents assurent des &eacute;changes sereins.</p>
            </article>
            <article class="value-card">
                <h3>Communaut&eacute;</h3>
                <p>Tom Troc rapproche les passionn&eacute;s, d&eacute;multiplie les discussions et cr&eacute;e de nouvelles complicit&eacute;s.</p>
            </article>
        </div>

        <p class="section__signature">L'&eacute;quipe Tom Troc</p>
    </div>
</section>

