<?php

namespace LBF\Errors\Meta;

use Throwable;

/**
 * Interface for all custom exception classes.
 * 
 * use LBF\Errors\Meta\ExceptionInterface;
 * 
 * @see https://www.php.net/manual/en/language.exceptions.php
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   LBF 0.2.0-beta
 */

interface ExceptionInterface {
    /* Protected methods inherited from Exception class */
    public function getMessage();                 // Exception message
    public function getCode();                    // User-defined Exception code
    public function getFile();                    // Source filename
    public function getLine();                    // Source line
    public function getTrace();                   // An array of the backtrace()
    public function getTraceAsString();           // Formated string of trace
   
    /* Overrideable methods inherited from Exception class */
    public function __toString();                 // formated string for display
    public function __construct( $message, $code = 0, ?Throwable $previous = null );

    /* Custom Tools */
    public function as_json(): string;
}