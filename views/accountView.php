<?php
// views/accountView.php

$user = $user ?? null;
$userBooks = $userBooks ?? [];
$exchangeRequests = $exchangeRequests ?? [];
?>

<section class="section">
    <div class="container">
        <h1 class="section__title">Mon compte</h1>
        <p class="section__subtitle">G&eacute;rez vos informations et vos livres.</p>

        <div class="account">
            <div class="account__panel">
                <h2>Mes informations</h2>
                <p>Pseudo : <strong><?= htmlspecialchars($user?->getUsername() ?? ''); ?></strong></p>
                <p>Email : <?= htmlspecialchars($user?->getEmail() ?? ''); ?></p>
                <?php if ($user && $user->getCity()) : ?>
                    <p>Ville : <?= htmlspecialchars($user->getCity()); ?></p>
                <?php endif; ?>
                <?php if ($user && $user->getBio()) : ?>
                    <p>Bio :</p>
                    <p><?= nl2br(htmlspecialchars($user->getBio())); ?></p>
                <?php endif; ?>
            </div>

            <div class="account__panel">
                <div class="account__panel-header">
                    <h2>Mes livres</h2>
                    <a class="btn btn--primary" href="index.php?action=add-book">Ajouter un livre</a>
                </div>

                <?php if (empty($userBooks)) : ?>
                    <p>Vous n'avez pas encore ajout&eacute; de livres.</p>
                <?php else : ?>
                    <ul class="account__books">
                        <?php foreach ($userBooks as $bookItem) : ?>
                            <li class="account__book">
                                <div class="account__book-info">
                                    <div class="account__book-cover">
                                        <?php if ($bookItem->getCoverImagePath()) : ?>
                                            <img src="<?= htmlspecialchars($bookItem->getCoverImagePath()); ?>" alt="Couverture de <?= htmlspecialchars($bookItem->getTitle()); ?>">
                                        <?php else : ?>
                                            <span class="book-card__cover-placeholder">Pas d'image</span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($bookItem->getTitle()); ?></strong>
                                        <span> - <?= htmlspecialchars($bookItem->getAuthor()); ?></span>
                                        <?php if ($bookItem->getGenre()) : ?>
                                            <span>(<?= htmlspecialchars($bookItem->getGenre()->getName()); ?>)</span>
                                        <?php endif; ?>
                                        <p class="account__book-meta">
                                            Etat :
                                            <span class="book-condition-badge">
                                                <?= htmlspecialchars(StringHelper::bookConditionLabel($bookItem->getCondition())); ?>
                                            </span>
                                            -
                                            <?= $bookItem->isAvailable() ? 'Disponible' : 'Indisponible'; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="account__book-actions">
                                    <a class="btn btn--outline" href="index.php?action=edit-book&id=<?= $bookItem->getId(); ?>">
                                        Modifier
                                    </a>
                                    <form method="post" action="index.php?action=delete-book" onsubmit="return confirm('Supprimer ce livre ?');">
                                        <input type="hidden" name="id" value="<?= $bookItem->getId(); ?>">
                                        <button type="submit" class="btn btn--danger">Supprimer</button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="account__panel">
                <h2>Mes &eacute;changes</h2>
                <?php
                $outgoing = array_filter($exchangeRequests, fn ($req) => $req['type'] === 'outgoing');
                $incoming = array_filter($exchangeRequests, fn ($req) => $req['type'] === 'incoming');
                ?>

                <?php if (empty($exchangeRequests)) : ?>
                    <p>Aucun &eacute;change en cours.</p>
                <?php else : ?>
                    <div class="account__exchanges">
                        <?php if (!empty($outgoing)) : ?>
                            <div>
                                <h3>Mes demandes</h3>
                                <ul>
                                    <?php foreach ($outgoing as $req) : ?>
                                        <li>
                                            Vous offrez <strong><?= htmlspecialchars($req['offered_book_title']); ?></strong>
                                            pour <strong><?= htmlspecialchars($req['requested_book_title']); ?></strong>
                                            auprès de <?= htmlspecialchars($req['other_user']); ?>
                                            (<?= htmlspecialchars($req['status']); ?>)
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($incoming)) : ?>
                            <div>
                                <h3>Demandes reçues</h3>
                                <ul>
                                    <?php foreach ($incoming as $req) : ?>
                                        <li>
                                            <strong><?= htmlspecialchars($req['other_user']); ?></strong>
                                            propose <strong><?= htmlspecialchars($req['offered_book_title']); ?></strong>
                                            pour votre livre <strong><?= htmlspecialchars($req['requested_book_title']); ?></strong>
                                            (<?= htmlspecialchars($req['status']); ?>)
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
