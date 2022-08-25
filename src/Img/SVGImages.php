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
     * @since   3.28.0
     */
    case error401;
    /**
     * @since   3.28.0
     */
    case error403;
    /**
     * @since   3.28.0
     */
    case error404;

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
            self::error401    => __DIR__ . '/401.svg',
            self::error403    => __DIR__ . '/403.svg',
            self::error404    => __DIR__ . '/404.svg',
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
        return file_get_contents( $this->path() );
    }
}