<?php
require_once __DIR__ . '/../core/Database.php';

class Project {
    private $id;
    private $name;

    public function __construct($data = []) {
        $this->id = $data['Id'] ?? null;
        $this->name = $data['Project'] ?? '';
    }

    // Create a new project (Admin/Manager only)
    public function create() {
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        $stmt = $conn->prepare("
            INSERT INTO project (Project) VALUES (:Project)
        ");
        $stmt->bindParam(':Project', $this->name);

        if ($stmt->execute()) {
            // Get the last inserted ID and set it
            $this->id = $conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Retrieve all projects
    public static function getAllProjects() {
        $database = new Database();
        $conn = $database->getConnection();
    
        $stmt = $conn->prepare("SELECT * FROM project");
        $stmt->execute();
    
        $projects = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $projects[] = new Project($row);
        }
    
        return $projects;
    }


    // Find a project by ID
    public static function findById($id) {
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        $stmt = $conn->prepare("SELECT * FROM project WHERE Id = :Id LIMIT 1");
        $stmt->bindParam(':Id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $projectData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $projectData ? new Project($projectData) : null;
    }

    // Update a project name (Admin/Manager only)
    public function update() {
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        $stmt = $conn->prepare("UPDATE project SET Project = :Project WHERE Id = :Id");
        $stmt->bindParam(':Project', $this->name);
        $stmt->bindParam(':Id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Delete a project (Admin/Manager only)
    public static function deleteById($projectId)
    {
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        // Unassign users from the project
        User::unassignUsersByProject($projectId);

        // Delete all bugs related to the project
        Bug::deleteBugsByProject($projectId);

        // Delete the project
        $stmt = $conn->prepare("DELETE FROM project WHERE Id = :projectId");
        $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);

        return $stmt->execute();
    }


    public static function getProjectByUserId($userId)
    {
        $database = new Database();
        $conn = $database->getConnection();

        $stmt = $conn->prepare("SELECT * FROM project WHERE Id = (SELECT ProjectId FROM user_details WHERE Id = :userId)");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Getters for properties
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }

    // Setters for properties
    public function setName($name) { $this->name = $name; }
}
