<?php

namespace LBF\Img;

/**
 * Listed SVG image files.
 * 
 * use LBF\Img\SVGImages;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.28.0
 */

enum SVGImages {
    /**
     * @since   LRS 3.28.0
     */
    case maintenance;
    /**
     * @since   LRS 3.28.0
     */
    case error401;
    /**
     * @since   LRS 3.28.0
     */
    case error403;
    /**
     * @since   LRS 3.28.0
     */
    case error404;
    /**
     * @since   LRS 3.6.5
     */
    case clippy;
    /**
     * @since   LRS 3.15.0
     */
    case grabber;
    /**
     * @since   LRS 3.9.1
     */
    case content_draw_arrow;

    /**
     * Return the full path of the selected SVG file.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LRS 3.28.0
     */

    public function path(): string {
        return match ($this) {
            self::maintenance        => __DIR__ . '/maintenance.svg',
            self::error401           => __DIR__ . '/401.svg',
            self::error403           => __DIR__ . '/403.svg',
            self::error404           => __DIR__ . '/404.svg',
            self::clippy             => __DIR__ . '/clippy.svg',
            self::grabber            => __DIR__ . '/grabber.svg',
            self::content_draw_arrow => __DIR__ . '/triangle-arrow-right.svg',
        };
    }

    /**
     * Return the markup of the svg image.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LRS 3.28.0
     */

    public function image(): string {
        return file_get_contents($this->path());
    }
}
