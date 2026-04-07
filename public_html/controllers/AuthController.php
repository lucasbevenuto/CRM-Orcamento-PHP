<?php

class AuthController extends Controller
{
    private User $users;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->users = new User();
    }

    public function showLogin(): void
    {
        if (is_logged_in()) {
            redirect(route_url(['page' => 'dashboard']));
        }

        $this->renderAuth('auth/login', [
            'pageTitle' => 'Entrar',
        ]);
    }

    public function login(): void
    {
        if (!is_post()) {
            redirect(route_url(['page' => 'login']));
        }

        $username = trim($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        with_old_input(['username' => $username]);

        $user = $this->users->findByUsername($username);
        $valid = false;

        if ($user) {
            $storedPassword = (string) $user['password'];
            $looksHashed = str_starts_with($storedPassword, '$2y$') || str_starts_with($storedPassword, '$2a$') || str_starts_with($storedPassword, '$argon2');
            $valid = $looksHashed ? password_verify($password, $storedPassword) : hash_equals($storedPassword, $password);

            if ($valid && !$looksHashed) {
                $this->users->upgradePassword((int) $user['id'], $password);
            }
        }

        if (!$valid) {
            flash('error', 'Usuário ou senha inválidos.');
            redirect(route_url(['page' => 'login']));
        }

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
        ];

        flash('success', 'Login realizado com sucesso.');
        redirect(route_url(['page' => 'dashboard']));
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        session_start();
        flash('success', 'Sessão encerrada.');
        redirect(route_url(['page' => 'login']));
    }
}
