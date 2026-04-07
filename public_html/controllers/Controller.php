<?php

class Controller
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function render(string $view, array $data = []): void
    {
        $pageTitle = $data['pageTitle'] ?? $this->config['app']['name'];
        $currentPage = $_GET['page'] ?? 'dashboard';
        $flash = get_flash();
        $old = consume_old_input();

        extract($data, EXTR_SKIP);

        require __DIR__ . '/../views/partials/header.php';
        require __DIR__ . '/../views/' . $view . '.php';
        require __DIR__ . '/../views/partials/footer.php';
    }

    protected function renderAuth(string $view, array $data = []): void
    {
        $pageTitle = $data['pageTitle'] ?? $this->config['app']['name'];
        $flash = get_flash();
        $old = consume_old_input();

        extract($data, EXTR_SKIP);

        require __DIR__ . '/../views/' . $view . '.php';
    }
}
