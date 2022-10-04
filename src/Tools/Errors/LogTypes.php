<?php

namespace LBF\Tools\Errors;

/**
 * Enum for defining the different ways errors may be logged.
 * 
 * @see LBF\Tools\Errors\ErrorExceptionHandler
 * 
 * use LBF\Tools\Errors\LogTypes;
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   LBF 0.2.0-beta
 */

enum LogTypes {
    /**
     * Log as a standard .log file.
     * 
     * @since   LBF 0.2.0-beta
     */
    case LOG;
    /**
     * Log as a .html file.
     * 
     * @since   LBF 0.2.0-beta
     */
    case HTML;
    /**
     * Log as a markdown .md file.
     * 
     * @since   LBF 0.2.0-beta
     */
    case MD;
    /**
     * Log as a .json file.
     * 
     * @since   LBF 0.2.0-beta
     */
    case JSON;
}