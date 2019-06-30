<?php

/**
 * 
 * General methods of interacting with the database
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class DatabaseControl {

    /**
     * @var pdo connection
     * 
     * @since   0.1 Pre-alpha
     */

    protected $conn;

    /**
     * Consructor method, things to do when the class is loaded
     * 
     * @since   0.1 Pre-alpha
     * 
     */

    public function __construct(){
        $this->conn = $this->connect_db();
    }//__construct


    /**
     * 
     * Connects the app to the database
     * 
     * @return  $conn   The connection variable
     * 
     * @since   0.1 Pre-alpha
     */

    private function connect_db(){
        $servername = DB_LOC;
        $username   = DB_USER;
        $password   = DB_PASS;
        $db_name    = DB_NAME;

        try {
            $conn = new PDO( "mysql:host=$servername;dbname=$db_name", $username, $password );
            $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            return $conn;
        } catch( PDOException $e ){
            die( "Connection failed: " . $e->getMessage() );
        }//catch
    }//public function connect_db()

    /**
     * 
     * Performs an SQL PDO select query
     * 
     * @param   string      $sql    An sql statement, something like "SELECT * FROM ..."
     * @return  $results    The results of the query
     * @return  false       If the query has an error in it.
     * 
     * @since   0.1 Pre-alpha
     */

    public function sql_select( $sql ){
        try {
            $statement = $this->conn->prepare( $sql );
            $statement->execute();
            $statement->setFetchMode( PDO::FETCH_ASSOC );
            $results = $statement->fetchAll();
            return $results;
        } catch( PDOException $e ) {
            echo "Error: " . $e->getMessage();
            return false;
        }//catch
    }//public function sql_select( $sql )

    /**
     * 
     * Performs sql queries such as "INSERT INTO..." and others where no response is required, simply the execution.
     * 
     * @param   string      $sql    An sql string command
     * @return  true        If the query was successful
     * @return  false       If the query has an error in it.
     * 
     * @since   0.1 Pre-alpha
     */
    
    public function sql_execute( $sql ){
        try {
            if ( !isset( $this->conn ) ){
                $this->conn = $this->connect_db();
            }
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $this->conn->exec( $sql );
            return true;
        } catch( PDOException $e ) {
            echo "Error: " . $e->getMessage();
            return false;
        }//catch
    }
    
    /**
     * 
     * Method to sanitize any form data that may be used to perform a malicious attack
     * 
     * @param   string  $data   Any string of data that needs to be sanitized
     * @return  string  $data   After sanitizing the string, return it
     * 
     * @since   0.1 Pre-alpha
     */

    public static function protect( $data ) {
        $data = trim( $data );
        $data = stripslashes( $data );
        $data = htmlspecialchars( $data, ENT_QUOTES );
        return $data;
    }

    /**
     * Destructor method, things to do when the class is closed
     * 
     * Closes the open database connection
     * 
     * @since   0.1 Pre-alpha
     */

    public function __destruct(){
        $this->conn = null;
    }//__destruct
}