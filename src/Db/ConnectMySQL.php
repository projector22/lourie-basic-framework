<?php

namespace Framework\Db;

use \PDO;
use Exception;
use Throwable;
use \PDOException;

/**
 * New fully revamped MySQL interface class, making use of PDO prepared statements.
 * 
 * use Framework\Db\ConnectMySQL;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.0.1
 * @since   3.12.0  Split off MySQL search queries into DatabaseMeta.
 * @since   3.17.0  Split off from DatabaseControl.
 * @since   3.27.0  Merge back all methods for interfacing with the database into this class.
 */

class ConnectMySQL {

    /**
     * PDO connection object
     * 
     * @var object  $conn
     * 
     * @access  private
     * @since   3.0.1
     */

    private ?object $conn;

    /**
     * Whether or not to show the error
     * 
     * @var boolean     $display_error. Default: false
     * 
     * @access  private
     * @since   3.6.1
     */

    private bool $display_error = false;

    /**
     * For debugging - echo the SQL statement before executing
     * 
     * @var boolean $echo_sql   Default: false
     * 
     * @access  private
     * @since   3.12.0
     */

    private bool $echo_sql = false;

    /**
     * Whether or not to log sql statement.
     * 
     * @var boolean     $log_sql    Default: false
     * 
     * @access  private
     * @since   3.21.0
     */

    private bool $log_sql = false;

    /**
     * Whether or not to return the last inserted ID
     * 
     * @var boolean     $return_last_inserted_id    Default: false
     * 
     * @access  public
     * @since   3.8.0
     * 
     * @deprecated  3.27.0  Move in favour of setting this data automatically.
     */

    public bool $return_last_inserted_id = false;


    /**
     * Get the number of rows affected by the previous execution
     * 
     * @var boolean $get_number_of_rows_affected    Default: false
     * 
     * @access  public
     * @since   3.15.8
     * 
     * @deprecated  3.27.0  Move in favour of setting this data automatically.
     */

    public bool $get_number_of_rows_affected = false;

    /**
     * The id of the last executed insert
     * 
     * @var string      $last_inserted_id   
     * 
     * @access  private
     * @since   3.8.0
     * @since   3.27.0  Moved private, access through get_ method
     */

    private string $last_inserted_id;

    /**
     * Number of rows affected if $this->boolean $get_number_of_rows_affected is true
     * 
     * @var integer $number_of_rows     Default: 0
     * 
     * @access  private
     * @since   3.15.8
     * @since   3.27.0  Moved private, access through get_ method
     */

    private int $number_of_rows = 0;

    /**
     * Record the last PDOException.
     * 
     * @var PDOException    $last_error
     * 
     * @access  private
     * @since   3.27.0
     */

    private PDOException $last_error;

    /**
     * Set the primary unique key as the index of each entry of the object / array being returned.
     * 
     * @var boolean $set_primary_key_index
     * 
     * @access  private
     * @since   3.27.0
     */
    
    private bool $set_primary_key_index = false;

    /**
     * The table from which to do perform the search.
     * 
     * @var string  $table
     * 
     * @access  public
     * @since   3.12.0
     */

    protected string $table;

    /**
     * The array of data from the database.
     * 
     * @var array|object   $data   Default: []
     * 
     * Object if single result, otherwise array
     * 
     * @access  public
     * @since   3.12.0
     */

    public array|object $data = [];

    /**
     * The number of records found as a result of the search
     * 
     * @var integer $number_of_records
     * 
     * @access  public
     * @since   3.12.0
     */

    public int $number_of_records;

    /**
     * Whether or not to append data to the existing data records 
     * as opposed to replacing it.
     * 
     * @var boolean $append_to_data     Default: false
     * 
     * @access  public
     * @since   3.12.0
     */

    public bool $append_to_data = false;

    /**
     * Contains the bind data to parse to an SQL statement.
     * 
     * @var array|null  $bind
     * 
     * @access  protected
     * @since   3.27.0
     */

    protected ?array $bind = null;

    /**
     * Whether or not the entry can be hidden.
     * 
     * @var boolean $can_be_hidden
     * 
     * @access  protected
     * @since   3.27.0
     */

    protected bool $can_be_hidden = false;

    /**
     * Whether or not the entry can be hidden.
     * 
     * @var boolean $can_be_deleted
     * 
     * @access  protected
     * @since   3.27.0
     */

    protected bool $can_be_deleted = false;

    /**
     * Whether or not the entry can be hidden.
     * 
     * @var boolean $can_be_archived
     * 
     * @access  protected
     * @since   3.27.0
     */

    protected bool $can_be_archived = false;

    /**
     * Whether or not to include inactive records
     * 
     * @var boolean $include_inactive   Default: false
     * 
     * @access  public
     * @since   3.12.0
     */

    public bool $include_inactive = false;

    /**
     * Whether or not to include deleted records
     * 
     * @var boolean $include_deleted   Default: false
     * 
     * @access  public
     * @since   3.18.0
     */

    public bool $include_deleted = false;

    /**
     * Whether or not to include deleted records
     * 
     * @var boolean $include_hidden   Default: false
     * 
     * @access  public
     * @since   3.18.0
     */

    public bool $include_hidden = false;

    /**
     * Whether or not find_all() or find_one() is being invoked
     * 
     * @var boolean $single_result
     * 
     * @access  protected
     * @since   3.12.0
     */

    protected bool $single_result;

    /**
     * The default values of the parsed search params, if they're not set
     * 
     * ## Options
     * - include_hidden
     * - include_archived
     * - include_deleted
     * 
     * @var array   DEFAULT_SEARCH_PARAMS
     * 
     * @access  public
     * @since   3.15.9
     */

    const DEFAULT_SEARCH_PARAMS = [
        'include_hidden'   => false,
        'include_archived' => false,
        'include_deleted'  => false,
    ];

    /**
     * Whether or not a rollover is being performed. 
     * Means that the database name should be set to the 'new year'
     * 
     * @todo    Think through making this private, and interacting with it this way.
     * 
     * @var boolean $rollover   Default: false
     * 
     * @access  public
     * @since   3.14.0
     */

    public bool $rollover = false;

    /**
     * The template class to apply to an SQL search. If null, an anonymous
     * class will be used.
     * 
     * @var string|null $template_class Default: null
     * 
     * @access  protected
     * @since   3.27.0
     */

    protected ?string $template_class = null;

    /**
     * The index by property when doing a select. If doing a select all,
     * the results will be indexed by this value.
     * 
     * @var string|null $index_data_by  Default: null
     * 
     * @access  protected
     * @since   3.27.0
     */

    protected ?string $index_data_by = null;

    /**
     * Whether or not to switch on all error handling and user feedback
     * in one convenient step.
     * 
     * @var boolean $debug_mode
     * 
     * @access  protected
     * @since   3.27.0
     */

    protected bool $debug_mode = false;

    /**
     * Items which are marked as UNIQUE on the database. Only these msy be used as indexing keys.
     * This property is intended to be overwritten by the child Table Data class.
     * 
     * @var array   $unique_values
     * 
     * @access  public
     * @since   3.27.0
     */

    public array $unique_values = [];


    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @param   boolean $rollover   Whether or not we are rolling over. Default: false
     * 
     * @access  public
     * @since   3.0.1
     */

    public function __construct( bool $rollover = false ) {
        $this->rollover = $rollover;
        $this->conn = $this->connect_db();
    }


    /**
     * Destructor method, things to do when the class is closed
     * 
     * Closes the open database connection
     * 
     * @access  public
     * @since   3.0.1
     */

    public function __destruct() {
        $this->close_connection();
    }


    /**
     * Connects the app to the database
     * 
     * @param   integer|string  $year   A specified year to bind $conn to. It should be passed as YYYY, or 'YYYY'.
     *                                  If null, it will bind to DB_YEAR by default or if $rollover is true DB_YEAR + 1
     *                                  Default: null
     * 
     * @return  $conn   The connection variable
     * 
     * @access  protected
     * @since   3.0.1
     */

    protected function connect_db( ?int $year = null ): object|bool {
        $servername = DB_LOC;
        $username   = DB_USER;
        $password   = DB_PASS;
        if ( is_null ( $year ) ) {
            $db_name = !$this->rollover ? DB_NAME . DB_YEAR : DB_NAME . ( DB_YEAR + 1 );
        } else {
            $db_name = DB_NAME . $year;
        }

        try {
            $conn = new PDO( "mysql:host={$servername};dbname={$db_name}", $username, $password );
            $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            return $conn;
        } catch( PDOException $e ) {
            if ( is_null ( $year ) ) {
                die( "Connection failed: {$e->getMessage()}. This means that the server was unable to connect to {$db_name}" );
            } else {
                // The database probably doesn't exist or can't be connected to by the app for some reason
                return false;
            }
        }
    }


    /**
     * Check if the database connection has been established, and if not, create it
     * 
     * @access  protected
     * @since   3.8.5
     */

    protected function check_db_connection(): void {
        if ( !isset( $this->conn ) ) {
            $this->conn = $this->connect_db();
        }
    }


    /**
     * Close the connection when called. Usually used within __destruct()
     * 
     * @access  protected
     * @since   3.8.5
     */

    protected function close_connection(): void {
        if ( isset( $this->conn ) ) {
            // Enable the line below to check how many database connections are being called 
            // echo __FILE__  . "<br>";
            $this->conn = null;
        }
    }

    /**
     * Get the table names of the database
     * 
     * @return  array
     * 
     * @access  protected
     * @since   3.19.0
     */

    protected function get_tables(): array {
        $raw = $this->sql_select( 
            "SELECT table_name 
             FROM information_schema.tables 
             WHERE TABLE_SCHEMA='" . DB_NAME . date( 'Y' ) . "';"
        );
        $sorted_data = [];
        foreach ( $raw as $table ) {
            $sorted_data[] = $table->TABLE_NAME;
        }
        return $sorted_data;
    }


    /**
     * Get the names of the columns within tables on the database.
     * 
     * @param   string|null $table  Specify a table, if null - return all tables. Default: null
     * 
     * @return  array
     * 
     * @access  protected
     * @since   3.19.0
     */

    protected function get_table_columns( ?string $table = null ): array {
        $sql = function ( $table_name ) {
            return "SELECT COLUMN_NAME 
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA='" . DB_NAME . date( 'Y' ) . "' AND TABLE_NAME='{$table_name}';";
        };
        $all_data = [];
        if ( is_null ( $table ) ) {
            $tables = $this->get_tables();
            foreach( $tables as $the_table ) {
                $raw = $this->sql_select( $sql($the_table) );
                foreach ( $raw as $column ) {
                    $all_data[$the_table][] = $column['COLUMN_NAME'];
                }
            }
        } else {
            $raw = $this->sql_select( $sql($table) );
            foreach ( $raw as $column ) {
                $all_data[$table][] = $column['COLUMN_NAME'];
            }
        }
        return $all_data;
    }


    /**
     * Get the full schema of the columns within tables on the database.
     * 
     * @param   string|null $table  Specify a table, if null - return all tables. Default: null
     * 
     * @return  array
     * 
     * @access  protected
     * @since   3.19.0
     */

    protected function get_table_columns_schemas( ?string $table = null ): array {
        $sql = function ( $table_name ) {
            return "SELECT * 
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA='" . DB_NAME . date( 'Y' ) . "' AND TABLE_NAME='{$table_name}';";
        };
        $all_data = [];
        if ( is_null ( $table ) ) {
            $tables = $this->get_tables();
            foreach( $tables as $the_table ) {
                $raw = $this->sql_select( $sql($the_table) );
                foreach ( $raw as $column ) {
                    $all_data[$the_table][$column->COLUMN_NAME] = $column;
                }
            }
        } else {
            $raw = $this->sql_select( $sql($table) );
            foreach ( $raw as $column ) {
                $all_data[$table][$column->COLUMN_NAME] = $column;
            }
        }
        return $all_data;
    }


    /**
     * Performs an SQL PDO select query, using prepared statements.
     * 
     * If you only want to select a limited number of fields, do not parse $call_class
     * 
     * @param   string      $sql                An sql statement, something like "SELECT * FROM ...".
     * @param   array|null  $bind               If using prepared statements, these are the values to bind.
     *                                          Default: null
     * @param   string|null $index_by           If $this->set_primary_key_index is false, and a custom 
     *                                          index key is desired, use this.
     *                                          Default: null
     * @param   string|null $call_class         The class to map selected properties to. Should include
     *                                          the full namespace of the class called. Leave blank to 
     *                                          use an anonymous class or if a limited number of columns
     *                                          are being selected.
     *                                          Default: null
     * @param   boolean     $expect_one         Whether or not to expect a single result.
     *                                          If true, will return the single selected object.
     *                                          If false, will return an array of objects.
     *                                          Default: false
     * @param   array|null  $constructor_args   Any parameters to parse to the called class. Does nothing 
     *                                          if no class is called.
     *                                          Default: null
     * 
     * @return  object|array   $results    The results of the query. Object if $expect_one is true.
     * 
     * @access  public
     * @since   3.0.1
     * @since   3.27.0  Added params `$bind`, `$index_by`, `$call_class` & `$expect_one`.
     *                  Largely rewritten with prepared statements, and the returned data
     *                  now comes as an object or array of objects, rather than this having
     *                  to be done manually later.
     */

    public function sql_select( 
        string $sql,
        ?array $bind = null,
        ?string $index_by = null,
        ?string $call_class = null,
        bool $expect_one = false,
        ?array $constructor_args = null,
    ): object|array {
        $this->check_db_connection();
        if ( $this->debug_mode ) {
            $this->set_display_error( true );
            $this->set_echo_sql( true );
            $this->set_log_sql( true );
        }
        try {
            $statement = $this->conn->prepare( 
                $sql, 
                [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY],
            );
            if ( $expect_one ) {
                if ( !is_null( $call_class ) ) {
                    $statement->setFetchMode( PDO::FETCH_CLASS, $call_class, $constructor_args );
                } else {
                    $statement->setFetchMode( PDO::FETCH_CLASS|PDO::FETCH_CLASSTYPE );
                }
            }
            $statement->execute( $bind );
            $this->number_of_rows = $statement->rowCount();
            if ( $this->log_sql ) {
                ob_start();
                $statement->debugDumpParams();
                $dump = ob_get_contents();
                if ( $dump == null || $dump == false ) {
                    $dump = $sql;
                }
                $this->log_sql( trim( $dump ) );
                $this->log_sql( 'Number of Rows Selected: ' . $statement->rowCount() );
                ob_end_clean();
            }
            if ( $this->echo_sql ) {
                echo "<pre class='text_align_left'>";
                $statement->debugDumpParams();
                echo "</pre>";
            }

            if ( $this->set_primary_key_index && is_null( $index_by ) ) {
                $fetch_method = PDO::FETCH_CLASS|PDO::FETCH_UNIQUE;
            } else {
                $fetch_method = PDO::FETCH_CLASS;
            }
            if ( $expect_one ) {
                $data = $statement->fetch();
            } else {
                $data = $statement->fetchAll( $fetch_method, $call_class, $constructor_args );
                if ( !is_null( $index_by ) && !$this->set_primary_key_index ) {
                    $kdata = [];
                    foreach ( $data as $entries ) {
                        $kdata[$entries->$index_by] = $entries;
                    }
                    $data = $kdata;
                    unset( $kdata );
                }
            }
            if ( !$data ) {
                return [];
            }
            return $data;
        } catch( PDOException $exception ) {
            $this->last_error = $exception;
            $this->display_the_error( $exception, $sql, $bind );
            return [];
        }
    }


    /**
     * Performs sql queries such as "INSERT INTO...", "UPDATE..." & "DELETE..." as well as 
     * other kinds of SQL commands. Uses prepared PDO statements.
     * 
     * @param   string      $sql    An sql string command. Should be a prepared statement with bind values
     *                              set in the next param, but can be parsed as plain SQL.
     * @param   array|null  $bind   Associative Array of binding values to use in a prepared statement.
     * 
     * @return  true    If the query was successful
     * @return  false   If the query has an error in it.
     * 
     * @example ```php
     * $db->sql_execute(
     * "INSERT INTO table (field1,field2) VALUES (:field_1_value, :field_2_value),
     * [
     *  "field_1_value" => "Cheese", 
     *  "field_2_value" => null
     * ]
     * );
     * ```
     * 
     * @access  public
     * @since   3.1.0
     * @since   3.27.0  Added param `$bind` and largly rewritten to better handle feedback, exceptions and prepared statements.
     */

    public function sql_execute(
        string $sql,
        ?array $bind = null,
    ): bool {
        $this->check_db_connection();
        if ( $this->debug_mode ) {
            $this->set_display_error( true );
            $this->set_echo_sql( true );
            $this->set_log_sql( true );
        }
        try {
            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
            $exec = $this->conn->prepare( $sql );
            if ( !is_null( $bind ) ) {
                /**
                 * If bind params are parsed. This is the ideal way of avoiding SQL injection.
                 */
                if ( isset( $bind[0] ) && is_array( $bind[0] ) ) {
                    /**
                     * If the bind parameters are an array of arrays,
                     * execute each entry recursively.
                     * 
                     * If you need to get the last id or number of rows for each interaction,
                     * rather call sql_execute seperately for each entry.
                     * 
                     * @since   3.27.0
                     */
                    foreach ( $bind as $b ) {
                        $exec->execute( $b );
                        $this->post_sql_execute( $exec, $sql );
                    }
                } else {
                    $exec->execute( $bind );
                    $this->post_sql_execute( $exec, $sql );
                }
            } else {
                $exec->execute();
                $this->post_sql_execute( $exec, $sql );
            }
            return true;
        } catch( PDOException $exception ) {
            $this->last_error = $exception;
            $this->display_the_error( $exception, $sql, $bind );
            return false;
        }
    }


    /**
     * Handle the various post SQL execute tasks.
     * 
     * @param   object  $exec.  From $this->conn->prepare()
     * @param   string  $sql    The SQL being processed.
     * 
     * @access  public
     * @since   3.27.0
     */

    private function post_sql_execute( object $exec, string $sql ): void {
        $this->last_inserted_id = $this->conn->lastInsertId() ?? null;
        $this->number_of_rows   = $exec->rowCount();
        if ( $this->log_sql ) {
            ob_start();
            $exec->debugDumpParams();
            $dump = ob_get_contents();
            if ( $dump == null || $dump == false ) {
                $dump = $sql;
            }
            $this->log_sql( trim( $dump ) );
            $this->log_sql( 'Number of Rows Affected: ' . $exec->rowCount() );
            ob_end_clean();
        }
        if ( $this->echo_sql ) {
            echo "<pre class='text_align_left'>";
            $exec->debugDumpParams();
            echo "</pre>";
        }
    }


    /**
     * Perform an SQL INSERT statement.
     * 
     * @param   array   $data           Key => Value pairs of data to insert into the table
     * @param   boolean $insert_ignore  Whether or not to use an "INSERT IGNORE ... " SQL statement
     *                                  Default: false
     * 
     * @return  boolean     Whether or not the SQL successfully executed.
     * 
     * @access  public
     * @since   3.12.0
     * @since   3.27.0  Rewritten to handle multi entries in the array $data.
     */

    public function insert( array $data, bool $insert_ignore = false ): bool {
        $place_ignore = $insert_ignore ? 'IGNORE ' : '';
        if ( isset( $data[0] ) && is_array( $data[0] ) ) {
            $headings = '(' . implode( ',', array_keys( $data[0] ) ) . ')';
            $values   = "(:" . implode( ",:", array_keys( $data[0] ) ) . ")";
        } else {
            $headings = '(' . implode( ',', array_keys( $data ) ) . ')';
            $values   = "(:" . implode( ",:", array_keys( $data ) ) . ")";
        }

        $sql = "INSERT {$place_ignore}INTO {$this->table} {$headings} VALUES {$values}";
        return $this->sql_execute( $sql, $data );
    }


    /**
     * Perform an SQL UPDATE statement
     * 
     * @param   array|string    $values     The array or string data to be inserted. 
     *                                      If a string, the value pairs to updated.
     *                                      Example: "key1='value1', key2='value2'"
     * @param   array|string    $where      The conditions of the update. Leave blank to affect all rows
     *                                      Example: "key3='value3'"
     *                                      Default: ''
     * 
     * @return  boolean
     * 
     * @access  public
     * @since   3.12.0
     * @since   3.27.0  Updated to parse prepared statements.
     */

    public function update( array|string $values, array|string $where = '' ): bool {
        $this->bind = [];
        if ( is_array( $values ) ) {
            $update_values = [];
            foreach ( $values as $key => $value ) {
                $update_values[] = "{$key}=:{$key}__val";
                $this->bind[$key . "__val"] = $value;
            }
            $update_values = implode( ', ', $update_values );
        } else {
            $update_values = $values;
        }
        $set_where = '';
        if ( is_string( $where ) && $where !== '' ) {
            $set_where = " WHERE {$where}";
        } else if ( is_array( $where ) && count( $where ) > 0 ) {
            $set_where = $this->prepare_where( $where );
        }
        $sql = "UPDATE {$this->table} SET {$update_values}{$set_where}";
        if ( count( $this->bind ) == 0 ) {
            $this->bind = null;
        }
        return $this->sql_execute( $sql, $this->bind );
    }


    /**
     * Perform an SQL DELETE statement
     * 
     * @param   array|string    $where  The where element of the SQL command. Leave blank to delete all records.
     *                                  If array, you can set or joins with sub array content.
     *                                  Example: "key='value'"
     *                                  Example: ['key' => 'value'] -> "key='value'"
     *                                  Example: ['key' => 'value', 'key2' => 'value2'] -> "key='value' AND key2='value2'"
     *                                  Example: [['key' => 'value', 'key2' => 'value2'],['key3' => 'value3']] -> "key='value' AND key2='value2' OR key3='value3'"
     *                                  Default: null
     * 
     * @return  boolean     Whether or not the SQL successfully executed
     * 
     * @access  public
     * @since   3.12.0
     * @since   3.27.0  Updated to handle parsed prepared statements.
     */

    public function delete( array|string $where = '' ): bool {
        $set_where = '';
        $this->bind = [];
        if ( is_string( $where ) && $where !== '' ) {
            $set_where = " WHERE {$where}";
        } else if ( is_array( $where ) && count( $where ) > 0 ) {
            $set_where = $this->prepare_where( $where );
        }
        $sql = "DELETE FROM {$this->table}{$set_where}";
        if ( count( $this->bind ) == 0 ) {
            $this->bind = null;
        }
        return $this->sql_execute( $sql, $this->bind );
    }


    /**
     * Select all data from the database according to the parsed parameters.
     * 
     * @param   array|string  $where      A bit of sql to filter the results 
     *                                    Expected 'example='example'
     *                                    Default: ''
     * 
     * @param   string  $order_by   A bit of sql to create a sort within the returned results
     *                              Expected 'example ASC'
     *                              Default: ''
     * 
     * @param   string  $limit      A bit of sql to limit the search results returned
     *                              Expected ''
     *                              Default: ''
     * 
     * @access  public
     * @since   3.12.0
     * @since   3.12.5  Consolidated into DataMeta
     * @since   3.27.0  Consolidated into ConnectMySQL, updated for prepared statements.
     */

    public function select_all(
        array|string $where = '',
        string $order_by = '',
        string $limit = ''
    ) : void {
        $this->bind = null;
        $this->single_result = false;
        $this->data = $this->sql_select( 
            sql: $this->prepare_select_sql( $where, $order_by, $limit ),
            bind: $this->bind ?? null,
            index_by: $this->index_data_by,
            call_class: $this->template_class,
            expect_one: false,
        );
        $this->number_of_records = count( $this->data ) ?? 0;
    }


    /**
     * Select one record from the database according to the parameters.
     * 
     * @param   string  $where      A bit of sql to filter the results 
     *                              Expected "example='example'" or ['example' => 'example'].
     * 
     * @access  public
     * @since   3.12.2
     * @since   3.12.5  Consolidated into DataMeta
     * @since   3.27.0  Consolidated into ConnectMySQL, updated for prepared statements.
     */

    public function select_one( array|string $where ): void {
        $this->bind = null;
        $this->single_result = true;
        $this->data = $this->sql_select( 
            sql: $this->prepare_select_sql( $where ),
            bind: $this->bind ?? null,
            call_class: $this->template_class,
            expect_one: true,
        );
        $this->number_of_records = $this->data == [] ? 0 : 1;
    }


    /**
     * To get class list data or to update the list with a WHERE sql statement
     * 
     * @param   array|string    $where      A bit of sql to filter the results 
     *                                      Expected 'example='example'
     *                                      Default: ''
     * 
     * @param   string          $order_by   A bit of sql to create a sort within the returned results
     *                                      Expected 'example ASC'
     *                                      Default: ''
     * 
     * @param   string          $limit      A bit of sql to limit the search results returned
     *                                      Expected ''
     *                                      Default: ''
     * 
     * @access  protected
     * @since   3.12.0
     * @since   3.21.0  Added array support
     * @since   3.27.0  Consolidated a number of steps into this method.
     */

    protected function prepare_select_sql( 
        array|string $where = '',
        string $order_by = '',
        string $limit = ''
    ): string {
        if ( !$this->append_to_data ) {
            // Discards any previous data
            $this->clear_data();
        }

        if ( is_string( $where ) ) {
            // String
            $where = $this->prepare_select_where_sql_by_string( $where );
        } else {
            // Array
            if ( count( $where ) == 0 ) {
                $where = '';
            } else {
                $where = $this->prepare_where( $where, true, true );
            }
        }

        if ( $order_by != '' ) {
            // Handle adding the ORDER BY clause to the search string
            if ( stripos( $order_by, 'ORDER BY' ) === false ) {
                $order_by = ' ORDER BY ' . $order_by;
            }

            // Handle adding a leading space to the $order_by clause, in case it's needed
            if ( $order_by[0] != '' ) {
                $order_by = ' ' . $order_by;
            }
        }

        if ( $limit != '' ) {
            // Handle adding the LIMIT clause to the search string
            if ( stripos( $limit, 'LIMIT' ) === false ) {
                $limit = ' LIMIT ' . $limit;
            }

            // Handle adding a leading space to the $limit clause, in case it's needed
            if ( $limit[0] != '' ) {
                $limit = ' ' . $limit;
            }
        }

        return "SELECT * FROM {$this->table}{$where}{$order_by}{$limit}";
    }

    /**
     * Clear the data in in $this->get_class_list_data
     * 
     * @since   3.6.0
     */

    public function clear_data(): void {
        $this->data = [];
        $this->number_of_records = 0;
    }


    /**
     * Prepare the WHERE clause of an SQL statement.
     * 
     * @param   array   $where          Data to parse into the WHERE clause.
     * @param   bool    $check_extras   Whether to include the search for the hidden, archived & deleted.
     *                                  Default: false
     * 
     * @return  string
     * 
     * @access  private
     * @since   3.21.0
     * @since   3.27.0  Rewritten, renamed from `prep_general_where` to 
     *                  `prepare_where` and updated for using 
     *                  prepared statements.
     */

    private function prepare_where( array $where, bool $check_extras = false, bool $reset_bind = false ): string {
        $random_id = function () {
            return substr( md5( rand() ), 0, 4 );
        };

        $set_where = [];
        if ( $reset_bind ) {
            /**
             * @note    The conditional, is because some situations doesn't require a reset (UPDATE, DELETE)
             *          whereas some do (SELECT_ALL).
             * 
             * @since   3.27.0
             */
            $this->bind = [];
        }
        foreach ( $where as $key => $value ) {
            if ( is_string( $value ) || is_int( $value ) || is_float( $value ) || is_null( $value ) ) {
                $uses_or = false;
            } else if ( is_array( $value ) ) {
                /**
                 * THis handles the difference between IN() and logical OR groupings.
                 * 
                 * @note    There is a bug if you wish to join and IN() with an OR at the same time.
                 *          This causes the SQL to give unexpected results.
                 * 
                 * @todo    Fix the above.
                 * 
                 * @since   3.27.0
                 */
                if ( isset( $where[0] ) ) {
                    $uses_or = true;
                } else {
                    $uses_or = false;
                }
            } else {
                throw new Exception( "Invalid data passed to SQL WHERE clause." );
            }
        }
        if ( $uses_or ) {
            foreach ( $where as $array ) {
                if ( $check_extras ) {
                    if ( $this->can_be_hidden  && !$this->include_hidden ) {
                        $array += ['is_hidden' => 0];
                    }
                    if ( $this->can_be_deleted  && !$this->include_deleted ) {
                        $array += ['is_deleted' => 0];
                    }
                    if ( $this->can_be_archived && !$this->include_inactive ) {
                        $array += ['is_archived' => 0];
                    }
                }
                $rand = $random_id();
                $or = [];
                foreach ( $array as $key => $value ) {
                    $or[] = $this->handle_prep_key_val( $key, $value, $rand );
                }
                $set_where[] = implode( ' AND ', $or );
            }
            $set_where = implode( " OR ", $set_where );
        } else {
            if ( $check_extras ) {
                if ( $this->can_be_hidden  && !$this->include_hidden ) {
                    $where += ['is_hidden' => 0];
                }
                if ( $this->can_be_deleted  && !$this->include_deleted ) {
                    $where += ['is_deleted' => 0];
                }
                if ( $this->can_be_archived && !$this->include_inactive ) {
                    $where += ['is_archived' => 0];
                }
            }
            foreach ( $where as $key => $value ) {
                $rand = $random_id();
                $set_where[] = $this->handle_prep_key_val( $key, $value, $rand );
            }
            $set_where = implode( ' AND ', $set_where );
        }
        if ( count ( $this->bind ) == 0 ) {
            $this->bind == null;
        }
        return " WHERE " . $set_where;
    }


    /**
     * Handle various possible SQL commands in the WHERE clause,
     * 
     * @todo    Test all the combos
     * 
     * ## Handles the following
     * - LIKE
     * - NOT LIKE
     * - IS NULL
     * - IS NOT NULL
     * - != (Does not equal)
     * - \> (Greater than)
     * - \>= (Greater than or equal)
     * - \< (Less than)
     * - \<= (Less than or equal)
     * - IN (list)
     * - NOT IN (list)
     * 
     * @param   string|integer          $key    The key to be paired.
     * @param   string|integer|array    $value  The value to be paired.
     * 
     * @return   string
     * 
     * @see https://dev.mysql.com/doc/refman/8.0/en/comparison-operators.html
     * 
     * @todo    Add BETWEEN
     * 
     * @access  private
     * @since   3.21.0
     * @since   3.27.0  Renamed from `select_prep_key_val` to `handle_prep_key_val`
     *                  and rewritten to parse prepared statements.
     */

    private function handle_prep_key_val(
        string|int $key,
        string|int|array $value,
        string $rand
    ): string {
        $is_like = function ( $key ) {
            return substr( $key, -5 ) === ' LIKE';
        };
        $is_not = function ( $key ) {
            return substr( $key, -9 ) === ' NOT LIKE';
        };
        $is_null = function ( $value ) {
            return $value === 'IS NULL';
        };
        $is_not_null = function ( $value ) {
            return $value === 'IS NOT NULL';
        };
        $not_equals = function ( $key ) {
            return substr( $key, -3 ) === ' !=';
        };
        $greater_than = function ( $key ) {
            return substr( $key, -2 ) === ' >';
        };
        $greater_than_equals = function ( $key ) {
            return substr( $key, -3 ) === ' >=';
        };
        $less_than = function ( $key ) {
            return substr( $key, -2 ) === ' <';
        };
        $less_than_equals = function ( $key ) {
            return substr( $key, -3 ) === ' <=';
        };
        $is_in = function ( $key ) {
            return substr( $key, -3 ) === ' IN';
        };
        $is_not_in = function ( $key ) {
            return substr( $key, -7 ) === ' NOT IN';
        };

        if ( $is_like( $key ) || $is_not( $key ) ) {
            /**
             * $field LIKE $value
             * $field NOT LIKE $value
             */
            $this->bind["{$this->strip( $key )}__{$rand}"] = $value;
            return "{$key} :{$this->strip( $key )}__{$rand}";
        } else if ( $is_null( $value ) || $is_not_null( $value ) ) {
            /**
             * $field IN NULL
             * $field IS NOT NULL
             */
            return "{$key} {$value}";
        } else if ( $not_equals( $key ) ) {
            /**
             * Is not equal
             * $field != $value
             */
            $this->bind["{$this->strip( $key )}__{$rand}"] = $value;
            return " {$key}:{$this->strip( $key )}__{$rand}";
        } else if ( 
            $greater_than( $key ) || $greater_than_equals( $key ) ||
            $less_than( $key ) || $less_than_equals( $key )
        ) {
            /**
             * $field > $value
             * $field >= $value
             * $field < $value
             * $field <= $value
             */
            $this->bind["{$this->strip( $key )}__{$rand}"] = $value;
            return " {$key}:{$this->strip( $key )}__{$rand}";
        } else if ( $is_in( $key ) || $is_not_in( $key ) ) {
            /**
             * If using the IN() function.
             * 
             * Can be parsed as an array, which will be functioning
             * as a prepared statement.
             * 
             * Can be parsed as a string, so that SQL statements
             * can be parsed.
             */
            if ( is_array( $value ) ) {
                $col_values = [];
                for ( $i = 0; $i < count( $value ); $i++  ) {
                    $col_values[] = ':k' . $i . $rand;
                    $this->bind['k' . $i . $rand] = $value[$i];
                }            
                return "{$key} (" . implode( ',', $col_values ) . ")";
            } else {
                return "{$key} {$value}";
            }
        }
        /**
         * $field = $value
         */
        $this->bind["{$this->strip( $key )}__{$rand}"] = $value;
        return "{$key}=:{$this->strip( $key )}__{$rand}";
    }


    /**
     * Clear out the the trailing characters used in some scenarios.
     * 
     * @param   string  $s_key  The key to be parsed.
     * 
     * @return  string
     * 
     * @access  private
     * @since   3.27.0
     */

    private function strip( string $s_key ): string {
        $pt = explode( ' ', $s_key )[0];
        $pt = str_replace( '(', '_', $pt );
        $pt = str_replace( ')', '_', $pt );
        return $pt;
    }


    /**
     * Prepare the sql string where category, if parsed as a string.
     * 
     * @param   string  $where  The parsed where to prepare.
     * 
     * @return  string
     * 
     * @access  private
     * @since   3.21.0
     */

    private function prepare_select_where_sql_by_string( string $where ): string {
        if ( $this->can_be_hidden && !$this->include_hidden ) {
            if ( $where == '' ) {
                $where = "is_hidden=0";
            } else {
                $split = preg_split( "/OR /i", $where );
                $where = '';
                foreach ( $split as $item ) {
                    $where .= ' ' . $item . " AND is_hidden=0 OR";
                }
                $where = rtrim( $where, 'OR' );
            }
        }

        if ( $this->can_be_deleted && !$this->include_deleted ) {
            if ( $where == '' ) {
                $where = "is_deleted=0";
            } else {
                $split = preg_split( "/OR /i", $where );
                $where = '';
                foreach ( $split as $item ) {
                    $where .= ' ' . $item . " AND is_deleted=0 OR";
                }
                $where = rtrim( $where, 'OR' );
            }
        }

        // Inject is_archived=0 in the $where when $this->include_inactive = false
        if ( $this->can_be_archived && !$this->include_inactive ) {
            if ( $where == '' ) {
                $where = "is_archived=0";
            } else {
                $split = preg_split( "/OR /i", $where );
                $where = '';
                foreach ( $split as $item ) {
                    $where .= ' ' . $item . " AND is_archived=0 OR";
                }
                $where = rtrim( $where, 'OR' );
            }
        }

        if ( $where != '' ) {
            // Handle adding the WHERE clause to the search string
            if ( strtoupper( explode( ' ', $where )[0] ) != 'WHERE' ) {
                $where = " WHERE " . trim( $where );
            }

            // Handle adding a leading space to the $where clause, in case it's needed
            if ( $where[0] != ' ' ) {
                $where = ' ' . $where;
            }
        }
        return $where;
    }


    /**
     * Set the database year, allows the user to search previous year's databases
     * 
     * NOTE: As changes are made over the years, searching for older records may throw errors
     * 
     * @param   string|integer  $year   The year to search for
     * 
     * @return  boolean     Whether the connection was successful
     * 
     * @access  public
     * @since   3.14.2
     */

    public function set_db_year( string|int $year ): bool {
        $success = $this->connect_db( $year ) !== false;
        if ( $success ) {
            $this->conn = $this->connect_db( $year );
        }
        return $success;
    }


    /**
     * Reset the database connection
     * 
     * @access  public
     * @since   3.14.2
     */

    public function reset_db_connection(): void {
        $this->conn = $this->connect_db();
    }


    /**
     * Perform a truncate table command to empty the table
     * 
     * @access  public
     * @since   3.19.0
     */

    public function empty_table(): void {
        $this->sql_execute( "TRUNCATE TABLE " . $this->table );
    }


    /**
     * Draw out errors if enabled
     * 
     * @param   object  $error  The error object to be drawn
     * @param   string  $sql    The sql statement being executed
     * 
     * @access  private
     * @since   3.6.1
     * @since   3.7.0   Added @param $sql
     */

    private function display_the_error( object $error, string $sql, ?array $bind = null ): void {
        if ( $this->display_error ) {
            echo "<br><h1>ERROR</h1><br>";
            echo "<b>SQL:</b><p class='font_mono text_align_left'>{$sql}</p>";
            if ( !is_null( $bind ) ) {
                echo "<b>Bind Params:</b>";
                echo "<pre class='text_align_left'>";
                var_dump( $bind );
                echo "</pre>";
            }
            switch ( $error->getCode() ) {
                case 42000:
                    echo "<b>Check:</b> That you have included the bind array with prepared SQL statement!<br><br>";
                    break;
            }
            echo "<b>Error:</b> " . $error->getMessage() . "<br><br>";
        }
    }


    /**
     * Log SQL queries.
     * 
     * @param   mixed   $data
     * 
     * @access  private
     * @since   3.23.3
     */

    private function log_sql( mixed $data ): void {
        $timestamp = date( 'Y-m-d G:i:s' );
        $path = BIN_PATH . "logs/sql.log";

        if ( is_array( $data ) || is_object( $data ) ) {
            $text = json_encode( $data, JSON_PRETTY_PRINT );
        } else {
            $text = $data;
        }

        try {
            $fp = fopen( $path, 'a' );
            if ( is_bool( $fp ) ) {
                throw new Exception( "Unable to write file to {$path}<br>" );
            }
            fwrite( $fp, "{$timestamp}\t\t{$text}\n" );
            fclose( $fp );
        } catch ( Throwable $th ) {
            echo "Error: {$th->getMessage()}";
        }
    }


    /**
     * Assign data to properties. If data needs to be custom assigned, rather
     * than automatically, for whatever reason, use this.
     * 
     * @param   string          $class  The full path, including namespace, of the template class.
     * @param   object|array    $data   A line of data to be custom assigned.
     * 
     * @return  object
     * 
     * @access  protected
     * @since   3.15.0
     * @since   3.27.0  Moved to ConnectMySQL, added param $class, return.
     */

    protected function assign_data_to_properties( string $class, object|array $data ): object {
        $container_class = new $class;
        foreach ( $data as $index => $entry ) {
            $container_class->$index = $entry;
        }
        return $container_class;
    }


    /**
     * Set the table being worked on.
     * 
     * @param   string  $table.
     * 
     * @access  public
     * @since   3.27.0
     */

    public function set_table( string $table ): void {
        $this->table = $table;
    }


    /**
     * Set the template class to be used in the search.
     * 
     * @param   string  $template_class.
     * 
     * @access  public
     * @since   3.27.0
     */

    public function set_template_class( string $template_class ): void {
        $this->template_class = $template_class;
    }


    /**
     * Set how to index the data.
     * 
     * @param   string  $index_data_by.
     * 
     * @access  public
     * @since   3.27.0
     */

    public function set_index_data_by( string $index_data_by ): void {
        if ( !in_array( $index_data_by, $this->unique_values ) ) {
            throw new Exception( "Index '{$index_data_by}' is not an a unque value for " . get_class( $this ) );
        }
        $this->index_data_by = $index_data_by;
    }


    /**
     * Return the index by key.
     * 
     * @return  string
     * 
     * @access  public
     * @since   3.27.0
     */

    public function get_data_index(): string {
        return $this->index_data_by;
    }
    

    /**
     * Get the Primary Key ID of the entry that was last inserted.
     * Note: Only works if the primary key is numeric.
     * 
     * @return  string
     * 
     * @access  public
     * @since   3.27.0
     */

    public function get_last_inserted_id(): string {
        return $this->last_inserted_id;
    }


    /**
     * Get the number of rows affected by the last interaction with the database.
     * 
     * @return  integer
     * 
     * @access  public
     * @since   3.27.0
     */

    public function get_number_of_row_affected(): int {
        return $this->number_of_rows;
    }


    /**
     * Get the number of rows selected by the last query of the database.
     * 
     * @return  integer
     * 
     * @access  public
     * @since   3.27.0
     */

    public function get_number_of_row_selected(): int {
        return $this->number_of_rows;
    }


    /**
     * Get last set error object if required.
     * 
     * @return  PDOException
     * 
     * @access  public
     * @since   3.27.0
     */

    public function get_last_error(): PDOException {
        return $this->last_error;
    }


    /**
     * Set the value of $this->set_primary_key_index.
     * 
     * @param   boolean $set    The value to set $this->set_primary_key_index
     * 
     * @access  public
     * @since   3.27.0
     */

    public function set_primary_key_as_index( bool $set ): void {
        $this->set_primary_key_index = $set;
    }


    /**
     * Set the value of $this->display_error.
     * 
     * @param   boolean $set    The value to set $this->display_error
     * 
     * @access  public
     * @since   3.27.0
     */

    public function set_display_error( bool $set ): void {
        $this->display_error = $set;
    }


    /**
     * Set the value of $this->echo_sql.
     * 
     * @param   boolean $set    The value to set $this->echo_sql
     * 
     * @access  public
     * @since   3.27.0
     */

    public function set_echo_sql( bool $set ): void {
        $this->echo_sql = $set;
    }


    /**
     * Set the value of $this->log_sql.
     * 
     * @param   boolean $set    The value to set $this->log_sql
     * 
     * @access  public
     * @since   3.27.0
     */

    public function set_log_sql( bool $set ): void {
        $this->log_sql = $set;
    }


    /**
     * Set the value of $this->append_to_data.
     * 
     * @param   boolean $set    The value to set $this->append_to_data
     * 
     * @access  public
     * @since   3.27.0
     */

    public function set_append_to_data( bool $set ): void {
        $this->append_to_data = $set;
    }


    /**
     * Set the value of $this->debug_mode.
     * 
     * @param   boolean $set    The value to set $this->debug_mode
     * 
     * @access  public
     * @since   3.27.0
     */

    public function set_debug_mode( bool $set ): void {
        $this->debug_mode = $set;
    }

}