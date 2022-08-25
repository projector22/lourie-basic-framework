<?php

namespace LBF\Img;

/**
 * Listed SVG image files.
 * 
 * use LBF\Img\SVGImages;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.28.0
 */

enum SVGImages {
    /**
     * @since   3.28.0
     */
    case maintenance;


    /**
     * Return the full path of the selected SVG file.
     * 
     * @return  string
     * 
     * @access  public
     * @since   3.28.0
     */

    public function path(): string {
        return match( $this ) {
            self::maintenance => __DIR__ . '/maintenance.svg',
        };
    }

    /**
     * Return the markup of the svg image.
     * 
     * @return  string
     * 
     * @access  public
     * @since   3.28.0
     */

    public function image(): string {
        return match( $this ) {
            self::maintenance => file_get_contents( self::maintenance->path() ),
        };
    }
}