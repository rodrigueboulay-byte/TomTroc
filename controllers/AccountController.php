<?php
// controllers/AccountController.php

class AccountController
{
    public function __construct(
        private ?UserManager $UserManager = null,
        private ?BookManager $BookManager = null,
        private ?GenreManager $GenreManager = null,
        private ?ExchangeRequestManager $ExchangeRequestManager = null
    ) {
        $this->UserManager = $this->UserManager ?? new UserManager();
        $this->BookManager = $this->BookManager ?? new BookManager();
        $this->GenreManager = $this->GenreManager ?? new GenreManager();
        $this->ExchangeRequestManager = $this->ExchangeRequestManager ?? new ExchangeRequestManager();
    }

    public function account(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $pageTitle = 'TomTroc - Mon compte';
        $userId = (int) $_SESSION['user']['id'];
        $user = $this->UserManager->find($userId);

        if ($user === null) {
            throw new RuntimeException('Utilisateur introuvable.');
        }

        $userBooks = $this->BookManager->findByOwner($userId);
        $exchangeRequests = $this->ExchangeRequestManager->getRequestsForUser($userId);

        require __DIR__ . '/../views/accountView.php';
    }

    public function publicAccount(): void
    {
        $pageTitle = 'TomTroc - Profil public';
        $publicUser = null;

        require __DIR__ . '/../views/publicAccountView.php';
    }

    public function addBook(): void
    {
        $this->handleBookForm();
    }

    public function editBook(): void
    {
        $bookId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $this->handleBookForm($bookId);
    }

    public function deleteBook(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $bookId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $currentUserId = (int) $_SESSION['user']['id'];

        if ($bookId > 0) {
            $book = $this->BookManager->find($bookId);
            if ($book && $book->getOwner()->getId() === $currentUserId) {
                $this->BookManager->deleteBook($bookId, $currentUserId);
            }
        }

        header('Location: index.php?action=account');
        exit;
    }

    private function handleBookForm(?int $bookId = null): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $isEdit = $bookId && $bookId > 0;
        $pageTitle = $isEdit ? 'TomTroc - Modifier un livre' : 'TomTroc - Ajouter un livre';

        $currentUserId = (int) $_SESSION['user']['id'];
        $bookData = [
            'title' => '',
            'author' => '',
            'genre_id' => '',
            'description' => '',
            'condition' => 'bon',
            'is_available' => true,
            'cover_image_path' => '',
        ];
        $errors = [];

        if ($isEdit) {
            $book = $this->BookManager->find($bookId);
            if ($book === null || $book->getOwner()->getId() !== $currentUserId) {
                throw new RuntimeException('Livre introuvable ou non autorisé.');
            }

            $bookData = [
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'genre_id' => $book->getGenre()?->getId() ?? '',
                'description' => $book->getDescription() ?? '',
                'condition' => $book->getCondition(),
                'is_available' => $book->isAvailable(),
                'cover_image_path' => $book->getCoverImagePath() ?? '',
            ];
        }

        $genres = $this->GenreManager->findAll();
        $conditions = [
            'comme_neuf' => 'Comme neuf',
            'tres_bon' => 'Très bon',
            'bon' => 'Bon',
            'correct' => 'Correct',
            'abime' => 'Abîmé',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookData['title'] = trim($_POST['title'] ?? '');
            $bookData['author'] = trim($_POST['author'] ?? '');
            $bookData['genre_id'] = $_POST['genre_id'] !== '' ? (int) $_POST['genre_id'] : '';
            $bookData['description'] = trim($_POST['description'] ?? '');
            $bookData['condition'] = $_POST['condition'] ?? 'bon';
            $bookData['is_available'] = isset($_POST['is_available']);
            $bookData['cover_image_path'] = trim($_POST['cover_url'] ?? $bookData['cover_image_path']);
            $coverImagePath = $bookData['cover_image_path'] !== '' ? $bookData['cover_image_path'] : null;

            if ($bookData['title'] === '') {
                $errors[] = 'Le titre est obligatoire.';
            }

            if ($bookData['author'] === '') {
                $errors[] = 'L\'auteur est obligatoire.';
            }

            if (!array_key_exists($bookData['condition'], $conditions)) {
                $errors[] = 'L\'état sélectionné est invalide.';
            }

            if ($coverImagePath && !filter_var($coverImagePath, FILTER_VALIDATE_URL)) {
                $errors[] = 'Merci de fournir une URL valide pour l\'image.';
            }

            if (empty($errors)) {
                if ($isEdit) {
                    $this->BookManager->updateBook(
                        $bookId,
                        $currentUserId,
                        $bookData['title'],
                        $bookData['author'],
                        $bookData['genre_id'] ?: null,
                        $bookData['description'] ?: null,
                        $bookData['condition'],
                        $bookData['is_available'],
                        $coverImagePath
                    );
                } else {
                    $this->BookManager->createBook(
                        $currentUserId,
                        $bookData['title'],
                        $bookData['author'],
                        $bookData['genre_id'] ?: null,
                        $bookData['description'] ?: null,
                        $bookData['condition'],
                        $bookData['is_available'],
                        $coverImagePath
                    );
                }

                header('Location: index.php?action=account');
                exit;
            }
        }

        require __DIR__ . '/../views/editBookView.php';
    }
}




