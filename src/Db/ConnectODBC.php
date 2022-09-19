<?php

namespace LBF\Db;

use Exception;
use \PDO;
use \PDOException;
use LBF\HTML\Draw;

/**
 * Tools for connecting to a MDB file
 * 
 * use LBF\Db\ConnectODBC;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.17.0
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                      Namespace changed from `Framework` to `LBF`.
 */

class ConnectODBC extends DatabaseControl {

    /**
     * The path of the database that is going to be used
     * 
     * @var string  $database_path
     * 
     * @access  private
     * @since   LRS 3.17.0
     */

    private string $database_path;

    /**
     * The username uid of the database, if required
     * 
     * @var string  $uid
     * 
     * @access  private
     * @since   LRS 3.17.0
     */

    private string $uid;

    /**
     * The passwor pwd of the database, if required
     * 
     * @var string  $pwd
     * 
     * @access  private
     * @since   LRS 3.17.0
     */

    private string $pwd;

    /**
     * The driver string to access ODBC databases
     * 
     * @var string  $driver_string
     * 
     * @access  private
     * @since   LRS 3.17.0
     */

    private string $driver_string;

    /**
     * Alternative connection variable using PHP functional ODBC libraries.
     * 
     * @var resource  $alt_conn
     * 
     * @access  private
     * @since   LRS 3.17.0
     */

    protected $alt_conn;


    /**
     * Contstructor method, things to do when the class is loaded
     * 
     * @access  public
     * @since   LRS 3.17.0
     */

    public function __construct( $database_path, $params = [] ) {
        if ( !file_exists( $database_path ) ) {
            echo "<i>{$database_path}</i> does not refer to a file.";
            throw new Exception( "<i>{$database_path}</i> does not refer to a file." );
        }
        $this->database_path = $database_path;

        $this->uid = isset( $params['uid'] ) ? "Uid={$params['uid']};" : '';
        $this->pwd = isset( $params['pwd'] ) ? "Pwd={$params['pwd']};" : '';

        switch ( PHP_OS ) {
            case 'Linux':
                /**
                 * NOTES
                 * 
                 * @link https://gist.github.com/amirkdv/9672857
                 * @link https://github.com/mdbtools/mdbtools
                 * 
                 * ```sh
                 * sudo apt install odbc-mdbtools
                 * sudo apt install mdbtools
                 * sudo apt install unixodbc-dev
                 * ```
                 */
                $this->driver_string = 'DRIVER=MDBTools;';
                // echo "Unfortunately there does not appear to be a free open source ODBC driver for Linux systems. The process cannot continue";
                // throw new Exception( "Unfortunately there does not appear to be a free open source ODBC driver for Linux systems. The process cannot continue" );
                break;
            case 'WINNT':
                $this->driver_string = 'DRIVER={Microsoft Access Driver (*.mdb, *.accdb)};';
                break;                
        }

        $this->connect_db();
    }


    /**
     * Connectsa the App to the Database
     * 
     * @access  protected
     * @since   LRS 3.17.0
     */

    protected function connect_db() {
        try {
            $this->conn = new PDO( "odbc:{$this->driver_string} charset=UTF-8; DBQ={$this->database_path};{$this->uid}{$this->pwd}" );
            $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        } catch( PDOException $e ) {
            echo "<b>SQL Connect Error:</b> {$e->getMessage()}";
            die;
        }
        $pass = substr( explode( '=', $this->pwd )[1] ?? '', 0, -1 );
        switch ( PHP_OS ) {
            case 'Linux':
                /**
                 * @wip
                 * 
                 * Have not succeeded past this point.
                 */
                // $this->alt_conn = odbc_connect( 
                //     dsn: "{$this->driver_string}DBQ={$this->database_path};", 
                //     user: 'ADODB.Connection', 
                //     password: $pass,
                //     // cursor_type: SQL_CUR_USE_IF_NEEDED,
                //     // cursor_type: SQL_CUR_USE_ODBC,
                //     // cursor_type: SQL_CUR_USE_DRIVER,
                // );
                break;
            case 'WINNT':
                $this->alt_conn = odbc_connect( 
                    dsn: "{$this->driver_string}DBQ={$this->database_path};", 
                    user: 'ADODB.Connection', 
                    password: $pass 
                );
                break;
        }
    }


    /**
     * Get the table names of the database
     * 
     * @uses odbc_connect rather than PDO.
     * 
     * @access  private
     * @since   LRS 3.17.0
     */

    public function get_tables(): void {
        $tables = odbc_tables( $this->alt_conn );
		while ( $entry = odbc_fetch_object( $tables ) ) {
            if ( $entry->TABLE_TYPE == 'TABLE' && !str_contains( $entry->TABLE_NAME, 'MSys' ) ) {
			    $this->tables[] = $entry->TABLE_NAME;
		    }
		}
    }


    /**
     * Get the table schema of each table and write to $this->table_schema
     * 
     * @uses odbc_connect rather than PDO.
     * 
     * @access  private
     * @since   LRS 3.17.0
     */

    public function get_table_schema(): void {
        if ( count ( $this->tables) == 0 ) {
            die( "{$this->database} is empty, halting." );
        }

        foreach ( $this->tables as $table ) {
            $columns = odbc_columns( $this->alt_conn );
            while ( $entry = odbc_fetch_object( $columns ) ) {
                if ( $entry->TABLE_NAME !== $table ) {
                    // Skip schemas which are not directly in the schema
                    continue;
                }
                $entry->COLUMN_NAME = str_replace( ' ', '_', $entry->COLUMN_NAME );
                if ( $entry->TYPE_NAME == 'LONGCHAR' ) {
                    $entry->TYPE_NAME = 'LONGTEXT';
                } else if ( $entry->TYPE_NAME == 'BYTE' ) {
                    $entry->TYPE_NAME = 'VARCHAR';
                } else if ( $entry->TYPE_NAME == 'CURRENCY' ) {
                    $entry->TYPE_NAME = 'VARCHAR';
                } else if ( $entry->TYPE_NAME == 'LONGBINARY' ) {
                    $entry->TYPE_NAME = 'VARBINARY';
                }
                $this->table_schema[$table][$entry->COLUMN_NAME] = $entry;
                $entry->COLUMN_NAME = $this->sanitize_sql_name( $entry->COLUMN_NAME );
                $this->table_columns[$table][] = $entry->COLUMN_NAME;
            }
        }
    }


    /**
     * MySQL Reserved words
     * 
     * @var array   RESERVED_WORDS
     * 
     * @since   MySQL Version 8.0.27
     * 
     * @access  public
     * @since   LRS 3.17.0
     */

    const RESERVED_WORDS = [
        'ACTIVE', 'ADMIN', 'ARRAY', 'ATTRIBUTE', 'AUTHENTICATION', 'BUCKETS', 'CHALLENGE_RESPONSE', 'CLONE', 'COMPONENT', 'CUME_DIST', 
        'DEFINITION', 'DENSE_RANK', 'DESCRIPTION', 'EMPTY', 'ENFORCED', 'ENGINE_ATTRIBUTE', 'EXCEPT', 'EXCLUDE', 'FACTOR', 'FAILED_LOGIN_ATTEMPTS',
        'FINISH', 'FIRST_VALUE', 'FOLLOWING', 'GEOMCOLLECTION', 'GET_MASTER_PUBLIC_KEY', 'GET_SOURCE_PUBLIC_KEY', 'GROUPING', 'GROUPS', 'HISTOGRAM', 
        'HISTORY', 'INACTIVE', 'INITIAL', 'INITIATE', 'INVISIBLE', 'JSON_TABLE', 'JSON_VALUE', 'KEYRING', 'LAG', 'LAST_VALUE', 'LATERAL', 'LEAD', 
        'LOCKED', 'MASTER_COMPRESSION_ALGORITHMS', 'MASTER_PUBLIC_KEY_PATH', 'MASTER_TLS_CIPHERSUITES', 'MASTER_ZSTD_COMPRESSION_LEVEL', 'MEMBER', 
        'NESTED', 'NETWORK_NAMESPACE', 'NOWAIT', 'NTH_VALUE', 'NTILE', 'NULLS', 'OF', 'OFF', 'OJ', 'OLD', 'OPTIONAL', 'ORDINALITY', 'ORGANIZATION', 
        'OTHERS', 'OVER', 'PASSWORD_LOCK_TIME', 'PATH', 'PERCENT_RANK', 'PERSIST', 'PERSIST_ONLY', 'PRECEDING', 'PRIVILEGE_CHECKS_USER', 'PROCESS',
        'RANDOM', 'RANK', 'RECURSIVE', 'REFERENCE', 'REGISTRATION', 'REPLICA', 'REPLICAS', 'REQUIRE_ROW_FORMAT', 'RESOURCE', 'RESPECT', 'RESTART', 
        'RETAIN', 'RETURNING', 'REUSE', 'ROLE', 'ROW_NUMBER', 'SECONDARY', 'SECONDARY_ENGINE', 'SECONDARY_ENGINE_ATTRIBUTE', 'SECONDARY_LOAD', 
        'SECONDARY_UNLOAD', 'SKIP', 'SOURCE_AUTO_POSITION', 'SOURCE_BIND', 'SOURCE_COMPRESSION_ALGORITHMS', 'SOURCE_CONNECT_RETRY', 'SOURCE_DELAY', 
        'SOURCE_HEARTBEAT_PERIOD', 'SOURCE_HOST', 'SOURCE_LOG_FILE', 'SOURCE_LOG_POS', 'SOURCE_PASSWORD', 'SOURCE_PORT', 'SOURCE_PUBLIC_KEY_PATH', 
        'SOURCE_RETRY_COUNT', 'SOURCE_SSL', 'SOURCE_SSL_CA', 'SOURCE_SSL_CAPATH', 'SOURCE_SSL_CERT', 'SOURCE_SSL_CIPHER', 'SOURCE_SSL_CRL', 
        'SOURCE_SSL_CRLPATH', 'SOURCE_SSL_KEY', 'SOURCE_SSL_VERIFY_SERVER_CERT', 'SOURCE_TLS_CIPHERSUITES', 'SOURCE_TLS_VERSION', 'SOURCE_USER', 
        'SOURCE_ZSTD_COMPRESSION_LEVEL', 'SRID', 'STREAM', 'SYSTEM', 'THREAD_PRIORITY', 'TIES', 'TLS', 'UNBOUNDED', 'UNREGISTER', 'VCPU', 'VISIBLE',
        'WINDOW', 'ZONE', 'POSITION', 'LEAVE', 'GROUP', 'ORDER', 'TO', 'GET', 'ASC', 'DESC', 'OPTION', 'PRIMARY', 'CONDITION', 'KEY', 'FLOAT',
    ];


    /**
     * MrSQL Illegal characters
     * 
     * @var array   ILLEGAL_CHARACTERS
     * 
     * @since   MySQL Version 8.0.27
     * 
     * @access  public
     * @since   LRS 3.17.0
     */

    const ILLEGAL_CHARACTERS = [
        '*', '(', ')', ' ', '<', '>', '-'
    ];


    /**
     * Sanitize a table name
     * 
     * @param   string  $entry                      The table name
     * @param   boolean $display_text_feedback      Whether or not to draw text response to the screen.
     * 
     * @return  string
     * 
     * @access  private
     * @since   LRS 3.17.0
     */

    private function sanitize_sql_name( string $entry, bool $display_text_feedback = false ): string {
        $changed = false;
        foreach ( self::ILLEGAL_CHARACTERS as $char ) {
            if ( str_contains( $entry, $char ) ) {
                $entry = str_replace( $char, '', $entry );
                $changed = true;
            }
        }
        if ( $changed ) {
            if ( $display_text_feedback ) {
                echo "<i><b>{$entry}</b> contains a reserved word for MySQL. Table renamed to <b>{$entry}__to_rename</b></i>";
                Draw::lines( 2 );
            }
            return $entry . '__to_rename';
        }

        $numbers = ['0','1','2','3','4','5','6','7','8','9'];

        if ( in_array( $entry[0], $numbers ) ) {
            if ( $display_text_feedback ) {
                echo "<i><b>{$entry}</b> begins with a number, Table renamed to <b>numeric__{$entry}</b>";
                Draw::lines( 2 ); 
            }
            return 'numeric__' . $entry;
        }
        
        if ( in_array( strtoupper( $entry ), self::RESERVED_WORDS, true ) ) {
            if ( $display_text_feedback ) {
                echo "<i><b>{$entry}</b> contains a reserved word for MySQL. Table renamed to <b>{$entry}__to_rename</b></i>";
                Draw::lines( 2 );
            }
            return $entry . '__to_rename';
        }

        return $entry;
    }


    /**
     * Destructor method, things to do when the class is closed
     * 
     * Closes the open database connection
     * 
     * @access  public
     * @since   LRS 3.17.0
     */

    public function __destruct() {
        $this->close_connection();
    }
}