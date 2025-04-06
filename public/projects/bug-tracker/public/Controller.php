<?php

require_once __DIR__ . '/../app/core/SessionManager.php';

//The base controller class that all other controllers will extend
class Controller {

    protected $session; //created here so can be used by all controllers

    //could take in a parameter for a session name
    function __construct() {
        $this->session = new SessionManager();
    }

    protected function render($view, $data = []) {
        extract($data);
        
        include(__DIR__ . "/../app/views/$view.php");
    }

    // Helper method to redirect to a specific route
    protected function redirect($route) {
        // Construct the full URL for the route, using the base project path
        $baseUrl = "/projects/bug-tracker/public";
        $url = $baseUrl . $route;
        header("Location: $url");
        exit();
    }
}   
