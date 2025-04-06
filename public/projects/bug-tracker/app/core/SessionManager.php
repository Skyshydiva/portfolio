<?php

class SessionManager {
    // Constructor
    function __construct() {
        // Check if the session is already active
        if (session_status() == PHP_SESSION_NONE) {
            session_name("myproject");
            session_start();
        }
    }

    // Get a session value
    function get($name) {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : "";
    }

    // Set a session value
    function set($name, $value) {
        if ($name !== "" && isset($value)) { // $value can't be null
            $_SESSION[$name] = $value;
        }
    }

    // Destroy the session
    function destroy() {
        session_unset();

        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(session_name(), "", time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }
}
