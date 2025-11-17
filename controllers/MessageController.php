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
        if ($selectedOfferedBookId !== null && $selectedOfferedBookId <= 0) {
            $selectedOfferedBookId = null;
        }
        $selectedExchangeId = isset($_GET['exchange_id']) ? (int) $_GET['exchange_id'] : null;
        if ($selectedExchangeId !== null && $selectedExchangeId <= 0) {
            $selectedExchangeId = null;
        }

        $selectedPartner = null;
        $requestedBookContext = null;
        $messages = [];
        $activeExchangeSummary = null;
        $currentUserBooks = $this->bookRepository->findByOwner($currentUserId);

        if ($selectedExchangeId !== null) {
            $activeExchangeSummary = $this->exchangeRequestRepository->findSummary($selectedExchangeId);
            if (
                $activeExchangeSummary === null
                || (
                    (int) $activeExchangeSummary['requester_id'] !== $currentUserId
                    && (int) $activeExchangeSummary['requested_id'] !== $currentUserId
                )
            ) {
                $activeExchangeSummary = null;
                $selectedExchangeId = null;
            } else {
                $partnerId = $currentUserId === (int) $activeExchangeSummary['requester_id']
                    ? (int) $activeExchangeSummary['requested_id']
                    : (int) $activeExchangeSummary['requester_id'];
                $requestedBookId = (int) $activeExchangeSummary['requested_book_id'];
                if ($currentUserId === (int) $activeExchangeSummary['requester_id']) {
                    $selectedOfferedBookId = (int) $activeExchangeSummary['offered_book_id'];
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $partnerId = (int) ($_POST['partner_id'] ?? 0);
            $requestedBookId = (int) ($_POST['requested_book_id'] ?? $requestedBookId);
            $selectedExchangeId = isset($_POST['exchange_id']) ? (int) $_POST['exchange_id'] : $selectedExchangeId;
            if ($selectedExchangeId !== null && $selectedExchangeId <= 0) {
                $selectedExchangeId = null;
            }
            $selectedOfferedBookId = isset($_POST['offered_book_id']) ? (int) $_POST['offered_book_id'] : $selectedOfferedBookId;
            if ($selectedOfferedBookId !== null && $selectedOfferedBookId <= 0) {
                $selectedOfferedBookId = null;
            }
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
                    $errors[] = 'Veuillez ecrire un message.';
                } else {
                    $exchangeRequestId = $selectedExchangeId;
                    if ($exchangeRequestId !== null) {
                        $exchangeSummary = $this->exchangeRequestRepository->findSummary($exchangeRequestId);
                        if (
                            $exchangeSummary === null
                            || (
                                (int) $exchangeSummary['requester_id'] !== $currentUserId
                                && (int) $exchangeSummary['requested_id'] !== $currentUserId
                            )
                        ) {
                            $errors[] = 'Demande d\'echange invalide.';
                            $exchangeRequestId = null;
                            $selectedExchangeId = null;
                        } else {
                            $partnerId = $currentUserId === (int) $exchangeSummary['requester_id']
                                ? (int) $exchangeSummary['requested_id']
                                : (int) $exchangeSummary['requester_id'];
                            $requestedBookId = (int) $exchangeSummary['requested_book_id'];
                            if ($currentUserId === (int) $exchangeSummary['requester_id']) {
                                $selectedOfferedBookId = (int) $exchangeSummary['offered_book_id'];
                            }
                            $activeExchangeSummary = $exchangeSummary;
                        }
                    }

                    if ($requestedBookId > 0) {
                        if ($exchangeRequestId === null) {
                            $requestedBookContext = $this->bookRepository->find($requestedBookId);
                            if ($requestedBookContext === null || $requestedBookContext->getOwner()->getId() !== $partnerId) {
                                $errors[] = 'Livre demande invalide.';
                            } elseif (empty($currentUserBooks)) {
                                $errors[] = 'Ajoutez au moins un livre avant de proposer un echange.';
                            } elseif ($selectedOfferedBookId === null) {
                                $errors[] = 'Veuillez selectionner le livre que vous proposez en echange.';
                            } else {
                                $offeredBook = $this->bookRepository->find($selectedOfferedBookId);
                                if ($offeredBook === null || $offeredBook->getOwner()->getId() !== $currentUserId) {
                                    $errors[] = 'Le livre propose est invalide.';
                                } else {
                                    $exchangeRequestId = $this->exchangeRequestRepository->create(
                                        $currentUserId,
                                        $partnerId,
                                        $selectedOfferedBookId,
                                        $requestedBookId
                                    );
                                    $selectedExchangeId = $exchangeRequestId;
                                    $activeExchangeSummary = $this->exchangeRequestRepository->findSummary($exchangeRequestId);
                                }
                            }
                        } elseif ($requestedBookContext === null) {
                            $requestedBookContext = $this->bookRepository->find($requestedBookId);
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
                        if ($exchangeRequestId) {
                            $redirect .= '&exchange_id=' . $exchangeRequestId;
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
                        $pageTitle = 'Echange - ' . $candidateBook->getTitle();
                    }
                }

                $messages = $this->messageRepository->getConversationMessages($currentUserId, $partnerId, $selectedExchangeId);
                if ($activeExchangeSummary === null && !empty($messages)) {
                    for ($index = count($messages) - 1; $index >= 0; $index--) {
                        $exchangeId = $messages[$index]->getExchangeId();
                        if ($exchangeId) {
                            $activeExchangeSummary = $this->exchangeRequestRepository->findSummary($exchangeId);
                            if ($activeExchangeSummary !== null) {
                                break;
                            }
                        }
                    }
                }
                $this->messageRepository->markConversationAsRead($currentUserId, $partnerId, $selectedExchangeId);
            } else {
                $errors[] = 'Conversation introuvable.';
            }
        }

        require __DIR__ . '/../views/messagesView.php';
    }
}
