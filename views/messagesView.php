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
$activeExchangeSummary = $activeExchangeSummary ?? null;
$selectedOfferedBook = null;
if ($selectedOfferedBookId) {
    foreach ($currentUserBooks as $bookOption) {
        if ($bookOption->getId() === $selectedOfferedBookId) {
            $selectedOfferedBook = $bookOption;
            break;
        }
    }
}
$offeredBookPlaceholder = 'Choisissez un livre dans la liste ci-dessous pour finaliser votre demande.';
$selectedExchangeId = $selectedExchangeId ?? null;

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
                            $cover = $conversation['cover'] ?? null;
                            $conversationExchangeId = $conversation['exchange_id'] ?? null;
                            $conversationBookId = $conversation['requested_book_id'] ?? null;
                            $conversationLink = 'index.php?action=messages&partner=' . $partner->getId();
                            if ($conversationBookId) {
                                $conversationLink .= '&requested_book_id=' . $conversationBookId;
                            }
                            if ($conversationExchangeId) {
                                $conversationLink .= '&exchange_id=' . $conversationExchangeId;
                            }
                            $isActive = false;
                            if ($selectedPartner && $partner->getId() === $selectedPartner->getId()) {
                                if ($conversationExchangeId === null && $selectedExchangeId === null) {
                                    $isActive = true;
                                } elseif ($conversationExchangeId !== null && $conversationExchangeId === $selectedExchangeId) {
                                    $isActive = true;
                                }
                            }
                            ?>
                            <li class="<?= $isActive ? 'messages__conversation--active' : ''; ?>">
                                <a href="<?= $conversationLink; ?>">
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
                                            <?php if (!empty($conversation['requested_book_title'])) : ?>
                                                <p class="messages__conversation-tag">
                                                    <?= htmlspecialchars($conversation['requested_book_title']); ?>
                                                </p>
                                            <?php endif; ?>
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

                    <?php if ($requestedBookContext || $activeExchangeSummary) : ?>
                        <div class="messages__exchange-context">
                            <?php if ($requestedBookContext) : ?>
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
                                <div id="offered-book-preview" class="messages__selected-offer" data-placeholder="<?= htmlspecialchars($offeredBookPlaceholder); ?>">
                                    <?php if ($selectedOfferedBook) : ?>
                                        <p>Actuellement s&eacute;lectionn&eacute; : <strong><?= htmlspecialchars($selectedOfferedBook->getTitle()); ?></strong></p>
                                        <div class="messages__book-cover">
                                            <?php if ($selectedOfferedBook->getCoverImagePath()) : ?>
                                                <img src="<?= htmlspecialchars($selectedOfferedBook->getCoverImagePath()); ?>" alt="Couverture de <?= htmlspecialchars($selectedOfferedBook->getTitle()); ?>">
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif ($activeExchangeSummary && $currentUserId && (int) $activeExchangeSummary['requester_id'] === (int) $currentUserId) : ?>
                                        <p>Actuellement s&eacute;lectionn&eacute; : <strong><?= htmlspecialchars($activeExchangeSummary['offered_book_title']); ?></strong></p>
                                        <?php if (!empty($activeExchangeSummary['offered_book_cover'])) : ?>
                                            <div class="messages__book-cover">
                                                <img src="<?= htmlspecialchars($activeExchangeSummary['offered_book_cover']); ?>" alt="Couverture de <?= htmlspecialchars($activeExchangeSummary['offered_book_title']); ?>">
                                            </div>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <p class="messages__selected-offer-placeholder"><?= htmlspecialchars($offeredBookPlaceholder); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($activeExchangeSummary) :
                                $isRequester = $currentUserId && ((int) $currentUserId === $activeExchangeSummary['requester_id']);
                                ?>
                                <p>R&eacute;sum&eacute; du dernier &eacute;change :</p>
                                <p>
                                    <strong><?= htmlspecialchars($activeExchangeSummary['offered_book_title']); ?></strong>
                                    contre
                                    <strong><?= htmlspecialchars($activeExchangeSummary['requested_book_title']); ?></strong>
                                </p>
                                <p>
                                    <?= $isRequester
                                        ? 'Vous avez propos&eacute; cet &eacute;change.'
                                        : htmlspecialchars($activeExchangeSummary['requester_name']) . ' vous a propos&eacute; cet &eacute;change.'; ?>
                                </p>
                                <ul>
                                    <li>
                                        <?= $isRequester ? 'Vous offrez' : htmlspecialchars($activeExchangeSummary['requester_name']) . ' offre'; ?>
                                        :
                                        <strong><?= htmlspecialchars($activeExchangeSummary['offered_book_title']); ?></strong>
                                    </li>
                                    <li>
                                        <?= $isRequester ? 'Vous demandez' : 'On vous demande'; ?>
                                        :
                                        <strong><?= htmlspecialchars($activeExchangeSummary['requested_book_title']); ?></strong>
                                    </li>
                                </ul>
                                <div class="messages__exchange-covers">
                                    <?php if (!empty($activeExchangeSummary['offered_book_cover'])) : ?>
                                        <div class="messages__book-cover">
                                            <img src="<?= htmlspecialchars($activeExchangeSummary['offered_book_cover']); ?>" alt="Couverture de <?= htmlspecialchars($activeExchangeSummary['offered_book_title']); ?>">
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($activeExchangeSummary['requested_book_cover'])) : ?>
                                        <div class="messages__book-cover">
                                            <img src="<?= htmlspecialchars($activeExchangeSummary['requested_book_cover']); ?>" alt="Couverture de <?= htmlspecialchars($activeExchangeSummary['requested_book_title']); ?>">
                                        </div>
                                    <?php endif; ?>
                                </div>
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
                    if ($selectedExchangeId) {
                        $formAction .= '&exchange_id=' . $selectedExchangeId;
                    }
                    $needsBook = $requestedBookContext !== null && $selectedExchangeId === null;
                    $hasBooks = !empty($currentUserBooks);
                    $allowOfferedSelection = $needsBook && $hasBooks && $selectedOfferedBookId === null;
                    ?>

                    <form class="messages__form" method="post" action="<?= $formAction; ?>">
                        <input type="hidden" name="partner_id" value="<?= $selectedPartner->getId(); ?>">
                        <?php if ($requestedBookContext) : ?>
                            <input type="hidden" name="requested_book_id" value="<?= $requestedBookContext->getId(); ?>">
                        <?php endif; ?>
                        <?php if ($selectedExchangeId) : ?>
                            <input type="hidden" name="exchange_id" value="<?= $selectedExchangeId; ?>">
                        <?php endif; ?>

                        <?php if ($needsBook && !$hasBooks) : ?>
                            <p>Ajoutez un livre sur votre profil avant d'envoyer une demande d'&eacute;change.</p>
                        <?php elseif ($allowOfferedSelection) : ?>
                            <label for="offered_book_id" class="messages__form-label">
                                Choisissez le livre que vous souhaitez proposer en &eacute;change
                            </label>
                            <select name="offered_book_id" id="offered_book_id" class="messages__form-select" required>
                                <option value="" disabled <?= $selectedOfferedBookId ? '' : 'selected'; ?>>-- Faites votre choix --</option>
                                <?php foreach ($currentUserBooks as $bookOption) : ?>
                                    <option
                                        value="<?= $bookOption->getId(); ?>"
                                        data-title="<?= htmlspecialchars($bookOption->getTitle()); ?>"
                                        data-author="<?= htmlspecialchars($bookOption->getAuthor()); ?>"
                                        data-cover="<?= htmlspecialchars($bookOption->getCoverImagePath() ?? ''); ?>"
                                        <?= $bookOption->getId() === $selectedOfferedBookId ? 'selected' : ''; ?>
                                    >
                                        <?= htmlspecialchars($bookOption->getTitle()); ?> - <?= htmlspecialchars($bookOption->getAuthor()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
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
?>

<?php if (!empty($currentUserBooks)) : ?>
    <script>
        (function () {
            const select = document.getElementById('offered_book_id');
            const preview = document.getElementById('offered-book-preview');
            if (!select || !preview) {
                return;
            }

            const placeholderText = preview.dataset.placeholder || 'Choisissez un livre a proposer.';

            function renderPlaceholder() {
                preview.innerHTML = '';
                const paragraph = document.createElement('p');
                paragraph.className = 'messages__selected-offer-placeholder';
                paragraph.textContent = placeholderText;
                preview.appendChild(paragraph);
            }

            function renderSelection(option) {
                preview.innerHTML = '';
                const title = option.dataset.title || '';
                const author = option.dataset.author || '';
                const cover = option.dataset.cover || '';

                const paragraph = document.createElement('p');
                paragraph.textContent = 'Actuellement selectionne : ';
                const strong = document.createElement('strong');
                strong.textContent = title;
                paragraph.appendChild(strong);
                if (author) {
                    const authorSpan = document.createElement('span');
                    authorSpan.textContent = ' - ' + author;
                    paragraph.appendChild(authorSpan);
                }
                preview.appendChild(paragraph);

                if (cover) {
                    const coverWrapper = document.createElement('div');
                    coverWrapper.className = 'messages__book-cover';
                    const img = document.createElement('img');
                    img.src = cover;
                    img.alt = 'Couverture de ' + title;
                    coverWrapper.appendChild(img);
                    preview.appendChild(coverWrapper);
                }
            }

            function updatePreview() {
                const option = select.options[select.selectedIndex];
                if (!option || !option.dataset || !option.dataset.title) {
                    renderPlaceholder();
                    return;
                }
                renderSelection(option);
            }

            select.addEventListener('change', updatePreview);
            updatePreview();
        })();
    </script>
<?php endif; ?>
