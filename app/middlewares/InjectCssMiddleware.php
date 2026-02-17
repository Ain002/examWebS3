<?php
declare(strict_types=1);

namespace app\middlewares;

use flight\Engine;

/**
 * Middleware léger pour injecter une feuille de style globale dans toutes les réponses HTML.
 * Avantage: on ne modifie aucune vue existante, le CSS s'applique à toutes les pages.
 */
class InjectCssMiddleware
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    // Called after the controller renders and before the response is sent
    public function after(array $params): void
    {
        $response = $this->app->response();
        $body = $response->getBody();

        // Only inject for HTML responses
        $contentType = $response->headers('Content-Type');
        // headers() may return an array of values; normaliser en string
        if (is_array($contentType)) {
            $contentType = implode(';', $contentType);
        }
        $contentType = (string) ($contentType ?? '');
        if ($contentType !== '' && stripos($contentType, 'text/html') === false) {
            return;
        }

        // Le body doit être une chaîne pour effectuer la recherche et l'injection
        if (!is_string($body) || stripos($body, '<head') === false) {
            return;
        }

    // Link to the global CSS (public/css/exam.css) using BASE_URL if available
    $base = defined('BASE_URL') ? BASE_URL : '';
    $link = "\n    <link rel=\"stylesheet\" href=\"" . $base . "/css/exam.css\">\n";

        // Insert right after <head> tag
        $newBody = preg_replace('/<head(.*?)>/i', '<head$1>' . $link, $body, 1);
        if ($newBody) {
            $response->body($newBody);
        }
    }
}
