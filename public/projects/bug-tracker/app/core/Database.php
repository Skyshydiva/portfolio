<?php
    class Database {

        private $dbh;

        function __construct() {

            
            // Load the database configuration from the config file
            $config = require __DIR__ . '/../../config/database.php';
    
            // Set database credentials from the loaded configuration
            $dbServer = $config['DB_SERVER'];
            $dbName = $config['DB'];
            $dbUser = $config['DB_USER'];
            $dbPassword = $config['DB_PASSWORD'];

            try {

                $this->dbh = new PDO("mysql:host=$dbServer;dbname=$dbName;charset=utf8", $dbUser, $dbPassword);
                
                    $this->dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
            } catch (PDOException $pe) {
                echo $pe->getMessage();
                die("Bad Database");
            }
        }//construct

        public function getConnection() {
            return $this->dbh;
        }

        public function query($sql, $params = []) {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        }
    }
?>
