<?php

namespace LBF\Tools\Errors;

/**
 * Enum for defining the different ways errors may be displayed.
 * 
 * @see LBF\Tools\Errors\ErrorExceptionHandler
 * 
 * use LBF\Tools\Errors\DrawError;
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   LBF 0.2.0-beta
 */

enum DrawError {

    /**
     * Draw the standard (default) error message.
     * 
     * @since   LBF 0.2.0-beta
     */

    case STANDARD;

    /**
     * Draw the error message inline, as a prettier text message.
     * 
     * @since   LBF 0.2.0-beta
     */

    case TEXT_INLINE;

    /**
     * Draw the error message inline, in a coloured box.
     * 
     * @since   LBF 0.2.0-beta
     */

    case PRETTY_INLINE;

    /**
     * Draw the error message inside a hidden bar, that can be extended and viewed after page render.
     * 
     * @todo    Build this one
     * 
     * @since   LBF 0.2.0-beta
     */

    case BAR;

    /**
     * Hide all Error and Exception messages.
     * 
     * @since   LBF 0.2.0-beta
     */

    case HIDDEN;
}
