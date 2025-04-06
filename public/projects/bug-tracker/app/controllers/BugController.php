<?php
require_once __DIR__ . '/../../public/Controller.php';
require_once __DIR__ . '/../models/Bug.php';

class BugController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getBugs() {
        $role = $this->session->get('user_role');
        $userId = $this->session->get('user_id');
    
        $projects = Project::getAllProjects();
        $groupedBugs = [];
        $unassignedBugs = [];
        $overdueBugs = [];
        $openBugs = [];
    
        if ($role === 'Admin' || $role === 'Manager') {
    
            // Get overdue bugs for Admin and Managers
            $overdueBugs = Bug::getOverdueBugs();
    
            // Get unassigned bugs for Admin and Managers
            $unassignedBugs = Bug::getUnassignedBugs();
    
            // Get open bugs for Admin and Managers
            foreach ($projects as $project) {
                $openBugs[$project->getId()] = Bug::getOpenBugsByProject($project->getId());
            }
    
            // Get all bugs grouped by projects for Admin and Managers
            foreach ($projects as $project) {
                $groupedBugs[$project->getId()] = Bug::getAllBugsByProject($project->getId());
            }
            
        } else {
            // For regular users, get only bugs in their assigned project
            $userProject = Project::getProjectByUserId($userId);
            if ($userProject) {
                // Get overdue bugs for the user's assigned project
                $overdueBugs = Bug::getUserOverdueBugs($userId, $userProject['Id']);
    
                $projectBugs = Bug::getBugsByUserProject($userProject['Id'], $userId);
               
                // Get open bugs for Admin and Managers
                foreach ($projects as $project) {
                    $openBugs[$project->getId()] = Bug::getOpenBugsByProject($project->getId());
                }
    
                // Get all bugs in the user's assigned project
                $groupedBugs[$userProject['Id']] = $projectBugs;
            }
        }
    
        // Render the bug view, passing all the necessary data
        $this->render('bug', [
            'groupedBugs' => $groupedBugs,
            'unassignedBugs' => $unassignedBugs,
            'overdueBugs' => $overdueBugs,
            'openBugs' => $openBugs,
        ]);
    }
    

    // Add a new bug
    public function addBug()
    {
        $userId = $this->session->get('user_id');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle form submission
            $projectId = $_POST['projectId'] ?? null;
            $summary = $_POST['summary'] ?? '';
            $description = $_POST['description'] ?? '';
            $assignedToId = $_POST['assignedToId'];
            $priorityId = $_POST['priorityId'] ?? 2; // Default to Medium priority if not set
            $targetDate = $_POST['targetDate'] ?? null;

            if ($assignedToId === "" || $assignedToId === "null") {
                $assignedToId = null;
            }

            // Validate the required fields
            if (empty($projectId) || empty($summary) || empty($description)) {
                $this->render('bug_add', [
                    'error' => 'Project, Summary, and Description are required.',
                    'projects' => Project::getAllProjects(),
                    'userProject' => Project::getProjectByUserId($userId),
                    'users' => User::getAllUsers()
                ]);
                return;
            }

            // Check the user's current project assignment
            if ($assignedToId) {
                $assignedUser = User::findById($assignedToId);
                if ($assignedUser) {
                    if ($assignedUser->getProjectId() === null) {
                        // Assign the user to the selected project
                        $assignedUser->setProjectId($projectId);
                        $assignedUser->update();
                    } elseif ($assignedUser->getProjectId() != $projectId) {
                        // Render error if the user is already assigned to a different project
                        $this->render('bug_add', [
                            'error' => 'The selected user is assigned to a different project. Please choose another user or unassign them from their current project.',
                            'projects' => Project::getAllProjects(),
                            'userProject' => Project::getProjectByUserId($userId),
                            'users' => User::getAllUsers()
                        ]);
                        return;
                    }
                }
            }

            // Create a new Bug instance
            $bug = new Bug([
                'projectId' => $projectId,
                'ownerId' => $this->session->get('user_id'),
                'assignedToId' => $assignedToId,
                'priorityId' => $priorityId,
                'summary' => $summary,
                'description' => $description,
                'targetDate' => $targetDate
            ]);

            // Insert the new bug into the database
            if ($bug->create()) {
                // Redirect to the bug management page after successful creation
                header('Location: ?bug&success=1');
                exit;
            } else {
                // Handle creation failure
                $this->render('bug_add', [
                    'error' => 'Failed to add the new bug.',
                    'projects' => Project::getAllProjects(),
                    'userProject' => Project::getProjectByUserId($userId),
                    'users' => User::getAllUsers()
                ]);
            }
        } else {
            // Display the add bug form
            $this->render('bug_add', [
                'projects' => Project::getAllProjects(),
                'userProject' => Project::getProjectByUserId($userId),
                'users' => User::getAllUsers()
            ]);
        }
    }




    // Update an existing bug
    public function updateBug($bugId)
{
    $userId = $this->session->get('user_id');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle form submission
        $projectId = $_POST['projectId'] ?? null;
        $summary = $_POST['summary'] ?? '';
        $description = $_POST['description'] ?? '';
        $assignedToId = $_POST['assignedToId'] ?? null;
        $priorityId = $_POST['priorityId'] ?? 2; // Default to Medium priority if not set
        $targetDate = $_POST['targetDate'] ?? null;
        $dateClosed = $_POST['dateClosed'] ?? null;
        $fixDescription = $_POST['fixDescription'] ?? '';

        // Convert empty targetDate and dateClosed to null
        if (empty($targetDate)) {
            $targetDate = null;
        }
        if (empty($dateClosed)) {
            $dateClosed = null;
        }

        // Validate the required fields
        if (empty($projectId) || empty($summary) || empty($description)) {
            $this->render('bug_update', [
                'error' => 'Project, Summary, and Description are required.',
                'bug' => Bug::findById($bugId),
                'projects' => Project::getAllProjects(),
                'users' => User::getAllRegularUsers()
            ]);
            return;
        }

        // Find the bug to be updated
        $bug = Bug::findById($bugId);
        if (!$bug) {
            $this->render('bug_update', [
                'error' => 'Bug not found.',
                'projects' => Project::getAllProjects(),
                'users' => User::getAllRegularUsers()
            ]);
            return;
        }

        // Update the bug properties
        $bug->setProjectId($projectId);
        $bug->setSummary($summary);
        $bug->setDescription($description);
        $bug->setAssignedToId($assignedToId);
        $bug->setPriorityId($priorityId);
        $bug->setTargetDate($targetDate);
        $bug->setDateClosed($dateClosed);
        $bug->setFixDescription($fixDescription);

        // Update the bug in the database
        if ($bug->update()) {
            // Redirect to the bug details page after successful update
            header('Location: ?bug/view/' . $bugId);
            exit;
        } else {
            // Handle update failure
            $this->render('bug_update', [
                'error' => 'Failed to update the bug.',
                'bug' => $bug,
                'projects' => Project::getAllProjects(),
                'users' => User::getAllRegularUsers()
            ]);
        }
    } else {
        // Display the update bug form
        $bug = Bug::findById($bugId);
        if (!$bug) {
            $this->render('bug_update', [
                'error' => 'Bug not found.',
                'projects' => Project::getAllProjects(),
                'users' => User::getAllRegularUsers()
            ]);
            return;
        }

        $this->render('bug_update', [
            'bug' => $bug,
            'projects' => Project::getAllProjects(),
            'users' => User::getAllRegularUsers()
        ]);
    }
}



    // View a specific bug by ID
    public function viewBug($id)
    {
    // Find the bug by ID
    $bug = Bug::findById($id);
    if (!$bug) {
        $this->render('NotFound', ['message' => 'Bug not found']);
        return;
    }

    if ($bug) {
        // Get the human-readable status name from the bug_status table
        $database = new Database();
        $conn = $database->getConnection();
        
        // Store status ID in a variable before binding
        $statusId = $bug->getStatusId();
        $priorityId = $bug->getPriorityId();
        
        // Fetch the status name
        $stmt = $conn->prepare("SELECT Status FROM bug_status WHERE Id = :statusId");
        $stmt->bindParam(':statusId', $statusId, PDO::PARAM_INT);
        $stmt->execute();
        $statusData = $stmt->fetch(PDO::FETCH_ASSOC);
        $statusName = $statusData ? $statusData['Status'] : 'Unknown';

        //Fetch the priority name
        $stmt = $conn->prepare("SELECT Priority FROM priority WHERE Id = :priorityId");
        $stmt->bindParam(':priorityId', $priorityId, PDO::PARAM_INT);
        $stmt->execute();
        $priorityData = $stmt->fetch(PDO::FETCH_ASSOC);
        $priorityName = $priorityData ? $priorityData['Priority'] : 'Unknown';

        // Fetch the assigned user name
        $assignedToId = $bug->getAssignedToId();
        $assignedToName = 'Unassigned';
        if ($assignedToId) {
            $stmt = $conn->prepare("SELECT Name FROM user_details WHERE Id = :assignedToId");
            $stmt->bindParam(':assignedToId', $assignedToId, PDO::PARAM_INT);
            $stmt->execute();
            $assignedData = $stmt->fetch(PDO::FETCH_ASSOC);
            $assignedToName = $assignedData ? $assignedData['Name'] : 'Unknown';
        }

        // Pass additional data to the view
        $data = [
            'bug' => $bug,
            'statusName' => $statusName,
            'priorityName' => $priorityName,
            'assignedToName' => $assignedToName
        ];

        // Render the bug view
        $this->render('bug_view', $data);
    } else {
        echo "Error: Bug not found.";
    }
}

    // Delete a bug by ID (Admins only)
    public function deleteBug($id)
    {
        $role = $this->session->get('user_role');

        if ($role === 'Admin' || $role === 'Manager') {
            $bug = Bug::findById($id);
            if ($bug) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmDelete'])) {
                    // Perform the deletion
                    if ($bug->delete()) {
                        header('Location: ?bug');
                        exit;
                    } else {
                        echo "Error: Unable to delete the bug.";
                    }
                } else {
                    // Render delete confirmation modal
                    $this->render('bug_delete', ['bug' => $bug]);
                }
            } else {
                echo "Error: Bug not found.";
            }
        } else {
            echo "Unauthorized action.";
        }
    }

    // Assign a bug to a user (Admin and Manager)
    public function assignBug($bugId, $userId)
    {
        $role = $this->session->get('user_role');

        if ($role === 'Admin' || $role === 'Manager') {
            $bug = Bug::findById($bugId);
            if ($bug) {
                $bug->setAssignedToId($userId);
                if ($bug->update()) {
                    header('Location: ?bug/view/' . $bug->getId());
                    exit;
                } else {
                    $this->render('bug/assign', ['error' => 'Failed to assign the bug.', 'bug' => $bug]);
                }
            } else {
                $this->render('NotFound', ['message' => 'Bug not found']);
            }
        } else {
            $this->render('NotFound', ['message' => 'Unauthorized access to assign bugs']);
        }
    }

    // Retrieve all bugs for a specific project (restricted by role)
    public function getBugsByProject($projectId)
    {
        $role = $this->session->get('user_role');
        $userId = $this->session->get('user_id');

        if ($role === 'Admin' || $role === 'Manager') {
            $bugs = Bug::getOpenBugsByProject($projectId);
        } else {
            $bugs = Bug::getBugsByUserProject($projectId, $userId);
        }
        $this->render('bug/index', ['bugs' => $bugs]);
    }

    // Get overdue bugs (Admin, Manager, or user's assigned project)
    public function getOverdueBugs($projectId = null)
    {
        $role = $this->session->get('user_role');
        $userId = $this->session->get('user_id');

        if ($role === 'Admin' || $role === 'Manager') {
            $bugs = Bug::getOverdueBugs($projectId);
        } else {
            $bugs = Bug::getUserOverdueBugs($userId, $projectId);
        }
        $this->render('bug/overdue', ['bugs' => $bugs]);
    }

    // Retrieve unassigned bugs (Admin, Manager)
    public function getUnassignedBugs()
    {
        $role = $this->session->get('user_role');

        if ($role === 'Admin' || $role === 'Manager') {
            $bugs = Bug::getUnassignedBugs();
            $this->render('bug/unassigned', ['bugs' => $bugs]);
        } else {
            $this->render('NotFound', ['message' => 'Unauthorized access to unassigned bugs']);
        }
    }
}
