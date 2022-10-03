<?php

namespace LBF\Tools\Errors;

use Throwable;
use LBF\Tools\JSON\JSONTools;
use LBF\Tools\Errors\DrawError;
use LBF\Tools\Files\FileSystem;

/**
 * Class for creating custom ways of drawing out ERROR & EXCEPTION messages.
 * 
 * use LBF\Tools\Errors\ErrorExceptionHandler;
 * 
 * ## Options for $params
 * 
 * - DrawError::STANDARD
 * - DrawError::TEXT_INLINE
 * - DrawError::PRETTY_INLINE
 * - DrawError::BAR
 * - DrawError::HIDDEN
 * 
 * @see src/Tools/Errors/DrawError.php
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   LBF 0.2.0-beta
 */

class ErrorExceptionHandler {

    /**
     * Whether or not the app is running in CLI mode.
     * 
     * @var bool    $is_cli
     * 
     * @access  private
     * @since   LBF 0.2.0-beta
     */

    private bool $is_cli;

    /**
     * The path to the minimized CSS file with styles used by the app.
     * 
     * Swap to the commented out path when changing styles.
     * 
     * @var string  $css_path
     * 
     * @access  private
     * @since   LBF 0.2.0-beta
     */

    private string $css_path = __DIR__ . '/css/error.min.css';
    // private string $css_path = __DIR__ . '/css/error.css';

    /**
     * Define the default bin path in which errors may be saved.
     * 
     * @var string  DEFAULT_BIN_PATH
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    const DEFAULT_BIN_PATH = __DIR__ . '/../../../bin/';

    /**
     * The path into which to log errors.
     * 
     * @var string  $log_file   Default: __DIR__ . '/../../bin/error.log'
     * 
     * @access  private
     * @since   LBF 0.2.0-beta
     */

    private string $log_file = self::DEFAULT_BIN_PATH . 'error.log';

    /**
     * The type of log to generate.
     * 
     * ## Options:
     * 
     * - LogTypes::LOG
     * - LogTypes::HTML
     * - LogTypes::MD
     * - LogTypes::JSON
     * 
     * @var LogTypes    $log_type   Default: LogTypes::LOG
     * 
     * @access  private
     * @since   LBF 0.2.0-beta
     */

    private LogTypes $log_type = LogTypes::LOG;


    /**
     * Class constructor.
     * 
     * @param   array   $params The params being parsed to the class.
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function __construct(

        /**
         * The params being parsed to the class.
         * 
         * @var array   $params
         * ## Options for $params
         * 
         * - DrawError::STANDARD
         * - DrawError::TEXT_INLINE
         * - DrawError::PRETTY_INLINE
         * - DrawError::BAR
         * - DrawError::HIDDEN
         * 
         * @see src/Tools/Errors/DrawError.php
         * 
         * @access  private
         * @since   LBF 0.2.0-beta
         */

        private array $params = [] 
    ) {
        $this->is_cli = !isset( $_SERVER['REMOTE_ADDR'] );

        /**
         * @see src/Tools/Errors/DrawError.php
         */
        $defaults = [
            'log_to_file'     => false,
            'hide_exceptions' => true,
            'display'         => DrawError::STANDARD,
        ];

        foreach ( $defaults as $key => $param ) {
            if ( !isset( $params[$key] ) ) {
                $this->params[$key] = $param;
            }
        }
    }


    /**
     * Enable or disable logging to file.
     * 
     * @param   boolean     $log    Whether to enable or disable logging.
     * @param   string      $path   Overwrite the path to where to write log file.
     * @param   LogTypes    $type   The type of log file to create.
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function set_log_to_file( bool $log, ?string $path = null, LogTypes $type = LogTypes::LOG ): void {
        $this->params['log_to_file'] = $log;
        $this->log_type = $type;

        if ( !is_null( $path ) ) {
            $this->log_file = $path;
        }

        if ( $log ) {
            if ( !FileSystem::file_exists( $this->log_file ) ) {
                FileSystem::create_blank_file( $this->log_file );
                switch( $type ) {
                    case LogTypes::HTML:
                        $file = '
                        <h1> LOG FILE</h1>
                        <style>' . file_get_contents( $this->css_path ) . ' </style>
                        <table class=\'log-table\'>
                            <tr>
                                <th>Timestamp</th>
                                <th>Error</th>
                                <th>Details</th>
                                <th>File</th>
                                <th>Line</th>
                                <th>Error Code</th>
                                <th>Stack Trace</th>
                            </tr>
                        </table>';
                        FileSystem::write_file( $this->log_file, $file );
                        break;
                    case LogTypes::MD:
                        $file = '# LOG FILE

| Timestamp | Error | Details | File | Line | Error Code | Stack Trace |
| --------- | ----- | ------- | ---- | ---- | ---------- | ----------- |';
                        FileSystem::write_file( $this->log_file, $file );
                        break;
                    case LogTypes::JSON:
                        JSONTools::write_json_file( $this->log_file, [] );
                        break;
                }
            }
        }
    }


    /**
     * Set the custom error handlers. All params should be set in class instantiation.
     * 
     * @todo    Think if want set display type as param.
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function set_error_handlers(): void {
        if ( $this->is_cli || $this->params['display'] === DrawError::STANDARD ) {
            return;
        }
        /**
         * @see src/Tools/Errors/DrawError.php
         */
        switch ( $this->params['display'] ) {
            case DrawError::TEXT_INLINE:
                set_exception_handler( [$this, 'text_inline'] );
                set_error_handler( [$this, 'catch_error_text_inline'], E_ALL );
                break;
            case DrawError::PRETTY_INLINE:
                set_exception_handler( [$this, 'pretty_exception_table'] );
                set_error_handler( [$this, 'catch_error_pretty'], E_ALL );
                break;
            case DrawError::BAR:
                /**
                 * @todo    build this one.
                 */
                set_exception_handler( [$this, 'catch_exception'] );
                set_error_handler( [$this, 'catch_error'], E_ALL );
                break;
            case DrawError::HIDDEN:
                set_exception_handler( [$this, 'hidden_exception'] );
                set_error_handler( [$this, 'hidden_error'], E_ALL );
                break;
        }
    }


    /**
     * Draw the appropriate error title, according to the thrown error code.
     * 
     * @param   integer $error_code The errorcode thrown.
     * 
     * @return  string
     * 
     * @access  private
     * @since   LBF 0.2.0-beta
     */

    private function error_title( int $error_code ): string {
        return match( $error_code ) {
            E_ERROR             => 'Error',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_WARNING           => 'Warning',
            E_PARSE             => 'Compile-time Parse Errors',
            E_NOTICE            => 'Notice',
            E_STRICT            => 'Suggestion Notice',
            E_DEPRECATED        => 'Deprecation Notice',
            E_CORE_ERROR        => 'Core Error',
            E_CORE_WARNING      => 'Core Warning',
            E_COMPILE_ERROR     => 'Compile Error',
            E_COMPILE_WARNING   => 'Compile Warning',
            E_USER_ERROR        => 'User Error',
            E_USER_WARNING      => 'User Warning',
            E_USER_NOTICE       => 'User Notice',
            E_USER_DEPRECATED   => 'User Deprecatied',
            E_ALL               => 'Error',
        };
    }


    /**
     * Draw out error messages in a pretty colour box.
     * 
     * @param   int $error_number       The thrown error code.
     * @param   string  $error_string   The error string describing what happened.
     * @param   string  $error_file     The file in which the error occured.
     * @param   int $error_line         The line on which the error occured.
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function catch_error_pretty( int $error_number, string $error_string, string $error_file, int $error_line ): void {
        echo '<style>' . file_get_contents( $this->css_path ) . ' </style>';
        echo "<div class='error-exception-container " . match ( $error_number ) {
            E_NOTICE            => 'general-container',
            E_USER_NOTICE       => 'general-container',
            E_ERROR             => 'error-container',
            E_USER_ERROR        => 'error-container',
            E_WARNING           => 'warning-container',
            E_USER_WARNING      => 'warning-container',
            E_DEPRECATED        => 'deprecated-container',
            E_USER_DEPRECATED   => 'deprecated-container',
            default             => 'general-container',
        } . "'>";
        echo "<h1>{$this->error_title( $error_number )}</h1>";
        echo "{$error_string} in file <b>{$error_file}</b> on line <b>{$error_line}</b>";
        echo "</div>"; // exception-container
        $this->log_errors( [
            'error'   => $error_number,
            'details' => $error_string,
            'file'    => $error_file,
            'line'    => $error_line,
            'code'    => $error_number,
        ] );
    }


    /**
     * Draw out a pretty exception notice and stack trace table.
     * 
     * @param   Throwable   $e  The error object.
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function pretty_exception_table( Throwable $e ): void {
        echo '<style>' . file_get_contents( $this->css_path ) . ' </style>';
        echo "<div class='error-exception-container exception-container'>";
        echo "<h1>" . get_class( $e ) . "</h1>";
        echo "'{$e->getMessage()}' in <b>{$e->getFile()}</b> ({$e->getLine()})\n";
        echo "<table class='error-table'>";
        echo "<tr>";
        echo "<thead>";
        echo "<th>ID</th>";
        echo "<th>File</th>";
        echo "<th>Line</th>";
        echo "<th>Function</th>";
        echo "<th>Args</th>";
        echo "</thead>";
        echo "</tr>";
        $i = 0;
        foreach ( $e->getTrace() as $id => $line ) {
            $fn = ( $line['class'] ?? '' ) . ( $line['type'] ?? '' ) . $line['function'];
            $file = $line['file'] ?? '';
            $ln = $line['line'] ?? '';
            echo "<tr>";
            echo "<td>#{$id}</td>";
            echo "<td>{$file}</td>";
            echo "<td>{$ln}</td>";
            echo "<td>{$fn}</td>";
            echo "<td>" . implode( "<br>", $line['args'] ?? [] ) . "</td>";
            echo "</tr>";
            $i = $id + 1;
        }
        echo "<tr>";
        echo "<td>#{$i}</td>";
        echo "<td>{$e->getFile()}</td>";
        echo "<td>{$e->getLine()}</td>";
        echo "<td>Main</td>";
        echo "<td></td>";
        echo "<tr>";
        echo "</table>";
        echo "</div>"; // exception-container
        $this->log_errors( [
            'error'   => get_class( $e ),
            'details' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'code'    => $e->getCode(),
            'trace'   => $e->getTraceAsString(),
        ] );
    }


    /**
     * Draw out a simple text error notice.
     * 
     * @param   int $error_number       The thrown error code.
     * @param   string  $error_string   The error string describing what happened.
     * @param   string  $error_file     The file in which the error occured.
     * @param   int $error_line         The line on which the error occured.
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function catch_error_text_inline( int $error_number, string $error_string, string $error_file, int $error_line ): void {
        echo "<b>{$this->error_title( $error_number )}</b>: {$error_string} in file <b>{$error_file}</b> on line <b>{$error_line}</b><br>";
        $this->log_errors( [
            'error'   => $error_number,
            'details' => $error_string,
            'file'    => $error_file,
            'line'    => $error_line,
            'code'    => $error_number,
        ] );
    }


    /**
     * Draw out a prettier exception notice and stack trace text.
     * 
     * @param   Throwable   $e  The error object.
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function text_inline( Throwable $e ): void {
        echo "<b>" . get_class( $e ) . "</b> '{$e->getMessage()}' in <b>{$e->getFile()}</b>({$e->getLine()})<br>";
        echo "<pre>Stack Trace:\n{$e->getTraceAsString()}</pre><br>";
        $this->log_errors( [
            'error'   => get_class( $e ),
            'details' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'code'    => $e->getCode(),
            'trace'   => $e->getTraceAsString(),
        ] );
    }


    /**
     * Does nothing, called to hide error messages for Errors.
     * 
     * @param   int $error_number       The thrown error code.
     * @param   string  $error_string   The error string describing what happened.
     * @param   string  $error_file     The file in which the error occured.
     * @param   int $error_line         The line on which the error occured.
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function hidden_error( int $error_number, string $error_string, string $error_file, int $error_line ): void {
        $this->log_errors( [
            'error'   => $error_number,
            'details' => $error_string,
            'file'    => $error_file,
            'line'    => $error_line,
            'code'    => $error_number,
        ] );
    }


    /**
     * Does nothing, called to hide error messages for Errors.
     * 
     * @param   Throwable   $e  The error object.
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function hidden_exception( Throwable $e ): void {
        $this->log_errors( [
            'error'   => get_class( $e ),
            'details' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'code'    => $e->getCode(),
            'trace'   => $e->getTraceAsString(),
        ] );
    }


    /**
     * Log any Errors and Exceptions that are thrown to file.
     * 
     * @param   array   $log_data   The data to log.
     * 
     * @access  private
     * @since   LBF 0.2.0-beta
     */

    private function log_errors( array $log_data ): void {
        if ( !$this->params['log_to_file'] ) {
            return;
        }
        $log_data['error'] = match( $log_data['error'] ) {
            E_ERROR             => 'E_ERROR',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_WARNING           => 'E_WARNING',
            E_PARSE             => 'E_PARSE',
            E_NOTICE            => 'E_NOTICE',
            E_STRICT            => 'E_STRICT',
            E_DEPRECATED        => 'E_DEPRECATED',
            E_CORE_ERROR        => 'E_CORE_ERROR',
            E_CORE_WARNING      => 'E_CORE_WARNING',
            E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
            E_USER_ERROR        => 'E_USER_ERROR',
            E_USER_WARNING      => 'E_USER_WARNING',
            E_USER_NOTICE       => 'E_USER_NOTICE',
            E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
            E_ALL               => 'E_ALL',
            default             => $log_data['error'],
        };

        if ( !isset( $log_data['trace'] ) ) {
            $log_data['trace'] = '';
        }

        $timestamp = date( 'Y-m-d H:i:s' );
        switch ( $this->log_type ) {
            case LogTypes::LOG:
                $log = "\n[{$timestamp}] - {$log_data['error']} | {$log_data['code']} | {$log_data['file']} | {$log_data['line']} | {$log_data['details']}";
                if ( $log_data['trace'] !== '' ) {
                    $log_data['trace'] = str_replace( "#", "\t#", $log_data['trace'] );
                    $log .= " | STACK TRACE:\n{$log_data['trace']}";
                }
                FileSystem::append_to_file( $this->log_file, $log );
                break;
            case LogTypes::HTML:
                $log_data['trace'] = '<pre>' . $log_data['trace'] . '</pre>';
                $new_log = "<tr>
        <td>{$timestamp}</td>
        <td>{$log_data['error']}</td>
        <td>{$log_data['details']}</td>
        <td>{$log_data['file']}</td>
        <td>{$log_data['line']}</td>
        <td>{$log_data['code']}</td>
        <td>{$log_data['trace']}</td>
    </tr>
</table>";
                $log = str_replace( '</table>', $new_log, file_get_contents( $this->log_file ) );
                FileSystem::write_file( $this->log_file, $log );
                break;
            case LogTypes::MD:
                $log_data['trace'] = str_replace( "\n", "<br>", $log_data['trace'] );
                $log_data['trace'] = str_replace( "#", "\#", $log_data['trace'] );
                $log = "\n| {$timestamp} | {$log_data['error']} | {$log_data['details']} | {$log_data['file']} | {$log_data['line']} | {$log_data['code']} | {$log_data['trace']} |";
                FileSystem::append_to_file( $this->log_file, $log );
                break;
            case LogTypes::JSON:
                $data = JSONTools::read_json_file_to_array( $this->log_file );
                $data[time()][] = $log_data;
                JSONTools::write_json_file( $this->log_file, $data );
                break;
        }
    }

}