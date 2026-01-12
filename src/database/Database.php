<?php

class Database {
    private $host = 'localhost:3306';
    private $dbname = 'gpsimdega';
    // private $username = 'gpsimdega_admin';
    // private $password = '3k1x!2P2s';

   private $username = 'root';
   private $password = '';
	
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            die("Database connection failed : " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}

?>

