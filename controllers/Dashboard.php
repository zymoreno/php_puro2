<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Libs\View;

class Dashboard
{
    public function main(): void
    {
        $session = $_SESSION['session'] ?? 'admin';


        $viewPath = "roles/{$session}/{$session}.view";

        View::render($viewPath);
    }
}
