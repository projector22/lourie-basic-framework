<?php

namespace LBF\Tools\PDF\Enums;

enum PageOrientation {
    /**
     * Set page orientation to portrait.
     * 
     * @var string  ORIENTATION_PORTRAIT
     * 
     * @access  public
     * @since   LRS 3.20.0
     */
    
    case PORTRAIT;


    /**
     * Set page orientation to portrait.
     * 
     * @var string  ORIENTATION_LANDSCAPE
     * 
     * @access  public
     * @since   LRS 3.20.0
     */
    
    case LANDSCAPE;

    public function value(): string {
        return match ( $this ) {
            self::PORTRAIT => 'P',
            self::LANDSCAPE => 'L',
        };
    }
}