<?php

namespace LBF\Tools\PDF\Enums;

enum OutputTo {

        /**
     * ENUM to indicate how to export the final result.
     * 
     * @var int  SCREEN    Output to the screen.
     * 
     * @access  public
     * @since   LRS 3.20.0
     */

    case SCREEN;

    /**
     * ENUM to indicate how to export the final result.
     * 
     * @var int  DISK  Output to the screen.
     * 
     * @access  public
     * @since   LRS 3.20.0
     */

    case DISK;

    /**
     * ENUM to indicate how to export the final result.
     * 
     * @var int  EMAIL Output to the screen.
     * 
     * @access  public
     * @since   LRS 3.20.0
     */

    case EMAIL;

    /**
     * ENUM to indicate how to export the final result.
     * 
     * @var int  DOWNLOAD  Output to the screen.
     * 
     * @access  public
     * @since   LRS 3.20.0
     */

    case DOWNLOAD;
}