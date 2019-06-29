<?php

class DatabaseStructure extends DatabaseControl {
    
private $tables;

    public function __construct(){
        $this->tables = array( USER_ACCOUNTS );
    }//__construct
    
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
                        // $sql2 = "ALTER TABLE $table MODIFY $column $info";
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
    
    public function __destruct(){}//__destruct

//need a public function for doing update repair as well as reseting default values in prefilled settings tables    
}


/**
 ** Usage:
 ** execute_create_tables( $link, $table_names );
 ** execute_fix_missing_column( $link, $table_names );
 **/
//Reference
//https://www.guru99.com/alter-drop-rename.html
//https://dev.mysql.com/doc/refman/8.0/en/alter-table-examples.html