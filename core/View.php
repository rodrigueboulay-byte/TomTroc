<?php

/**
 * Gestionnaire de rendu des vues avec un layout principal.
 */
class View
{
    public function __construct(private string $pageTitle = 'TomTroc')
    {
    }

    /**
     * Rend une vue et l'injecte dans le template principal.
     */
    public function render(string $viewName, array $params = []): void
    {
        $viewPath = $this->buildViewPath($viewName);

        if (!file_exists($viewPath)) {
            throw new RuntimeException(sprintf('Vue "%s" introuvable.', $viewName));
        }

        $content = $this->captureView($viewPath, $params);
        $this->renderLayout($content);
    }

    /**
     * Construit le chemin absolu vers une vue.
     */
    private function buildViewPath(string $viewName): string
    {
        if (!defined('TEMPLATE_VIEW_PATH')) {
            throw new RuntimeException('TEMPLATE_VIEW_PATH n\'est pas defini.');
        }

        $sanitized = str_replace(['..', '\\'], ['', '/'], $viewName);
        $sanitized = trim($sanitized, '/');

        if ($sanitized === '') {
            throw new InvalidArgumentException('Nom de vue invalide.');
        }

        return rtrim(TEMPLATE_VIEW_PATH, '/\\') . '/' . $sanitized . '.php';
    }

    /**
     * Capture le rendu d'une vue dans un buffer.
     */
    private function captureView(string $viewPath, array $params): string
    {
        if (!empty($params)) {
            extract($params, EXTR_SKIP);
        }

        ob_start();
        require $viewPath;

        return ob_get_clean() ?: '';
    }

    /**
     * Injecte le contenu capturé dans le layout principal.
     */
    private function renderLayout(string $content): void
    {
        if (!defined('MAIN_VIEW_PATH')) {
            throw new RuntimeException('MAIN_VIEW_PATH n\'est pas defini.');
        }

        if (!file_exists(MAIN_VIEW_PATH)) {
            throw new RuntimeException('Le template principal est introuvable.');
        }

        $pageTitle = $this->pageTitle;

        ob_start();
        require MAIN_VIEW_PATH;
        echo ob_get_clean();
    }
}
