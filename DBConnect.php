<?php
// DBConnect Library - a simple PHP library for connecting to databases using PDO, but for simplicity, and beginners. 
 
// Copyright (c) 2024 legojrp. All rights reserved.
// Licensed via MIT license. See LICENSE file in the project root for full license information.

// Current version: v0.0.1
// February 2024
// See https://github.com/legojrp/DB-Connect for more updated versions!!. 

// To include this in your project,

// download a zip of the release
// extract it
// move the folder to your project
// include_once("DBConnect/DBConnect.php");
// and use it!!!!

// Contact at legojrp@gmail.com for issues or support


// Made by legojrp - https://github.com/legojrp
// Follow me and my projects for more coding

// Please report any issues found!!!

// Thank you for using DBConnect!

class DBConnect {
    private $credentials; // Credentials for the database via Credential class
    private $conn; // Connection for PDO

    private function __construct() {}

    public static function withCredential(Credential $credentials) {
        $self = new self();
        $self->construct($credentials);
        return $self;
    }
    public static function withParams($host, $user, $password, $database) {
        $self = new self();
        $self->construct(new Credential($host, $user, $password, $database));
        return $self;
    }

    private function construct(Credential $credentials) {

        $this->credentials = $credentials;

        try {
            $dsn = "mysql:host=" . $this->credentials->getHost() . ";dbname=" . $this->credentials->getDatabase();
            $this->conn = new PDO($dsn, $this->credentials->getUser(), $this->credentials->getPassword());
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            // Handle exception
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * A function to select data from a table based on the specified columns and conditions. PLEASE SANITIZE ALL CONDITION!!!
     *
     * @param array $columns The columns to be selected
     * @param string $table The table from which to select the data
     * @param string $condition The condition to be applied in the WHERE clause
     * @return array The selected data from the table
     */
    public function select($table, $columns, $condition) {
        
        $sql = "SELECT :columns FROM :table WHERE :condition";
        
        $stmt = $this->conn->prepare($sql);

        $columns = implode(',', $columns);
        $stmt->bindParam(':columns', $columns);
        $stmt->bindParam(':table', $table);
        $stmt->bindParam(':condition', $condition);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function insert($table, $columns, $values, $condition) {

        $sql = "INSERT INTO :table (:columns) VALUES (:values)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":table", $table);
        $columns = implode(",", $columns);
        $values = implode(",", $values);
        $stmt->bindParam(":columns", $columns);
        $stmt->bindParam(":values", $values);
        $stmt->bindParam(":condition", $condition);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function update($table, $columns, $values, $condition) { 

        $sql = "UPDATE :table SET :statements WHERE :condition";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":table", $table);
        $statement = "";

        for ($i = 0; $i < count($columns) > count($values) ? count($values) : count($columns); $i++) {
            $statement .= $columns[$i] . " = " . $values[$i];
            if ($i < count($columns) - 1) {
                $statement .= ", ";
            }
        }
        $stmt->bindParam(":statements", $statement);
        
        $stmt->bindParam(":condition", $condition);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function delete($table, $condition) {

        $sql = "DELETE FROM :table WHERE :condition";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":table", $table);
        $stmt->bindParam(":condition", $condition);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;

    }
}

class Credential {
    private $host;
    private $user;
    private $password;
    private $database;

    public function __construct($host, $user, $password, $database) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
    }

    public function getHost() {
        return $this->host;
    }
    public function getUser() {
        return $this->user;
    }
    public function getPassword() {
        return $this->password;
    }
    public function getDatabase() {
        return $this->database;
    }


}

