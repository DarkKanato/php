<?php

abstract class Controller
{
    protected string $layout = 'layout';

    /**
     * Renders view inside of the layout.
     */
    protected function render(string $view, array $params = []): void
    {
        extract($params);

        ob_start();
        require APP_PATH . '/views/' . $view . '.php';
        $content = ob_get_clean();

        require APP_PATH . '/views/' . $this->layout . '.php';
    }

    protected function redirect(string $route): void
    {
        header('Location: index.php?r=' . $route);
        exit;
    }

    protected function requireLogin(): int
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }
        return (int)$_SESSION['user_id'];
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
