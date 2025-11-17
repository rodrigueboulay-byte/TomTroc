<?php
// views/messagesView.php

$errors = $errors ?? [];
$conversations = $conversations ?? [];
$messages = $messages ?? [];
$selectedPartner = $selectedPartner ?? null;
$messageDraft = $messageDraft ?? '';
$currentUserId = $currentUserId ?? ($_SESSION['user']['id'] ?? null);
$currentUserBooks = $currentUserBooks ?? [];
$requestedBookContext = $requestedBookContext ?? null;
$selectedOfferedBookId = $selectedOfferedBookId ?? null;

require __DIR__ . '/templates/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="section__title">Messagerie</h1>
        <p class="section__subtitle">
            Retrouvez ici vos &eacute;changes avec les autres membres.
        </p>

        <div class="messages">
            <div class="messages__list">
                <h2>Conversations</h2>

                <?php if (empty($conversations)) : ?>
                    <p>Pas encore de messages. D&eacute;marrez un &eacute;change depuis la fiche d'un livre.</p>
                <?php else : ?>
                    <ul>
                        <?php foreach ($conversations as $conversation) :
                            $partner = $conversation['partner'];
                            $lastMessage = $conversation['lastMessage'];
                            $isActive = $selectedPartner && $partner->getId() === $selectedPartner->getId();
                            $cover = $conversation['cover'] ?? null;
                            ?>
                            <li class="<?= $isActive ? 'messages__conversation--active' : ''; ?>">
                                <a href="index.php?action=messages&partner=<?= $partner->getId(); ?>">
                                    <div class="messages__conversation-entry">
                                        <div class="messages__conversation-cover">
                                            <?php if ($cover) : ?>
                                                <img src="<?= htmlspecialchars($cover); ?>" alt="Livre concerné">
                                            <?php else : ?>
                                                <span class="book-card__cover-placeholder">?</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="messages__conversation-text">
                                            <div class="messages__conversation-title">
                                                <span><?= htmlspecialchars($partner->getUsername()); ?></span>
                                                <time><?= $lastMessage->getCreatedAt()->format('d/m H:i'); ?></time>
                                            </div>
                                            <p class="messages__conversation-preview">
                                                <?= htmlspecialchars(mb_strimwidth($lastMessage->getContent(), 0, 80, '...')); ?>
                                            </p>
                                        </div>
                                        <?php if ($conversation['unread'] > 0) : ?>
                                            <span class="badge badge--primary"><?= $conversation['unread']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="messages__thread">
                <h2>Discussion</h2>

                <?php if (!empty($errors)) : ?>
                    <div class="alert alert--error">
                        <ul>
                            <?php foreach ($errors as $error) : ?>
                                <li><?= htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($selectedPartner) : ?>
                    <div class="messages__thread-header">
                        <h3><?= htmlspecialchars($selectedPartner->getUsername()); ?></h3>
                        <?php if ($selectedPartner->getCity()) : ?>
                            <p><?= htmlspecialchars($selectedPartner->getCity()); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if ($requestedBookContext) : ?>
                        <div class="messages__exchange-context">
                            <p>
                                Vous proposez un &eacute;change pour
                                <strong><?= htmlspecialchars($requestedBookContext->getTitle()); ?></strong>
                                de <?= htmlspecialchars($requestedBookContext->getOwner()->getUsername()); ?>.
                            </p>
                            <div class="messages__book-cover">
                                <?php if ($requestedBookContext->getCoverImagePath()) : ?>
                                    <img src="<?= htmlspecialchars($requestedBookContext->getCoverImagePath()); ?>" alt="Couverture de <?= htmlspecialchars($requestedBookContext->getTitle()); ?>">
                                <?php endif; ?>
                            </div>
                            <?php if ($selectedOfferedBookId) :
                                $offered = null;
                                foreach ($currentUserBooks as $bookOption) {
                                    if ($bookOption->getId() === $selectedOfferedBookId) {
                                        $offered = $bookOption;
                                        break;
                                    }
                                }
                                ?>
                                <?php if ($offered) : ?>
                                    <p>Vous offrez automatiquement : <strong><?= htmlspecialchars($offered->getTitle()); ?></strong></p>
                                    <div class="messages__book-cover">
                                        <?php if ($offered->getCoverImagePath()) : ?>
                                            <img src="<?= htmlspecialchars($offered->getCoverImagePath()); ?>" alt="Couverture de <?= htmlspecialchars($offered->getTitle()); ?>">
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="messages__thread-body">
                        <?php if (empty($messages)) : ?>
                            <p>Aucun message pour le moment. Soyez le premier &agrave; &eacute;crire !</p>
                        <?php else : ?>
                            <?php foreach ($messages as $message) :
                                $isMine = $message->getSender()->getId() === $currentUserId;
                                ?>
                                <div class="messages__bubble <?= $isMine ? 'messages__bubble--me' : 'messages__bubble--other'; ?>">
                                    <div class="messages__bubble-text">
                                        <?= nl2br(htmlspecialchars($message->getContent())); ?>
                                    </div>
                                    <span class="messages__bubble-date">
                                        <?= $message->getCreatedAt()->format('d/m/Y H:i'); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php
                    $formAction = 'index.php?action=messages&partner=' . $selectedPartner->getId();
                    if ($requestedBookContext) {
                        $formAction .= '&requested_book_id=' . $requestedBookContext->getId();
                    }
                    if ($selectedOfferedBookId) {
                        $formAction .= '&offered_book_id=' . $selectedOfferedBookId;
                    }
                    $needsBook = $requestedBookContext !== null;
                    $hasBooks = !empty($currentUserBooks);
                    ?>

                    <form class="messages__form" method="post" action="<?= $formAction; ?>">
                        <input type="hidden" name="partner_id" value="<?= $selectedPartner->getId(); ?>">
                        <?php if ($requestedBookContext) : ?>
                            <input type="hidden" name="requested_book_id" value="<?= $requestedBookContext->getId(); ?>">
                        <?php endif; ?>

                        <?php if ($needsBook && !$hasBooks) : ?>
                            <p>Ajoutez un livre sur votre profil avant d'envoyer une demande d'&eacute;change.</p>
                        <?php endif; ?>

                        <textarea rows="3" name="content" placeholder="&Eacute;crire un message..." required><?= htmlspecialchars($messageDraft); ?></textarea>
                        <button type="submit" class="btn btn--primary" <?= ($needsBook && !$hasBooks) ? 'disabled' : ''; ?>>
                            Envoyer
                        </button>
                    </form>
                <?php else : ?>
                    <p class="messages__thread-empty">S&eacute;lectionnez une conversation pour commencer &agrave; discuter.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
require __DIR__ . '/templates/footer.php';
