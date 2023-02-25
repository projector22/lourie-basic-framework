<?php

namespace LBF\HTML\Injector;

use LBF\Errors\Files\FileNotFound;
use LBF\HTML\Injector\InjectorMeta;
use LBF\HTML\Injector\PagePositions;

/**
 * The trait allows users to inject Javascript into a webpage.
 * 
 * LBF\HTML\Injector\JSInjector;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

trait JSInjector {

    use InjectorMeta;


    /**
     * The javascript to inject into the loaded page.
     * 
     * @var array   $injected_js
     * 
     * @static
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private static array $injected_js;


    /**
     * Insert a plain set of Javascript into the webpage.
     * 
     * ```php
     * HTML::inject_js(<<<JS
     *      document.getElementById('element').innerHTML = '<h1>Hey</h1>';
     * JS, PagePositions::BOTTOM_OF_PAGE);
     * ```
     * 
     * @param   string          $js         The style to inject.
     * @param   PagePositions   $position   The position on the page to inject these js.
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function inject_js( string $js, PagePositions $position = PagePositions::IN_HEAD ): void {
        if ( !isset( self::$injected_js ) ) {
            self::$injected_js = self::set_default_data();
        }
        self::$injected_js[$position->id()]['raw'][] = $js;
    }


    /**
     * Insert Javascript from a Javascript file into the webpage.
     * 
     * ```php
     * HTML::inject_js_file( __DIR__ . '/js/scripts.js', PagePositions::BOTTOM_OF_PAGE );
     * ```
     * 
     * @param   string          $file_path  The full path to the file to be included.
     * @param   PagePositions   $position   The position on the page to inject these js.
     * 
     * @throws  FileNotFound    If the requested file path is invalid.
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function inject_js_file( string $file_path, PagePositions $position = PagePositions::IN_HEAD ): void {
        if ( !is_file( $file_path ) ) {
            throw new FileNotFound( "File {$file_path} does not exist." );
        }
        if ( !isset( self::$injected_js ) ) {
            self::$injected_js = self::set_default_data();
        }
        self::$injected_js[$position->id()]['raw'][] = file_get_contents($file_path);   
    }


    /**
     * Insert a Javascript CDN into the website.
     * 
     * ```php
     * HTML::inject_js_cdn( 'https://cdn.jsdelivr.net/npm/example', PagePositions::TOP_OF_PAGE );
     * ```
     * 
     * @param   string          $file_path  The full path to the file to be included.
     * @param   PagePositions   $position   The position on the page to inject these styles.
     * @param   boolean         $async      Whether or not to load the CDN as async. Default: false.
     * @param   boolean         $defer      Whether or not to load the CDN as defer. Default: false.
     * @param   boolean         $module     Whether or not to load the CDN as module. Default: false.
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function inject_js_cdn( string $url, PagePositions $position = PagePositions::IN_HEAD, bool $async = false, bool $defer = false, bool $module = false ): void {
        if ( !isset( self::$injected_js ) ) {
            self::$injected_js = self::set_default_data();
        }
        $insert_async = $insert_defer = $insert_module = '';
        if ( $async ) {
            $insert_async = ' async';
        }
        if ( $defer ) {
            $insert_defer = ' defer';
        }
        if ( $module ) {
            $insert_module = " type='module'";
        }
        self::$injected_js[$position->id()]['cdn'][] = "<script src='{$url}'{$insert_async}{$insert_defer}{$insert_module}></script>";
    }


    /**
     * Insert the Javascript elements into the parsed part of the page.
     * 
     * @param   PagePositions   $position   The position to insert the styles.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

     public function insert_js( PagePositions $position ): string {
        $js = '';
        if ( isset( self::$injected_js ) ) {
            $raw = $this->remove_duplicates( self::$injected_js[$position->id()]['raw'] );
            $insert = $this->merge( $raw );
            if ( $insert !== '' ) {
                $js .= "<script type='module'>{$insert}</script>";
            }
            $cdn = $this->remove_duplicates( self::$injected_js[$position->id()]['cdn'] );
            $js .= $this->merge( $cdn );
        }
        return $js;
    }

}