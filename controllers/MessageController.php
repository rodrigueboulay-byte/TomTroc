<?php
// controllers/MessageController.php

class MessageController
{
    public function inbox(): void
    {
        $pageTitle = 'TomTroc – Messagerie';
        // plus tard : récupérer les conversations/messages de l’utilisateur
        $conversations = [];

        require __DIR__ . '/../views/messagesView.php';
    }
}
