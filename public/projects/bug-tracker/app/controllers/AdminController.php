<?php

// AdminController.php
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../public/Controller.php';

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct(); // Call the parent constructor to initialize the session
    }


    // Show the Admin Dashboard
    public function showAdmin()
    {
        $role = $this->session->get('user_role');
        if ($role !== 'Admin' && $role !== 'Manager') {
            echo "Error: Only Admins or Managers can access the admin dashboard.";
            $this->render('login');
            return;
        }

        // Fetch users and projects to display in the admin view
        $users = User::getAllUsers();
        $projects = Project::getAllProjects();

        // Render the admin page with the data
        $this->render('admin', [
            'users' => $users,
            'projects' => $projects
        ]);
    }

    // Render the Add User page

    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $name = $_POST['name'] ?? '';
            $roleId = $_POST['roleId'] ?? null;
            $projectId = $_POST['projectId'] ?? null;

            if($projectId === 'null' || $projectId === ''){
                $projectId = null;
            }
            

            if (empty($username) || empty($password) || empty($roleId)) {
                $this->render('admin_manage', [
                    'error' => 'Username, Password, and Role are required fields.',
                    'title' => 'Add User',
                    'action' => 'add-user',
                    'projects' => Project::getAllProjects()
                ]);
                return;
            }

            $hashedPassword = hash('sha256', $password);

            // Create a new user instance
            $user = new User([
                'Username' => $username,
                'Password' => $hashedPassword,
                'Name' => $name,
                'RoleID' => $roleId,
                'ProjectId' => $projectId,
            ]);

            // Create the user in the database
            if ($user->create()) {
                // Render the success view with user data
                $this->render('admin_manage', [
                    'success' => true,
                    'user' => [
                        'id' => $user->getId(),
                        'name' => $user->getName(),
                        'username' => $user->getUsername(),
                        'password' => $password, // Not hashed, for confirmation
                        'roleId' => $user->getRoleId(),
                        'projectId' => $user->getProjectId(),
                    ],
                    'title' => 'Add User',
                    'action' => 'add-user'
                ]);
                return;
            } else {
                $this->render('admin_manage', ['error' => 'Failed to add the new user.']);
                return;
            }
        }

        // Render the add user form by default
        $this->render('admin_manage', [
            'projects' => Project::getAllProjects(),
            'users' => User::getAllUsers(),
            'title' => 'Add User',
            'action' => 'add-user'
        ]);
    }




    public function deleteUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['userId'] ?? null;

            if ($userId) {
                $user = User::findById($userId);

                if ($user) {
                    try {
                        if (User::deleteById($userId)) {
                            // Render the delete page with a success flag for the modal
                            $this->render('admin_manage', [
                                'success' => true,
                                'deletedUser' => [
                                    'id' => $user->getId(),
                                    'name' => $user->getName(),
                                    'username' => $user->getUsername(),
                                    'roleId' => $user->getRoleId(),
                                    'projectId' => $user->getProjectId() ?? 'None'
                                ],
                                'users' => User::getAllUsers(),
                                'title' => 'Delete User',
                                'action' => 'delete-user'
                            ]);
                            return;
                        } else {
                            $this->render('admin_manage', [
                                'error' => 'Failed to delete the user.',
                                'users' => User::getAllUsers(),
                                'title' => 'Delete User',
                                'action' => 'delete-user'
                            ]);
                            return;
                        }
                    } catch (Exception $e) {
                        $this->render('admin_manage', [
                            'error' => 'Exception occurred: ' . $e->getMessage(),
                            'users' => User::getAllUsers(),
                            'title' => 'Delete User',
                            'action' => 'delete-user'
                        ]);
                        return;
                    }
                }
            }
        }

        // Render the delete user form by default
        $this->render('admin_manage', [
            'users' => User::getAllUsers(),
            'title' => 'Delete User',
            'action' => 'delete-user'
        ]);
    }


    // Render the Create Project page
    public function addProject()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectName = $_POST['projectName'] ?? '';

            // Check if the project name is empty
            if (empty($projectName)) {
                $this->render('admin_manage', [
                    'error' => 'Project name cannot be empty.',
                    'action' => 'add-project',
                    'title' => 'Add Project',
                ]);
                return;
            }

            // Create the project in the database
            $project = new Project(['Project' => $projectName]);
            if ($project->create()) {
                $this->render('admin_manage', [
                    'success' => true,
                    'createdProject' => [
                        'name' => $projectName,
                        'id' => $project->getId(),
                    ],
                    'action' => 'add-project',
                    'title' => 'Add Project',
                ]);
            } else {
                $this->render('admin_manage', [
                    'error' => 'Failed to add the project.',
                    'action' => 'add-project',
                    'title' => 'Add Project',
                ]);
            }
        } else {
            // Render the add project form by default
            $this->render('admin_manage', [
                'action' => 'add-project',
                'title' => 'Add Project',
            ]);
        }
    }


    // Render the Delete Project page
    public function deleteProject()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectId = $_POST['projectId'] ?? null;

            if ($projectId) {
                $project = Project::findById($projectId);
                if ($project) {
                    if (Project::deleteById($projectId)) {
                        $this->render('admin_manage', [
                            'success' => true,
                            'message' => 'Project deleted successfully.',
                            'projects' => Project::getAllProjects(),
                            'title' => 'Delete Project',
                            'action' => 'delete-project',
                        ]);
                    } else {
                        $this->render('admin_manage', [
                            'error' => 'Failed to delete the project.',
                            'projects' => Project::getAllProjects(),
                            'title' => 'Delete Project',
                            'action' => 'delete-project',
                        ]);
                    }
                    return;
                } else {
                    $this->render('admin_manage', [
                        'error' => 'Project not found.',
                        'projects' => Project::getAllProjects(),
                        'title' => 'Delete Project',
                        'action' => 'delete-project',
                    ]);
                    return;
                }
            }
        }

        // Render the delete project form by default
        $this->render('admin_manage', [
            'projects' => Project::getAllProjects(),
            'title' => 'Delete Project',
            'action' => 'delete-project',
        ]);
    }


    public function assignUserToProject()
    {
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['userId'] ?? null;
            $projectId = $_POST['projectId'] ?? null;

            if ($userId && $projectId) {
                if (User::assignToProject($userId, $projectId)) {
                    $this->render('admin_manage', [
                        'success' => true,
                        'message' => 'User assigned to project successfully.',
                        'users' => User::getAllRegularUsers(),
                        'projects' => Project::getAllProjects(),
                        'title' => 'Assign User to Project',
                        'action' => 'assign-user'
                    ]);
                } else {
                    $this->render('admin_manage', [
                        'error' => 'Failed to assign user to project.',
                        'users' => User::getAllRegularUsers(),
                        'projects' => Project::getAllProjects(),
                        'title' => 'Assign User to Project',
                        'action' => 'assign-user'
                    ]);
                }
            }
        } else {
            // Render the form for assigning user to project
            $this->render('admin_manage', [
                'users' => User::getAllRegularUsers(),
                'projects' => Project::getAllProjects(),
                'title' => 'Assign User to Project',
                'action' => 'assign-user'
            ]);
        }
    }
}