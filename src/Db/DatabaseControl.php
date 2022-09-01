<?php

namespace LBF\Db;

use \PDO;
use Exception;
use \PDOException;

/**
 * General methods of interacting with the database
 * 
 * use LBF\Db\DatabaseControl;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.0.1
 * @since   LRS 3.17.0  Split out specific functions for connecting to a MySQL DB,
 *                  to make this class more general.
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                  Namespace changed from `Framework` to `LBF`.
 */

class DatabaseControl {

    /**
     * PDO connection object
     * 
     * @var object  $conn
     * 
     * @access  protected
     * @since   LRS 3.0.1
     */

    protected ?object $conn;

    /**
     * Whether or not to show the error
     * 
     * @var boolean     $display_error. Default: false
     * 
     * @access  protected
     * @since   LRS 3.6.1
     */

    protected bool $display_error = false;

    /**
     * Whether or not to log sql statement.
     * 
     * @var boolean     $log_sql    Default: false
     * 
     * @access  protected
     * @since   LRS 3.21.0
     */

    protected bool $log_sql = false;

    /**
     * Whether or not to return the last inserted ID
     * 
     * @var boolean     $return_last_inserted_id    Default: false
     * 
     * @access  public
     * @since   LRS 3.8.0
     */

    public bool $return_last_inserted_id = false;

    /**
     * The id of the last executed insert
     * 
     * @var string      $last_inserted_id   
     * 
     * @access  public
     * @since   LRS 3.8.0
     */

    public string $last_inserted_id;

    /**
     * Whether or not a rollover is being performed. Means that the database name should be set to the 'new year'
     * 
     * @var boolean $rollover   Default: false
     * 
     * @access  public
     * @since   LRS 3.14.0
     */

    public bool $rollover = false;

    /**
     * Get the number of rows affected by the previous execution
     * 
     * @var boolean $get_number_of_rows_affected    Default: false
     * 
     * @access  public
     * @since   LRS 3.15.8
     */

    public bool $get_number_of_rows_affected = false;

    /**
     * Number of rows affected if $this->boolean $get_number_of_rows_affected is true
     * 
     * @var integer $number_of_rows     Default: 0
     * 
     * @access  public
     * @since   LRS 3.15.8
     */

    public int $number_of_rows = 0;


    /**
     * Placeholder method. This class should be extended to and this method should be overwritten
     * 
     * @access  protected
     * @since   LRS 3.0.1
     * @since   LRS 3.17.0  Converted to placeholder class.
     */

    protected function connect_db() {
        throw new Exception( "You cannot use DatabaseControl directly. It should be an extension of a `connectDB` class" );
    }


    /**
     * Performs an SQL PDO select query
     * 
     * @param   string      $sql    An sql statement, something like "SELECT * FROM ..."
     * 
     * @return  array   $results    The results of the query
     * 
     * @access  public
     * @since   LRS 3.0.1
     */    

    public function sql_select( string $sql ): array {
        try {
            $statement = $this->conn->prepare( $sql );
            $statement->execute();
            if ( $this->get_number_of_rows_affected ) {
                $this->number_of_rows = $statement->rowCount();
            }
            $statement->setFetchMode( PDO::FETCH_ASSOC );
            if ( $this->log_sql ) {
                $this->log_sql( $sql );
                $this->log_sql( 'Number of Rows Found: ' . $statement->rowCount() );
            }
    
            return $statement->fetchAll();
        } catch( PDOException $e ) {
            $this->display_the_error( $e, $sql );
            return [];
        }
    }


    /**
     * Performs sql queries such as "INSERT INTO..." and others where no response is required, simply the execution.
     * 
     * @param   string  $sql    An sql string command
     * 
     * @return  true    If the query was successful
     * @return  false   If the query has an error in it.
     * 
     * @access  public
     * @since   LRS 3.1.0
     */

    public function sql_execute( string $sql ): bool {
        try {
            $this->check_db_connection();
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $exec = $this->conn->prepare( $sql );
            $exec->execute();
            if ( $this->return_last_inserted_id ) {
                $this->last_inserted_id = $this->conn->lastInsertId();
            }
            if ( $this->get_number_of_rows_affected ) {
                $this->number_of_rows = $exec->rowCount();
            }
            if ( $this->log_sql ) {
                $this->log_sql( $sql );
                $this->log_sql( 'Number of Rows Affected: ' . $exec->rowCount() );
            }
            return true;
        } catch( PDOException $e ) {
            $this->display_the_error( $e, $sql );
            return false;
        }
    }


    /**
     * Performs sql queries such as "INSERT INTO..." and returns the error object $e when an error occures
     * 
     * @param   string  $sql    An sql string command
     * 
     * @return  true    If the query was successful
     * @return  object  If the query has an error in it, return the error object
     * 
     * @access  public
     * @since   LRS 3.1.0
     * 
     * @deprecated  3.27.0
     */

    public function sql_execute_return_error( string $sql ): bool|PDOException {
        try {
            $this->check_db_connection();
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            $this->conn->exec( $sql );
            if ( $this->return_last_inserted_id ) {
                $this->last_inserted_id = $this->conn->lastInsertId();
            }
            return true;
        } catch( PDOException $e ) {
            return $e;
        }
    }


    /**
     * Draw out errors if enabled
     * 
     * @param   object  $error  The error object to be drawn
     * @param   string  $sql    The sql statement being executed
     * 
     * @access  private
     * @since   LRS 3.6.1
     * @since   LRS 3.7.0   Added @param $sql
     */

    private function display_the_error( object $error, string $sql ): void {
        if ( $this->display_error ) {
            echo "<br><h1>ERROR</h1><br>";
            echo "<b>SQL:</b> $sql<br><br>";
            echo "<b>Error:</b> " . $error->getMessage() . "<br><br>";
        }
    }


    /**
     * Check if the database connection has been established, and if not, create it
     * 
     * @access  protected
     * @since   LRS 3.8.5
     */

    protected function check_db_connection(): void {
        if ( !isset( $this->conn ) ) {
            $this->conn = $this->connect_db();
        }
    }


    /**
     * Log SQL queries.
     * 
     * @param   mixed   $data
     * 
     * @access  private
     * @since   LRS 3.23.3
     */

    private function log_sql( mixed $data ): void {
        $timestamp = date( 'Y-m-d G:i:s' );
        $path = realpath( "./bin/logs/sql.log" );

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
        } catch (\Throwable $th) {
            echo "Error: {$th->getMessage()}";
        }
    }


    /**
     * Close the connection when called. Usually used within __destruct()
     * 
     * @access  protected
     * @since   LRS 3.8.5
     */

    protected function close_connection(): void {
        if ( isset( $this->conn ) ) {
            // Enable the line below to check how many database connections are being called 
            // echo __FILE__  . "<br>";
            $this->conn = null;
        }
    }

}