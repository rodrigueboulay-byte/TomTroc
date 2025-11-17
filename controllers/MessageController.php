<?php
// controllers/MessageController.php

class MessageController
{
    private MessageRepository $messageRepository;
    private UserRepository $userRepository;
    private BookRepository $bookRepository;
    private ExchangeRequestRepository $exchangeRequestRepository;

    public function __construct(
        ?MessageRepository $messageRepository = null,
        ?UserRepository $userRepository = null,
        ?BookRepository $bookRepository = null,
        ?ExchangeRequestRepository $exchangeRequestRepository = null
    ) {
        $this->messageRepository = $messageRepository ?? new MessageRepository();
        $this->userRepository = $userRepository ?? new UserRepository();
        $this->bookRepository = $bookRepository ?? new BookRepository();
        $this->exchangeRequestRepository = $exchangeRequestRepository ?? new ExchangeRequestRepository();
    }

    public function inbox(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $currentUserId = (int) $_SESSION['user']['id'];
        $pageTitle = 'TomTroc - Messagerie';
        $errors = [];
        $messageDraft = '';
        $selectedOfferedBookId = null;

        $partnerId = isset($_GET['partner']) ? (int) $_GET['partner'] : null;
        $requestedBookId = isset($_GET['requested_book_id']) ? (int) $_GET['requested_book_id'] : 0;
        $selectedOfferedBookId = isset($_GET['offered_book_id']) ? (int) $_GET['offered_book_id'] : null;
        $selectedPartner = null;
        $requestedBookContext = null;
        $messages = [];

        $currentUserBooks = $this->bookRepository->findByOwner($currentUserId);
        if ($selectedOfferedBookId === null && !empty($currentUserBooks)) {
            $selectedOfferedBookId = $currentUserBooks[0]->getId();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $partnerId = (int) ($_POST['partner_id'] ?? 0);
            $requestedBookId = (int) ($_POST['requested_book_id'] ?? 0);
            $messageDraft = trim($_POST['content'] ?? '');

            if ($partnerId <= 0) {
                $errors[] = 'Conversation invalide.';
            } elseif ($partnerId === $currentUserId) {
                $errors[] = 'Vous ne pouvez pas vous envoyer de message.';
            } else {
                $selectedPartner = $this->userRepository->find($partnerId);

                if ($selectedPartner === null) {
                    $errors[] = 'Utilisateur introuvable.';
                } elseif ($messageDraft === '') {
                    $errors[] = 'Veuillez écrire un message.';
                } else {
                    $exchangeRequestId = null;

                    if ($requestedBookId > 0) {
                        $requestedBookContext = $this->bookRepository->find($requestedBookId);
                        if ($requestedBookContext === null || $requestedBookContext->getOwner()->getId() !== $partnerId) {
                            $errors[] = 'Livre demandé invalide.';
                        } elseif (empty($currentUserBooks)) {
                            $errors[] = 'Ajoutez au moins un livre avant de proposer un échange.';
                        } else {
                            if ($selectedOfferedBookId === null) {
                                $selectedOfferedBookId = $currentUserBooks[0]->getId();
                            }
                            $offeredBook = $this->bookRepository->find($selectedOfferedBookId);
                            if ($offeredBook === null || $offeredBook->getOwner()->getId() !== $currentUserId) {
                                $errors[] = 'Le livre proposé est invalide.';
                            } else {
                                $exchangeRequestId = $this->exchangeRequestRepository->create(
                                    $currentUserId,
                                    $partnerId,
                                    $selectedOfferedBookId,
                                    $requestedBookId
                                );
                            }
                        }
                    }

                    if (empty($errors)) {
                        $this->messageRepository->sendMessage(
                            $currentUserId,
                            $partnerId,
                            $messageDraft,
                            $exchangeRequestId
                        );

                        $redirect = 'index.php?action=messages&partner=' . $partnerId;
                        if ($requestedBookId > 0) {
                            $redirect .= '&requested_book_id=' . $requestedBookId;
                        }
                        if ($selectedOfferedBookId) {
                            $redirect .= '&offered_book_id=' . $selectedOfferedBookId;
                        }
                        header('Location: ' . $redirect);
                        exit;
                    }
                }
            }
        }

        $conversations = $this->messageRepository->getConversationsForUser($currentUserId);

        if ($partnerId) {
            if ($selectedPartner === null) {
                $selectedPartner = $this->userRepository->find($partnerId);
            }

            if ($selectedPartner !== null) {
                if ($requestedBookId > 0 && $requestedBookContext === null) {
                    $candidateBook = $this->bookRepository->find($requestedBookId);
                    if ($candidateBook && $candidateBook->getOwner()->getId() === $partnerId) {
                        $requestedBookContext = $candidateBook;
                        $pageTitle = 'Échange - ' . $candidateBook->getTitle();
                    }
                }

                if ($requestedBookContext && $selectedOfferedBookId === null && !empty($currentUserBooks)) {
                    $selectedOfferedBookId = $currentUserBooks[0]->getId();
                }

                $messages = $this->messageRepository->getConversationMessages($currentUserId, $partnerId);
                $this->messageRepository->markConversationAsRead($currentUserId, $partnerId);
            } else {
                $errors[] = 'Conversation introuvable.';
            }
        }

        require __DIR__ . '/../views/messagesView.php';
    }
}
