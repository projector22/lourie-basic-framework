<?php

namespace LBF\Errors\Meta;

use Exception;
use Throwable;
use LBF\Errors\Meta\ExceptionInterface;

/**
 * Abstract Meta class for implimenting custom Exceptions.
 * 
 * use LBF\Errors\Meta\ExceptionMeta;
 * 
 * @see https://www.php.net/manual/en/language.exceptions.php
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   LBF 0.2.0-beta
 */

abstract class ExceptionMeta extends Exception implements ExceptionInterface {

    /**
     * @inheritdoc
     * 
     * @access  protected
     * @since   LBF 0.2.0-beta
     */
    
    protected $message = "Unknown Exception"; // Exception message
    
    /**
     * @inheritdoc
     * 
     * @access  protected
     * @since   LBF 0.2.0-beta
     */
    
    protected $code = 0;                      // User-defined exception code
    
    /**
     * @inheritdoc
     * 
     * @access  protected
     * @since   LBF 0.2.0-beta
     */
    
    protected string $file;                   // Source filename of exception
    
    /**
     * @inheritdoc
     * 
     * @access  protected
     * @since   LBF 0.2.0-beta
     */
    
    protected int $line;                      // Source line of exception


    /**
     * Class constructor.
     * 
     * @param   string          $message — [optional] The Exception message to throw.
     * @param   int             $code — [optional] The Exception code.
     * @param   Throwable|null  $previous [optional] The previous throwable used for the exception chaining.
     * 
     * @return  never
     * 
     * @inheritdoc
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function __construct( $message, $code = 0, ?Throwable $previous = null ) {
        parent::__construct( $message, $code, $previous );
    }


    /**
     * Render a basic error message if the class is echo'd directly.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */
    public function __toString() {
        return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n"
                                . "{$this->getTraceAsString()}";
    }


    /**
     * Return the error data as a JSON string.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.2.0-beta
     */

    public function as_json(): string {
        return json_encode( [
            'message' => $this->message,
            'code'    => $this->code,
            'file'    => $this->file,
            'line'    => $this->line,
            'trace'   => $this->getTrace(),
        ] );
    }
}