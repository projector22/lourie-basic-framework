<?php

namespace LBF\Errors;

use LBF\Errors\Meta\ExceptionMeta;

/**
 * Error page for handling errors when a file is not found.
 * 
 * use LBF\Errors\FileNotFoundError;
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   LBF 0.1.4-beta
 * @since   LBF 0.2.0-beta  Moved __construct to abstract class `ExceptionMeta`.
 */

class FileNotFoundError extends ExceptionMeta {}