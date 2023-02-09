<?php

namespace LBF\HTML\Injector;

/**
 * The possible places on the page a bit of data may be injected.
 * 
 * LBF\HTML\Injector\PagePositions;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

enum PagePositions {
    case IN_HEAD;
    case TOP_OF_PAGE;
    case BOTTOM_OF_PAGE;


    /**
     * Return the id of the selected position.
     * 
     * @return  integer
     * @since   LBF 0.6.0-beta
     */

    public function id(): int {
        return match( $this ) {
            self::IN_HEAD        => 0,
            self::TOP_OF_PAGE    => 1,
            self::BOTTOM_OF_PAGE => 2,
        };
    }

}