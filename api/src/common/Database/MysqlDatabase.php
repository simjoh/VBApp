<?php

namespace App\common\Database;

use DomainException;
use mysqli;

class MySQLDatabase
{
    // const SERVERNAME = 'mysql687.loopia.se';
    const SERVERNAME = 's687.loopia.se';
    const USERNAME   = 'brevet@v231933';
    const PASSWORD   = 'brevet2017';
    const DATABASE   = 'vasterbottenbrevet_se';

    private $sql;

    // -------------- public -----------------

    public function __construct() {
        $sql = null;
        $this->sql = new mysqli(self::SERVERNAME, self::USERNAME, self::PASSWORD, self::DATABASE);

        // Abort if failed to connect.

        if ($sql->connect_errno) {
            exit();
        }
    }

    public function begin_transaction()
    {
        $this->sql->begin_transaction();
    }

    public function commit_transaction()
    {
        $this->sql->commit();
    }

    public function prepare($query)
    {
        $stmt = $this->sql->prepare($query);
        return $stmt;
    }

    public function bind($stmt, $params)
    {
        $types = "";
        foreach ($params as $index => $value) {
            $type = $this->get_type($value);
            $types .= $type;
        }
        $stmt->bind_param($types, ...$params);
    }

    public function insert($query, ...$params)
    {
        $stmt = $this->prepare($query);
        $this->bind($stmt, $params);
        $stmt->execute();
        $stmt->close();
    }

    public function insert_prepared($stmt, ...$params)
    {
        $this->bind($stmt, $params);
        $stmt->execute();
    }

    public function delete($query, ...$params)
    {
        return $this->query_changes($query, $params);
    }

    public function update($query, ...$params)
    {
        return $this->query_changes($query, $params);
    }

    public function select($query, ...$params)
    {
        $stmt = $this->prepare($query);
        $this->bind($stmt, $params);
        $stmt->execute();
        $result = $stmt->get_result();
        $table = $this->make_table($result);
        $stmt->close();
        return $table;
    }

    public function select_single($query, ...$params)
    {
        $table = $this->select($query, ...$params);
        if ($table) {
            return $table[0];
        } else {
            return NULL;
        }
    }

    // ------------- private -------------

    private function query_changes($query, $params)
    {
        $stmt = $this->prepare($query);
        $this->bind($stmt, $params);
        $stmt->execute();
        $changes = $stmt->affected_rows;
        $stmt->close();
        return $changes;
    }

    private function make_table($result)
    {
        $table = [];
        while ($row = $result->fetch_assoc()) {
            $table[] = $row;
        }
        $result->close();
        return $table;
    }

    private function get_type($arg)
    {
        switch (gettype($arg)) {
            case 'integer': return 'i'; break;
            case 'double': return 'd'; break;
            case 'string': return 's'; break;
        }
        throw new DomainException('Unsupported parameter type');
    }
}
