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
    
    /** 
     * 
     * The user tables that make up the database structure, organised in an array
     * 
     * @var array   List of all tables in 
     * 
     * @since   0.1 Pre-alpha
     */

    private $table_names = array( USER_ACCOUNTS,
                                  USER_GROUPS,
                                  SESSION_LOGS,
                                  LDAP_CONFIG
                                );

    /**
     * Consructor method, things to do when the class is loaded
     * 
     * @since   0.1 Pre-alpha
     */

    public function __construct(){

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
        foreach( $this->table_names as $i => $tbl ){
            switch( $i ) {
                case 0:
                    //User accounts
                    $data[] = array ( 'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                      'account_name' => 'VARCHAR(50) UNIQUE NOT NULL',
                                      'password' => 'VARCHAR(255) NOT NULL',
                                      'email' => 'VARCHAR(255)',
                                      'last_login' => 'DATETIME',
                                      'first_name' => 'VARCHAR(100)',
                                      'last_name' => 'VARCHAR(100)',
                                      'ldap_user' => 'VARCHAR(1) NOT NULL DEFAULT "0"',
                                      'ldap_dn' => 'VARCHAR(255)',
                                      'account_status' => 'VARCHAR(10) NOT NULL DEFAULT "active"', //active or suspended
                                      'account_permissions' => 'VARCHAR(50)', //list groups, like site_admin, teacher
                                      'ldap_password_fragment' => 'VARCHAR(10)' );
                    break;
                case 1:
                    //user groups
                    $data[] = array( 'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                     'group_name' => 'VARCHAR(250) NOT NULL UNIQUE',
                                     'built_in' => 'VARCHAR(1) NOT NULL DEFAULT "0"',
                                     'position_index' => 'VARCHAR(3) NOT NULL' );
                    break;
                case 2:
                    //session_logs, or user logins records
                    $data[] = array( 'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                     'account_id' => 'VARCHAR(50) NOT NULL',
                                     'ip' => 'VARCHAR(50) NOT NULL',
                                     'browser' => 'VARCHAR(255)',
                                     'timestamp' => 'DATETIME' );
                    break;
                case 3:
                    //ldap config
                    $data[] = array( 'id' => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                     'ldap_enabled' => 'VARCHAR(1) NOT NULL DEFAULT "0"',
                                     'dn' => 'VARCHAR(255)',
                                     'dn_password' => 'VARCHAR(255)',
                                     'address' => 'VARCHAR(255)',
                                     'search_ou' => 'VARCHAR(255)',
                                    //  'student_search_ou' => 'VARCHAR(255)',
                                     'port' => 'VARCHAR(10) DEFAULT "389"' );
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
            $query = $this->sql_execute( $sql );
            if ( !$query ){
                $errors[] = "Table $i : Creation failed";
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
                $results = $this->sql_select( "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$database_name' AND TABLE_NAME='$table' AND COLUMN_NAME='$column'" );
                if ( count( $results ) > 0 ){
                    $query = $this->sql_execute( "ALTER TABLE $table CHANGE COLUMN $column $column $info" );
                    if( $query ){
                        dot();
                    }//if execute update
                } else {
                    echo "Adding $column to $table<br>";
                    $query = $this->sql_execute( "ALTER TABLE $table ADD $column $info" );
                    if( !$query ){
                        echo "Problem adding $column to $table : Creation failed (" . $this->conn->error . ")<br>";
                    } else { 
                        echo "Success: Added $column to $table<br>";
                    }//if execute update
                    lines(2);
                }//if results > 0
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