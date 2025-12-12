<?php
declare(strict_types=1);

namespace App\Libs;

class View
{
    public static function render(string $path, array $data = []): void
    {
        // Convierte claves en variables para la vista (sin sobrescribir)
        if (!empty($data)) {
            extract($data, EXTR_SKIP);
        }

        // Base de vistas
        $basePath = __DIR__ . '/../views/';

        // Asegura extensión .php
        $file = $basePath . ltrim($path, '/') . '.php';

        if (is_file($file)) {
            require_once $file;
            return;
        }

        echo "Vista no encontrada: " . htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
    }
}
