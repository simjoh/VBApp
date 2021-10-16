<?php

namespace App\common;

class Database
{

    private string $username;
    private string  $password;
    private string $host;
    private string $db;
    private $conn;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->container->get('settings')['db'];

    }

    public function getConnection()
    {
        if (!$this->conn) {
            $username = $this->username;
            $password = $this->password;
            $host = $this->host;
            $db = $this->db;
            $this->conn = new PDO("mysql:dbname=$db;host=$host", $username, $password);
        }
        return $this->connection;
    }


    protected function execute($sql, $args = null) {
        return $this->getConnection()->prepare($sql)->execute($args);
    }
}