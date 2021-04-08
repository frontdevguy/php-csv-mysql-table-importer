<?php
namespace App;

require_once __DIR__ . '/config.php';

use PDO;

final class DBConnection {
    private $host;
    private $user;
    private $password;
    private $connection = null;

    public function __construct() {
        $configuration = \App\getConfig();
        $this->host = $configuration['host'];
        $this->user = $configuration['user'];
        $this->password = $configuration['password'];
    }

    public function openConnection(string $dbName) {
        $this->connection = new PDO("mysql:host=$this->host;dbname=$dbName", $this->user, $this->password, 
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => 0]);
        return $this->connection;
    }

    public function closeConnection() {
        if($this->connection) $this->connection = null;
    }
}
