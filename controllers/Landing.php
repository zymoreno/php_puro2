<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Libs\View;

class Landing
{
    public function main(): void
    {
        View::render('modules/landing.view');
    }
}
