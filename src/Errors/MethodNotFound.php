<?php

namespace LBF\Errors;

use LBF\Errors\Meta\ExceptionMeta;

/**
 * Error page for handling errors when a called class method does not exist.
 * 
 * use LBF\Errors\MethodNotFound;
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   LBF 0.1.6-beta
 * @since   LBF 0.2.0-beta  Moved __construct to abstract class `ExceptionMeta`.
 */

class MethodNotFound extends ExceptionMeta {}