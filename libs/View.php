<?php

class View
{
    public static function render(string $path, array $data = []): void
    {
        // Convierte las claves del arreglo en variables para la vista
        extract($data);

        // Ruta base de las vistas
        $basePath = __DIR__ . '/../views/';

        // Asegura que siempre termine en .php
        $file = $basePath . $path . '.php';

        if (file_exists($file)) {
            require $file;
        } else {
            echo "Vista no encontrada: " . htmlspecialchars($file);
        }
    }
}
