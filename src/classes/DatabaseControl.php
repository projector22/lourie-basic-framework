<?php

class DatabaseControl {

    protected $conn;

    public function __construct(){
        $this->conn = $this->connect_db();
    }//__construct

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
    
    public function __destruct(){
        //Close connection
        $this->conn = null;
    }//__destruct
}