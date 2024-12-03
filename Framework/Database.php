<?php

namespace Framework;

use Exception;
use PDO; //composer 
use PDOException;

class Database
{
    public $conn;

    /**
     * Constructor for Database class
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}"; // DSN = Data Source Name

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false, // Prevent SQL injection attacks by disabling prepared statements
        ];

        try {
            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options); // PDO = PHP Data Object
        } catch (PDOException $e) {
            throw new Exception("Database Connection failed: {$e->getMessage()}");
        }
    }


    /**
     *  QUery the Database
     * 
     * @param string $query
     * 
     * @return PDOStatement
     * @throws PDOException
     */
    public function query($query, $params = [])
    {
        try {
            $sth = $this->conn->prepare($query); // sth = statement handle

            foreach ($params as $param => $value) { //Bind named params
                $sth->bindValue(':' . $param, $value);
            }
            $sth->execute();
            return $sth;
        } catch (PDOException $e) {
            throw new Exception("Database Query failed: {$e->getMessage()}");
        }
    }
}
