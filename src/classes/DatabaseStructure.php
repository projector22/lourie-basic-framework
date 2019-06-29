<?php

/**
 * 
 * Class for controlling and building the site's database. Can be used to install or repair the database structure.
 * 
 * @link https://www.guru99.com/alter-drop-rename.html
 * @link https://dev.mysql.com/doc/refman/8.0/en/alter-table-examples.html
 * 
 * @author  Gareth  Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class DatabaseStructure extends DatabaseControl {
    //TO DO - Needs a public function for doing update repair as well as reseting default values in prefilled settings tables
    //TO DO - Move SQL over to PDO
    
    /** 
     * 
     * The user tables that make up the database structure, organised in an array
     * 
     * @var array
     * 
     * @since   0.1 Pre-alpha
     */

    private $tables;

    /**
     * Consructor method, things to do when the class is loaded
     * 
     * @since   0.1 Pre-alpha
     */

    public function __construct(){
        $this->tables = array( USER_ACCOUNTS );
    }//__construct
    
    /**
     * 
     * Creates array of all the elements of each table to be created/ updated.
     * 
     * @return  array   Returns the complete array with the entire database structure.
     * 
     * @since   0.1 Pre-alpha
     */

    private function database_tables(){
        $data = [];
        foreach( SITE_TABLES as $i => $tbl ){
            switch( $i ) {
                case 0:
                    //students
                    $data[] = array ( 'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                      'user_name' => 'VARCHAR(50) NOT NULL',
                                      'password' => 'VARCHAR(50) NOT NULL' );
                    break;

            }//switch
        }//foreach
        return $data;
    }//database_tables

    /**
     * 
     * Executes the creation of all the tables in the database from the array data returned from database_tables()
     * 
     * @since   0.1 Pre-alpha
     */
    
    public function create_tables(){
        $errors = [];
        $data = $this->database_tables();
        foreach ( $this->table_names as $i => $table ){
            dot();
            $sql = "CREATE TABLE IF NOT EXISTS $table(";
            foreach ( $data[$i] as $k => $d ){
                $sql .= "$k $d,";      
            }//foreach
            $sql = remove_trailing_chars( $sql, ',' );  
            $sql .= ')';
            $query = mysqli_query( $this->conn, $sql );
            if ( !$query ){
                $errors[] = "Table $i : Creation failed (" . $this->conn->error . ")";
            }
        }//foreach
        lines(2);
        foreach( $errors as $msg ) {
            echo "$msg <br>";
        }//foreach
    }//execute_create_tables

    /** 
     * 
     * Checks the structure of any table pulled from database_tables() and adds any missing columns
     * 
     * @since   0.1 Pre-alpha
     */
    
    public function fix_missing_column(){
        $errors = [];
        $database_name = DB_NAME;
        $data = $this->database_tables( $this->table_names );    
        foreach ( $this->table_names as $i => $table ){
            foreach( $data[$i] as $column => $info ){
                $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$database_name' AND TABLE_NAME='$table' AND COLUMN_NAME='$column'";
                if ( $result = mysqli_query( $this->conn, $sql ) ){
                    if ( mysqli_num_rows( $result ) == 0 ){
                        echo "Adding $column to $table<br>";
                        $sql2 = "ALTER TABLE $table ADD $column $info";
                        $query = mysqli_query( $this->conn, $sql2 );
                        if( !$query ){
                            echo "Problem adding $column to $table : Creation failed (" . $this->conn->error . ")<br>";
                        } else { 
                            echo "Success: Added $column to $table<br>";
                        }//if execute update
                        lines(2);
                    } else {
                        $sql2 = "ALTER TABLE $table CHANGE COLUMN $column $column $info";
                        $query = mysqli_query( $this->conn, $sql2 );
                        if( $query ){
                            dot();
                        }//if execute update
                    }
                } else {execute_error( $sql, $this->conn );}            
            }//foreach
        }//foreach
    }//execute_fix_missing_column
    
    /**
     * Destructor method, things to do when the class is closed
     * 
     * @since   0.1 Pre-alpha
     */

    public function __destruct(){

    }//__destruct
}