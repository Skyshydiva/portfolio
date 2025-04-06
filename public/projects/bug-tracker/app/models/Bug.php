<?php

    require_once __DIR__ . '/../core/Database.php';

    class Bug
    {
        private $id;
        private $projectId;
        private $ownerId;
        private $assignedToId;
        private $statusId;
        private $priorityId;
        private $summary;
        private $description;
        private $fixDescription;
        private $dateRaised;
        private $targetDate;
        private $dateClosed;

        // Constructor to initialize properties
        public function __construct($data = [])
        {
            $this->id = $data['id'] ?? null;
            $this->projectId = $data['projectId'] ?? null;
            $this->ownerId = $data['ownerId'] ?? null;
            $this->assignedToId = $data['assignedToId'] ?? null;
            $this->statusId = $data['statusId'] ?? 1; // Default to "unassigned" status
            $this->priorityId = $data['priorityId'] ?? 2; // Default to "medium" priority
            $this->summary = $data['summary'] ?? '';
            $this->description = $data['description'] ?? '';
            $this->fixDescription = $data['fixDescription'] ?? null;
            $this->dateRaised = $data['dateRaised'] ?? date('Y-m-d H:i:s');
            $this->targetDate = $data['targetDate'] ?? null;
            $this->dateClosed = $data['dateClosed'] ?? null;
        }

        // Create a new bug in the database
        public function create()
        {
            $database = new Database();
            $conn = $database->getConnection();

            $stmt = $conn->prepare("INSERT INTO bugs (projectId, ownerId, assignedToId, statusId, priorityId, summary, description, targetDate) 
                                    VALUES (:projectId, :ownerId, :assignedToId, :statusId, :priorityId, :summary, :description, :targetDate)");

            $stmt->bindParam(':projectId', $this->projectId);
            $stmt->bindParam(':ownerId', $this->ownerId);
            $stmt->bindParam(':assignedToId', $this->assignedToId);
            if($this->assignedToId != null){
                $stmt->bindValue(':statusId', 2); // Assigned
            }
            else {
                $stmt->bindValue(':statusId', 1); // Default status to Unassigned
            }
            $stmt->bindParam(':priorityId', $this->priorityId);
            $stmt->bindParam(':summary', $this->summary);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':targetDate', $this->targetDate);

            try {
                if ($stmt->execute()) {
                    return true;
                } else {
                    error_log("Failed to insert new bug: " . implode(" ", $stmt->errorInfo()));
                    return false;
                }
            } catch (PDOException $e) {
                error_log("PDOException: " . $e->getMessage());
                return false;
            }
        }

        

        // Update an existing bug in the database
        public function update()
        {
            $database = new Database();
            $conn = $database->getConnection();

            $stmt = $conn->prepare("UPDATE bugs SET projectId = :projectId, ownerId = :ownerId, assignedToId = :assignedToId, statusId = :statusId, priorityId = :priorityId, summary = :summary, description = :description, fixDescription = :fixDescription, targetDate = :targetDate, dateClosed = :dateClosed WHERE id = :id");

            $stmt->bindParam(':projectId', $this->projectId);
            $stmt->bindParam(':ownerId', $this->ownerId);
            $stmt->bindParam(':assignedToId', $this->assignedToId);
            $stmt->bindParam(':statusId', $this->statusId);
            $stmt->bindParam(':priorityId', $this->priorityId);
            $stmt->bindParam(':summary', $this->summary);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':fixDescription', $this->fixDescription);
            $stmt->bindParam(':targetDate', $this->targetDate);

            // Handling `dateClosed` to allow `NULL` value
            if ($this->dateClosed !== null) {
                $stmt->bindParam(':dateClosed', $this->dateClosed);
            } else {
                $stmt->bindValue(':dateClosed', null, PDO::PARAM_NULL);
            }

            $stmt->bindParam(':id', $this->id);

            return $stmt->execute();
        }

        public function delete()
        {
            $database = new Database();
            $conn = $database->getConnection();

            $stmt = $conn->prepare("DELETE FROM bugs WHERE id = :id");
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

            return $stmt->execute();
        }

        // Find a bug by ID
        public static function findById($id)
        {
            $database = new Database();
            $conn = $database->getConnection();

            $stmt = $conn->prepare("SELECT * FROM bugs WHERE id = :id LIMIT 1");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $bugData = $stmt->fetch(PDO::FETCH_ASSOC);

            return $bugData ? new Bug($bugData) : null;
        }


        // Retrieve all open bugs for a specific project
        public static function getOpenBugsByProject($projectId)
        {
            $database = new Database();
            $conn = $database->getConnection();

            $stmt = $conn->prepare("
                SELECT bugs.*, project.Project AS projectName, bug_status.Status AS statusName, priority.Priority AS priorityName
                FROM bugs
                LEFT JOIN project ON bugs.projectId = project.Id
                LEFT JOIN bug_status ON bugs.statusId = bug_status.Id
                LEFT JOIN priority ON bugs.priorityId = priority.Id
                WHERE bugs.projectId = :projectId AND bugs.statusId != (SELECT Id FROM bug_status WHERE Status = 'Closed')
            ");
            $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        // Retrieve overdue bugs
        public static function getOverdueBugs($projectId = null)
        {
            $database = new Database();
            $conn = $database->getConnection();

            if ($projectId) {
                $stmt = $conn->prepare("
                    SELECT bugs.*, project.Project AS projectName, bug_status.Status AS statusName, priority.Priority AS priorityName
                    FROM bugs
                    LEFT JOIN project ON bugs.projectId = project.Id
                    LEFT JOIN bug_status ON bugs.statusId = bug_status.Id
                    LEFT JOIN priority ON bugs.priorityId = priority.Id
                    WHERE bugs.targetDate < NOW() AND bugs.statusId != (SELECT Id FROM bug_status WHERE Status = 'Closed') AND bugs.projectId = :projectId
                ");
                $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);
            } else {
                $stmt = $conn->prepare("
                    SELECT bugs.*, project.Project AS projectName, bug_status.Status AS statusName, priority.Priority AS priorityName
                    FROM bugs
                    LEFT JOIN project ON bugs.projectId = project.Id
                    LEFT JOIN bug_status ON bugs.statusId = bug_status.Id
                    LEFT JOIN priority ON bugs.priorityId = priority.Id
                    WHERE bugs.targetDate < NOW() AND bugs.statusId != (SELECT Id FROM bug_status WHERE Status = 'Closed')
                ");
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        // Method to retrieve overdue bugs for a specific user, optionally filtered by project
        public static function getUserOverdueBugs($userId, $projectId = null)
        {
            $database = new Database();
            $conn = $database->getConnection();

            if ($projectId) {
                $stmt = $conn->prepare("
                    SELECT 
                        bugs.*, 
                        project.Project AS projectName, 
                        bug_status.status AS statusName, 
                        priority.Priority AS priorityName 
                    FROM 
                        bugs 
                    JOIN 
                        project ON bugs.projectId = project.id
                    JOIN 
                        bug_status ON bugs.statusId = bug_status.id
                    JOIN 
                        priority ON bugs.priorityId = priority.id
                    WHERE 
                        targetDate < NOW() 
                        AND statusId != (SELECT Id FROM bug_status WHERE Status = 'Closed')
                        AND (ownerId = :userId OR assignedToId = :userId)
                        AND projectId = :projectId
                ");
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);
            } else {
                $stmt = $conn->prepare("
                    SELECT 
                        bugs.*, 
                        projects.name AS projectName, 
                        bug_status.status AS statusName, 
                        priority.name AS priorityName 
                    FROM 
                        bugs 
                    JOIN 
                        projects ON bugs.projectId = projects.id
                    JOIN 
                        bug_status ON bugs.statusId = bug_status.id
                    JOIN 
                        priority ON bugs.priorityId = priority.id
                    WHERE 
                        targetDate < NOW() 
                        AND statusId != (SELECT Id FROM bug_status WHERE Status = 'Closed')
                        AND (ownerId = :userId OR assignedToId = :userId)
                ");
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        // Retrieve unassigned bugs
        public static function getUnassignedBugs()
        {
            $database = new Database();
            $conn = $database->getConnection();

            $stmt = $conn->prepare(
                "SELECT bugs.*, project.Project AS projectName, bug_status.Status AS statusName, priority.Priority AS priorityName
                FROM bugs
                LEFT JOIN project ON bugs.projectId = project.Id
                LEFT JOIN bug_status ON bugs.statusId = bug_status.Id
                LEFT JOIN priority ON bugs.priorityId = priority.Id
                WHERE assignedToId IS NULL"
            );
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Method to get bugs for a specific project, filtered by a specific user
        public static function getBugsByUserProject($projectId, $userId)
        {
            $database = new Database();
            $conn = $database->getConnection();

            $stmt = $conn->prepare("SELECT b.*, p.Project AS projectName, s.Status AS statusName, pr.Priority AS priorityName, u.Username AS assignedTo
                FROM bugs b
                LEFT JOIN project p ON b.projectId = p.Id
                LEFT JOIN bug_status s ON b.statusId = s.Id
                LEFT JOIN priority pr ON b.priorityId = pr.Id
                LEFT JOIN user_details u ON b.assignedToId = u.Id
                WHERE b.projectId = :projectId AND (ownerId = :userId OR assignedToId = :userId)");
            $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public static function unassignBugsByUser($userId)
        {
            $database = new Database();
            $conn = $database->getConnection();
            if (!$conn) {
                throw new Exception("Database connection failed");
            }

            // Set the assignedToId to NULL for all bugs assigned to this user
            $stmt = $conn->prepare("UPDATE bugs SET assignedToId = NULL, statusId = 1 WHERE assignedToId = :userId");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

            return $stmt->execute();
        }

        public static function deleteBugsByProject($projectId)
        {
            $database = new Database();
            $conn = $database->getConnection();
            if (!$conn) {
                throw new Exception("Database connection failed");
            }

            // Delete all bugs associated with the given project
            $stmt = $conn->prepare("DELETE FROM bugs WHERE projectId = :projectId");
            $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);

            return $stmt->execute();
        }

        public static function getAllBugs()
        {
            $database = new Database();
            $conn = $database->getConnection();

            if (!$conn) {
                throw new Exception("Database connection failed");
            }

            // Query to select all bugs
            $stmt = $conn->prepare("SELECT * FROM bugs");
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Retrieve all bugs for a specific project
        public static function getAllBugsByProject($projectId)
        {
            $database = new Database();
            $conn = $database->getConnection();

            if (!$conn) {
                throw new Exception("Database connection failed");
            }

            $stmt = $conn->prepare("
                SELECT b.*, p.Project AS projectName, s.Status AS statusName, pr.Priority AS priorityName, u.Username AS assignedTo
                FROM bugs b
                LEFT JOIN project p ON b.projectId = p.Id
                LEFT JOIN bug_status s ON b.statusId = s.Id
                LEFT JOIN priority pr ON b.priorityId = pr.Id
                LEFT JOIN user_details u ON b.assignedToId = u.Id
                WHERE b.projectId = :projectId
            ");
            $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        // Getters and Setters for properties
        public function getId() { return $this->id; }
        public function getProjectId() { return $this->projectId; }
        public function setProjectId($projectId) { $this->projectId = $projectId; }

        public function getOwnerId() { return $this->ownerId; }
        public function setOwnerId($ownerId) { $this->ownerId = $ownerId; }

        public function getAssignedToId() { return $this->assignedToId; }
        public function setAssignedToId($assignedToId) { $this->assignedToId = $assignedToId; }

        public function getStatusId() { return $this->statusId; }
        public function setStatusId($statusId) { $this->statusId = $statusId; }

        public function getPriorityId() { return $this->priorityId; }
        public function setPriorityId($priorityId) { $this->priorityId = $priorityId; }

        public function getSummary() { return $this->summary; }
        public function setSummary($summary) { $this->summary = $summary; }

        public function getDescription() { return $this->description; }
        public function setDescription($description) { $this->description = $description; }

        public function getFixDescription() { return $this->fixDescription; }
        public function setFixDescription($fixDescription) { $this->fixDescription = $fixDescription; }

        public function getDateRaised() { return $this->dateRaised; }
        public function setDateRaised($dateRaised) { $this->dateRaised = $dateRaised; }

        public function getTargetDate() { return $this->targetDate; }
        public function setTargetDate($targetDate) { $this->targetDate = $targetDate; }

        public function getDateClosed() { return $this->dateClosed; }
        public function setDateClosed($dateClosed) { $this->dateClosed = $dateClosed; }

    }