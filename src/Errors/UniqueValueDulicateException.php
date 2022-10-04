<?php

namespace LBF\Errors;

use LBF\Errors\Meta\ExceptionMeta;

/**
 * Error page for handling errors that occure when a duplicate of a unique value is trying to be set.
 * 
 * use LBF\Errors\UniqueValueDulicateException;
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   LBF 0.1.11-beta
 * @since   LBF 0.2.0-beta  Moved __construct to abstract class `ExceptionMeta`.
 */

class UniqueValueDulicateException extends ExceptionMeta {}