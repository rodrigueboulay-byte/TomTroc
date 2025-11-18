<?php
/**
 * Contrôleur gérant les actions liées au compte utilisateur (CRUD de livres, profil, etc.).
 */
class AccountController
{
    /**
     * Injecte les managers ou crée des instances par défaut.
     */
    public function __construct(
        private ?UserManager $userManager = null,
        private ?BookManager $bookManager = null,
        private ?GenreManager $genreManager = null,
        private ?ExchangeRequestManager $exchangeRequestManager = null
    ) {
        $this->userManager = $this->userManager ?? new UserManager();
        $this->bookManager = $this->bookManager ?? new BookManager();
        $this->genreManager = $this->genreManager ?? new GenreManager();
        $this->exchangeRequestManager = $this->exchangeRequestManager ?? new ExchangeRequestManager();
    }

    /**
     * Affiche la page de compte privée avec les livres et demandes d'échange de l'utilisateur connecté.
     */
    public function account(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $pageTitle = 'TomTroc - Mon compte';
        $userId = (int) $_SESSION['user']['id'];
        $user = $this->userManager->find($userId);

        if ($user === null) {
            throw new RuntimeException('Utilisateur introuvable.');
        }

        $userBooks = $this->bookManager->findByOwner($userId);
        $exchangeRequests = $this->exchangeRequestManager->getRequestsForUser($userId);

        require __DIR__ . '/../views/accountView.php';
    }

    /**
     * Affiche un profil public (données encore statiques pour l'instant).
     */
    public function publicAccount(): void
    {
        $pageTitle = 'TomTroc - Profil public';
        $publicUser = null;

        require __DIR__ . '/../views/publicAccountView.php';
    }

    public function addBook(): void
    {
        $this->createBookForm();
    }

    public function editBook(): void
    {
        $bookId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($bookId <= 0) {
            $this->createBookForm();
            return;
        }

        $this->editBookForm($bookId);
    }

    /**
     * Supprime un livre appartenant à l'utilisateur connecté.
     */
    public function deleteBook(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $bookId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $currentUserId = (int) $_SESSION['user']['id'];

        if ($bookId > 0) {
            $book = $this->bookManager->find($bookId);
            if ($book && $book->getOwner()->getId() === $currentUserId) {
                $this->bookManager->deleteBook($bookId, $currentUserId);
            }
        }

        header('Location: index.php?action=account');
        exit;
    }

    /**
     * Gère l'affichage du formulaire de création de livre.
     */
    private function createBookForm(): void
    {
        $currentUserId = $this->requireAuthenticatedUser();
        $pageTitle = 'TomTroc - Ajouter un livre';

        $bookData = $this->getDefaultBookData();
        $errors = [];
        $book = null;

        $genres = $this->genreManager->findAll();
        $conditions = $this->getBookConditions();

        if ($this->isPostRequest()) {
            $bookData = $this->hydrateBookDataFromRequest($bookData);
            $errors = $this->validateBookData($bookData, $conditions);

            if (empty($errors)) {
                $this->persistBook($bookData, $currentUserId, false);
            }
        }

        require __DIR__ . '/../views/editBookView.php';
    }

    /**
     * Gère l'affichage du formulaire d'édition de livre.
     */
    private function editBookForm(int $bookId): void
    {
        $currentUserId = $this->requireAuthenticatedUser();
        $pageTitle = 'TomTroc - Modifier un livre';

        $book = $this->bookManager->find($bookId);
        if ($book === null || $book->getOwner()->getId() !== $currentUserId) {
            throw new RuntimeException('Livre introuvable ou non autorisé.');
        }

        $bookData = $this->extractBookData($book);
        $errors = [];

        $genres = $this->genreManager->findAll();
        $conditions = $this->getBookConditions();

        if ($this->isPostRequest()) {
            $bookData = $this->hydrateBookDataFromRequest($bookData);
            $errors = $this->validateBookData($bookData, $conditions);

            if (empty($errors)) {
                $this->persistBook($bookData, $currentUserId, true, $bookId);
            }
        }

        require __DIR__ . '/../views/editBookView.php';
    }

    /**
     * Vérifie la présence d'un utilisateur connecté et retourne son identifiant.
     */
    private function requireAuthenticatedUser(): int
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        return (int) $_SESSION['user']['id'];
    }

    /**
     * Retourne les valeurs par défaut du formulaire.
     */
    private function getDefaultBookData(): array
    {
        return [
            'title' => '',
            'author' => '',
            'genre_id' => '',
            'description' => '',
            'condition' => 'bon',
            'is_available' => true,
            'cover_image_path' => '',
        ];
    }

    /**
     * Liste des états possibles d'un livre.
     */
    private function getBookConditions(): array
    {
        return [
            'comme_neuf' => 'Comme neuf',
            'tres_bon' => 'Très bon',
            'bon' => 'Bon',
            'correct' => 'Correct',
            'abime' => 'Abîmé',
        ];
    }

    /**
     * Hydrate les données du livre à partir de la requête POST.
     */
    private function hydrateBookDataFromRequest(array $bookData): array
    {
        $bookData['title'] = trim($_POST['title'] ?? '');
        $bookData['author'] = trim($_POST['author'] ?? '');
        $bookData['genre_id'] = $_POST['genre_id'] !== '' ? (int) $_POST['genre_id'] : '';
        $bookData['description'] = trim($_POST['description'] ?? '');
        $bookData['condition'] = $_POST['condition'] ?? 'bon';
        $bookData['is_available'] = isset($_POST['is_available']);
        $bookData['cover_image_path'] = trim($_POST['cover_url'] ?? $bookData['cover_image_path'] ?? '');

        return $bookData;
    }

    /**
     * Valide les données du formulaire de livre.
     */
    private function validateBookData(array $bookData, array $conditions): array
    {
        $errors = [];
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

        return $errors;
    }

    /**
     * Alimente les données du livre à partir d'une entité Book.
     */
    private function extractBookData(Book $book): array
    {
        return [
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'genre_id' => $book->getGenre()?->getId() ?? '',
            'description' => $book->getDescription() ?? '',
            'condition' => $book->getCondition(),
            'is_available' => $book->isAvailable(),
            'cover_image_path' => $book->getCoverImagePath() ?? '',
        ];
    }

    /**
     * Créé ou met à jour un livre avant redirection vers le compte.
     */
    private function persistBook(array $bookData, int $currentUserId, bool $isEdit, ?int $bookId = null): void
    {
        $coverImagePath = $bookData['cover_image_path'] !== '' ? $bookData['cover_image_path'] : null;

        if ($isEdit && $bookId !== null) {
            $this->bookManager->updateBook(
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
            $this->bookManager->createBook(
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

    private function isPostRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}




