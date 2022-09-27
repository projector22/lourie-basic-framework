<?php

namespace LBF\Errors;

use Exception;
use Throwable;

/**
 * Error page for handling errors that occure when a duplicate of a unique value is trying to be set.
 * 
 * use LBF\Errors\UniqueValueDulicateException;
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   LBF 0.1.11-beta
 */

class UniqueValueDulicateException extends Exception {

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
     * @since   LBF 0.1.4-beta
     */

    public function __construct( $message, $code = 0, ?Throwable $previous = null ) {
        parent::__construct( $message, $code, $previous );
    }
}