<?php

namespace LBF\HTML\Injector;

/**
 * The trait has common methods used by the CSS & JS injectors.
 * 
 * LBF\HTML\Injector\InjectorMeta;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

trait InjectorMeta {


    /**
     * Remove any duplicates in the array of data parsed.
     * 
     * @param   array   $data   List of styles / js.
     * 
     * @return  array
     * 
     * @access  public
     * @since   0.6.0-beta
     */

    public function remove_duplicates( array $data ): array {
        return array_unique( $data );
    }
}