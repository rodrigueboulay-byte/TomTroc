<?php
// views/messagesView.php
require __DIR__ . '/templates/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="section__title">Messagerie</h1>
        <p class="section__subtitle">
            Retrouvez ici vos échanges avec les autres membres.
        </p>

        <div class="messages">
            <div class="messages__list">
                <h2>Conversations</h2>
                <ul>
                    <li><a href="#">Conversation avec TomReader</a></li>
                    <li><a href="#">Conversation avec Liseuse93</a></li>
                </ul>
            </div>

            <div class="messages__thread">
                <h2>Discussion sélectionnée</h2>
                <div class="messages__bubble messages__bubble--other">
                    Bonjour, ce livre est-il toujours disponible ?
                </div>
                <div class="messages__bubble messages__bubble--me">
                    Oui, toujours :) On peut organiser un échange !
                </div>

                <form class="messages__form">
                    <textarea rows="3" placeholder="Écrire un message..."></textarea>
                    <button type="submit" class="btn btn--primary">Envoyer</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
require __DIR__ . '/templates/footer.php';
