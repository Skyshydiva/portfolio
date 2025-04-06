<?php
require_once __DIR__ . '/../core/Database.php';

class User
{
    private $id;
    private $username;
    private $roleId;
    private $password;
    private $name;
    private $projectId;

    public function __construct($data = [])
    {
        $this->id = $data['Id'] ?? null; 
        $this->username = $data['Username'] ?? '';
        $this->password = $data['Password'] ?? null;
        $this->roleId = $data['RoleID'] ?? null;
        $this->name = $data['Name'] ?? '';
        $this->projectId = $data['ProjectId'] ?? null;
    }
    

    // Create a new user
    public function create()
    {
        if (empty($this->password)) {
            throw new Exception("Password cannot be empty");
        }

        $database = new Database();
        $conn = $database->getConnection();

        $stmt = $conn->prepare("INSERT INTO user_details (Username, Password, Name, RoleID, ProjectId) VALUES (:username, :password, :name, :roleid, :projectid)");
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password); // hashed password already set in `addUser`
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':roleid', $this->roleId); // Ensure RoleID is set as an integer
        $stmt->bindParam(':projectid', $this->projectId);

        if ($stmt->execute()) {
            $this->id = $conn->lastInsertId();
            return true;
        } else {
            return false;
        }
    }

    // Update user details
    public function update()
    {
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        $stmt = $conn->prepare("UPDATE user_details SET Username = :username, RoleID = :roleId, Name = :name, ProjectId = :projectId WHERE Id = :id");
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':roleId', $this->roleId);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':projectId', $this->projectId);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }


    public static function getAllUsers()
    {
        $database = new Database();
        $conn = $database->getConnection();

        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        // Query to select all users
        $stmt = $conn->prepare("SELECT * FROM user_details");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllRegularUsers()
    {
        $database = new Database();
        $conn = $database->getConnection();

        $stmt = $conn->prepare("SELECT * FROM user_details WHERE roleId NOT IN (1, 2)"); // Assuming roleId 1 = Admin, 2 = Manager
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Find a user by ID
    public static function findById($id)
    {
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        $stmt = $conn->prepare("SELECT * FROM user_details WHERE Id = :id LIMIT 1");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $userData ? new User($userData) : null;
    }

    // Find a user by username
    public static function findByUsername($username)
    {
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        $stmt = $conn->prepare("SELECT * FROM user_details WHERE Username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $userData ? new User($userData) : null;
    }

    // Delete a user by ID
    public static function deleteById($id)
    {
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            throw new Exception("Database connection failed");
        }
    
        // Remove the user from any assigned projects or bugs before deleting
        // Set assignedToId to NULL for any bugs assigned to this user
        $stmt = $conn->prepare("UPDATE bugs SET assignedToId = NULL WHERE assignedToId = :userId");
        $stmt->bindParam(':userId', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        // Set ownerId to NULL for any bugs owned by this user
        $stmt = $conn->prepare("UPDATE bugs SET ownerId = NULL WHERE ownerId = :userId");
        $stmt->bindParam(':userId', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        // Delete the user from the user_details table
        $stmt = $conn->prepare("DELETE FROM user_details WHERE Id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
        return $stmt->execute();
    }
    

    // Assign a user to a project
    public static function assignToProject($userId, $projectId)
    {
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        $stmt = $conn->prepare("UPDATE user_details SET ProjectId = :projectId WHERE Id = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    //Unassign users by project

    public static function unassignUsersByProject($projectId)
    {
        $database = new Database();
        $conn = $database->getConnection();
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        // Set ProjectId to NULL for all users assigned to the given project
        $stmt = $conn->prepare("UPDATE user_details SET ProjectId = NULL WHERE ProjectId = :projectId");
        $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);

        return $stmt->execute();
    }


    // Getters for properties
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getRoleId() { return $this->roleId; }
    public function getPassword() { return $this->password; }
    public function getName() { return $this->name; }
    public function getProjectId() { return $this->projectId; }

    // Setters for properties
    public function setUsername($username) { $this->username = $username; }
    public function setRoleId($roleId) { $this->roleId = $roleId; }
    public function setPassword($password)
    {
        // Use SHA256 to hash the password
        $this->password = hash('sha256', $password);
    }
    public function setName($name) { $this->name = $name; }
    public function setProjectId($projectId) { $this->projectId = $projectId; }
}
