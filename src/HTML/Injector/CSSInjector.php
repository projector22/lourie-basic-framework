<?php

namespace LBF\HTML\Injector;

use LBF\Errors\Files\FileNotFound;
use LBF\HTML\Injector\InjectorMeta;
use LBF\HTML\Injector\PagePositions;

/**
 * The trait allows users to inject CSS into a webpage.
 * 
 * LBF\HTML\Injector\CSSInjector;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

trait CSSInjector {

    use InjectorMeta;


    /**
     * The styles to inject into the loaded page.
     * 
     * @var array   $injected_styles
     * 
     * @static
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private static array $injected_styles;


    /**
     * Insert a plain set of CSS into the webpage.
     * 
     * ```php
     * HTML::inject_css(<<<CSS
     *     .my-class {
     *         background-color: red;
     *         border: 1px solid black;
     *     }
     * CSS, PagePositions::TOP_OF_PAGE);
     * ```
     * 
     * @param   string          $styles     The style to inject.
     * @param   PagePositions   $position   The position on the page to inject these styles.
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function inject_css( string $styles, PagePositions $position = PagePositions::IN_HEAD ): void {
        if ( !isset( self::$injected_styles ) ) {
            self::$injected_styles = self::set_default_data();
        }
        self::$injected_styles[$position->id()]['raw'][] = $styles;
    }


    /**
     * Insert CSS from a CSS file into the webpage.
     * 
     * ```php
     * HTML::inject_css_file( __DIR__ . '/css/styles.css', PagePositions::TOP_OF_PAGE );
     * ```
     * 
     * @param   string          $file_path  The full path to the file to be included.
     * @param   PagePositions   $position   The position on the page to inject these styles.
     * 
     * @throws  FileNotFound    If the requested file path is invalid.
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function inject_css_file( string $file_path, PagePositions $position = PagePositions::IN_HEAD ): void {
        if ( !is_file( $file_path ) ) {
            throw new FileNotFound( "File {$file_path} does not exist." );
        }
        if ( !isset( self::$injected_styles ) ) {
            self::$injected_styles = self::set_default_data();
        }
        self::$injected_styles[$position->id()]['raw'][] = file_get_contents($file_path);
    }


    /**
     * Insert a CSS CDN into the website.
     * 
     * ```php
     * HTML::inject_css_cdn( 'https://example.com/css/style.css', PagePositions::TOP_OF_PAGE );
     * ```
     * 
     * @param   string          $file_path  The full path to the file to be included.
     * @param   PagePositions   $position   The position on the page to inject these styles.
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function inject_css_cdn( string $url, PagePositions $position = PagePositions::IN_HEAD ): void {
        if ( !isset( self::$injected_styles ) ) {
            self::$injected_styles = self::set_default_data();
        }
        self::$injected_styles[$position->id()]['cdn'][] = "<link rel='stylesheet' href='{$url}'>";
    }


    /**
     * Insert the CSS elements into the parsed part of the page.
     * 
     * @param   PagePositions   $position   The position to insert the styles.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function insert_css( PagePositions $position ): string {
        $css = '';
        if ( isset( self::$injected_styles ) ) {
            $raw = $this->remove_duplicates( self::$injected_styles[$position->id()]['raw'] );
            $insert = $this->merge( $raw );
            if ( $insert !== '' ) {
                $css .= "<style>{$insert}</style>";
            }
            $cdn = $this->remove_duplicates( self::$injected_styles[$position->id()]['cdn'] );
            $css .= $this->merge( $cdn );
        }
        return $css;
    }

}