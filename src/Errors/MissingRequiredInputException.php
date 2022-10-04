<?php

namespace LBF\Errors;

use LBF\Errors\Meta\ExceptionMeta;

/**
 * Error page for handling errors that occure when some kind required input is missing exception is thrown.
 * 
 * use LBF\Errors\MissingRequiredInputException;
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   LBF 0.1.4-beta
 * @since   LBF 0.2.0-beta  Moved __construct to abstract class `ExceptionMeta`.
 */

class MissingRequiredInputException extends ExceptionMeta {}