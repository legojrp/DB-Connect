<?php
// DBConnect Library - a simple PHP library for connecting to databases using PDO, but for simplicity, and beginners. 
 
// Copyright (c) 2024 legojrp. All rights reserved.
// Licensed via MIT license. See LICENSE file in the project root for full license information.

// Current version: v0.1.1
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

    /**
     * Create a new instance of DBConnect with the Credential object. Treat like constructor
     *  
     * @param Credential $credentials The credentials for the database.
     * @return DBConnect The new instance of DBConnect.
     * @throws Exception If the connection fails.
     * @throws PDOException If an error occurs while executing the query.
     *  
     */

    public static function withCredential(Credential $credentials) {
        $self = new self();
        $self->construct($credentials);
        return $self;
    }
    /**
     * A static function that creates a new instance with the given, host, user, password, and database.
     *
     * @param string $host the host
     * @param string $user the user
     * @param string $password Password to the database
     * @param string $database database name
     * @return DBConnect The new instance of DBConnect.
     */
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

    /**
     * Insert data into the specified table with the given columns, values, and condition.
     *
     * @param string $table The name of the table
     * @param array $columns The columns to be inserted
     * @param array $values The values to be inserted
     * @param string $condition The condition to be applied in the WHERE clause
     * @return array The inserted data
     */
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

    /**
     * Update the specified table with the provided columns and values based on the given condition.
     *
     * @param string $table The name of the table to be updated
     * @param array $columns The columns to be updated
     * @param array $values The new values for the columns
     * @param string $condition The condition for the update operation
     * @return array The result of the update operation
     */
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
    /**
     * Deletes data from the specified table based on the given condition.
     *
     * @param mixed $table The table name from which to delete the data.
     * @param mixed $condition The condition for deleting the data.
     * @throws PDOException If an error occurs while executing the query.
     * @return array The result of fetching the deleted data.
     */
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

    /**
     * Get the host value.
     *
     * @return string The host eg. localhost
     * 
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Get the user value.
     * 
     * @return string The user
     * 
     * */

    public function getUser() {
        return $this->user;
    }

    /**
     * Get the password value
     * 
     * @return string The password
     * 
     * 
     * */

    public function getPassword() {
        return $this->password;
    }

    /**
     * 
     * Get the database value
     * 
     * @return string The database
     * 
     * */


    public function getDatabase() {
        return $this->database;
    }


}

