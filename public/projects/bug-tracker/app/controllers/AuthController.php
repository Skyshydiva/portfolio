<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../public/Controller.php';

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();  // Initialize the base Controller's constructor, which includes session management
    }

    // Handles the login process
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            try {
                $user = User::findByUsername($username);
                // Hash the password with sha256 to compare with the hashed value in the database
                $hashedPassword = hash('sha256', $password);

                if ($user && $hashedPassword === $user->getPassword()) {
                    $this->session->set('user_id', $user->getId());
                    $this->session->set('user_role', $user->getName());
                    $this->session->set('project_id', $user->getProjectId());
                    $this->session->set('logged_in', true);

                    // Redirect to the home page after successful login
                    $this->redirect('/');
                    return;
                } else {
                    echo "Invalid username or password.";
                }
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        }

        // Render the login view if GET request or invalid login credentials
        $this->render('login');
    }

    // Handles the logout process
    public function logout()
    {
        // Destroy the session and redirect to login page
        $this->session->destroy();
        $this->redirect('/');
    }
}
