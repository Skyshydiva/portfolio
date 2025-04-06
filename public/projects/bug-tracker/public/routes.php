<?php

//Define the routes for your application.
require_once('./Router.php');
require_once('./../app/controllers/AuthController.php');
require_once('./../app/controllers/AdminController.php');
require_once('./../app/controllers/BugController.php');
require_once('./../app/controllers/HomeController.php');

$router = new Router();

//Add routes here: "path", "Controller", "controllerFunction")
//default route for system - could be login or something else
$router->addRoute('^$','HomeController', 'index'); // Home page route            
$router->addRoute('home','HomeController', 'index'); // Home page route            
$router->addRoute('login', 'AuthController', 'login');           // Login route
$router->addRoute('logout', 'AuthController', 'logout');         // Logout route
$router->addRoute('bug', 'BugController', 'getBugs');              // Bug management page
$router->addRoute('bug/add', 'BugController', 'addBug');              // Add bug
$router->addRoute('bug/view/(\d+)', 'BugController', 'viewBug'); // View specific bug with an ID
$router->addRoute('bug/update/(\d+)', 'BugController', 'updateBug'); // Update a specific bug with an ID
$router->addRoute('bug/delete/(\d+)', 'BugController', 'deleteBug'); // Delete a specific bug with an ID
$router->addRoute('bug/assign', 'BugController', 'assignBug');   // Assign bug to a user
$router->addRoute('admin', 'AdminController', 'showAdmin'); // Admin dashboard
$router->addRoute('admin/add-user', 'AdminController', 'addUser'); // Add new user (Admin only)
$router->addRoute('admin/delete-user', 'AdminController', 'deleteUser'); // Delete user (Admin only)
$router->addRoute('admin/add-project', 'AdminController', 'addProject'); // Add new project (Admin/Manager)
$router->addRoute('admin/delete-project', 'AdminController', 'deleteProject'); // Delete project (Admin/Manager)
$router->addRoute('admin/assign-user', 'AdminController', 'assignUserToProject'); // Assign user to a project (Admin/Manager)