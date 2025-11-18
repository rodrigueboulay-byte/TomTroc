<?php
// controllers/AuthController.php

class AuthController
{
    private UserManager $userManager;

    public function __construct(?UserManager $userManager = null)
    {
        $this->userManager = $userManager ?? new UserManager();
    }

    public function register(): void
    {
        $pageTitle = 'TomTroc - Inscription';
        $errors = [];
        $old = [
            'username' => '',
            'email' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';

            $old['username'] = $username;
            $old['email'] = $email;

            if ($username === '' || strlen($username) < 3) {
                $errors[] = 'Le pseudo doit contenir au moins 3 caractères.';
            }

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Merci de saisir un email valide.';
            }

            if ($password === '' || strlen($password) < 8) {
                $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
            }

            if ($password !== $passwordConfirm) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }

            if ($username !== '' && $this->userManager->findOneByUsername($username)) {
                $errors[] = 'Ce pseudo est déjà utilisé.';
            }

            if ($email !== '' && $this->userManager->findOneByEmail($email)) {
                $errors[] = 'Un compte existe déjà avec cet email.';
            }

            if (empty($errors)) {
                $user = $this->userManager->create($username, $email, $password);
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                ];

                header('Location: index.php?action=account');
                exit;
            }
        }

        $view = new View($pageTitle);
        $view->render('registerView', [
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function login(): void
    {
        $pageTitle = 'TomTroc - Connexion';
        $errors = [];
        $old = [
            'login' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginInput = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';
            $old['login'] = $loginInput;

            if ($loginInput === '' || $password === '') {
                $errors[] = 'Merci de renseigner vos identifiants.';
            } else {
                $user = $this->userManager->findOneByLogin($loginInput);

                if (!$user || !$user->getPasswordHash() || !password_verify($password, $user->getPasswordHash())) {
                    $errors[] = 'Identifiants invalides.';
                } elseif (!$user->isActive()) {
                    $errors[] = 'Votre compte est inactif. Contactez un administrateur.';
                } else {
                    session_regenerate_id(true);
                    $_SESSION['user'] = [
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                        'email' => $user->getEmail(),
                    ];

                    header('Location: index.php?action=account');
                    exit;
                }
            }
        }

        $view = new View($pageTitle);
        $view->render('loginView', [
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
        header('Location: index.php?action=home');
        exit;
    }
}

