<?php

require_once __DIR__ . '/../../public/Controller.php'; // Since Controller.php already includes SessionManager

class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct(); // Initialize the base Controller's constructor
    }

    public function index()
    {
        // If user is logged in, show home page; otherwise, show login page
        if ($this->session->get("user_id")) {
            // User is logged in, display the home page
            $this->render('home');
        } else {
            // User is not logged in, display the login page
            $this->render('login');
        }
    }
}
