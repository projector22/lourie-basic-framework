<?php

namespace LBF\Errors;

use Throwable;
use LBF\Errors\DrawError;

/**
 * Class for creating custom ways of drawing out ERROR & EXCEPTION messages.
 * 
 * use LBF\Errors\ErrorExceptionHandler;
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
     * @since   0.2.0-beta
     */

    private string $css_path = __DIR__ . '/css/error.min.css';
    // private string $css_path = __DIR__ . '/css/error.css';

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
         * 
         * @access  private
         * @since   LBF 0.2.0-beta
         */

        private array $params = [] 
    ) {
        $this->is_cli = !isset( $_SERVER['REMOTE_ADDR'] );

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
     * @param   boolean $log    Whether to enable or disable logging.
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function set_log_to_file( bool $log ): void {
        $this->params['log_to_file'] = $log;
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

    public function catch_error_pretty( int $error_number, string $error_string, string $error_file, int $erorr_line ): void {
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
        echo "{$error_string} in file <b>{$error_file}</b> on line <b>{$erorr_line}</b>";
        echo "</div>"; // exception-container
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
            echo "<tr>";
            echo "<td>#{$id}</td>";
            echo "<td>{$line['file']}</td>";
            echo "<td>{$line['line']}</td>";
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

    public function catch_error_text_inline( int $error_number, string $error_string, string $error_file, int $erorr_line ): void {
        echo "<b>{$this->error_title( $error_number )}</b>: {$error_string} in file <b>{$error_file}</b> on line <b>{$erorr_line}</b><br>";
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
    }


    /**
     * Does nothing, called to hide error messages for Errors.
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function hidden_error(): void {}


    /**
     * Does nothing, called to hide error messages for Errors.
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function hidden_exception(): void {}

}