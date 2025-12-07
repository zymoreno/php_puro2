<?php

namespace App\Controllers;

class Logout
{
    public function main(): void
    {
        session_destroy();
        header("Location: ?");
        exit;
    }
}
