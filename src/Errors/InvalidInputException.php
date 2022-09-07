<?php

namespace LBF\Errors;

use Exception;
use Throwable;

/**
 * Error page for handling errors that occure when some kind of invalid input exception is thrown.
 * 
 * use LBF\Errors\InvalidInputException;
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   0.1.4-beta
 */

class InvalidInputException extends Exception {

    /**
     * Class constructor.
     * 
     * @param   string          $message — [optional] The Exception message to throw.
     * @param   int             $code — [optional] The Exception code.
     * @param   Throwable|null  $previous [optional] The previous throwable used for the exception chaining.
     * 
     * @return  never
     * 
     * @access  public
     * @since   0.1.0-alpha
     */

    public function __construct( $message, $code = 0, ?Throwable $previous = null ) {
        parent::__construct( $message, $code, $previous );
    }
}