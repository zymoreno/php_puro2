<?php
class Dashboard{

    public function main(){
        $session = $_SESSION['session'] ?? 'admin';
        $viewPath = 'roles/' . $session . '/' . $session . '.view';

        View::render($viewPath);
    }
}
