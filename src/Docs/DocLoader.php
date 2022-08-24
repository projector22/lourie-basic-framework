<?php

namespace LBS\Docs;

use Parsedown;
use LBS\HTML\Draw;
use LBS\HTML\HTML;

/**
 * This class is to autoload markdown files from the /docs folder.
 * 
 * use LBS\Docs\DocLoader;
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * 
 * @since   3.17.0
 */

class DocLoader {

    /**
     * The path of the markdown file. Generated from the parsed URI
     * 
     * @var string  $markdown_file
     * 
     * @access  private
     * @since   3.17.0
     */

    private string $markdown_file;


    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @access  public
     * @since   3.17.0
     */

    public function __construct() {
        if ( $_SERVER['REQUEST_URI'] == '/help' ) {
            $this->markdown_file = 'docs/index.md';
        } else if ( $_SERVER['REQUEST_URI'] == '/help/changelog' ) {
            $this->markdown_file = 'changelog.md';
        } else {
            $this->markdown_file = 'docs' . str_replace( '/help', '', $_SERVER['REQUEST_URI'] ) . '.md';
        }
        if ( !file_exists( $this->markdown_file ) ) {
            $this->markdown_file = 'docs/index.md';
        }
    }
 

    /**
     * Load the markdown file and put it in the context of a page.
     * 
     * @access  public
     * @since   3.17.0
     */
    
    public function construct_page(): void {
        $md = new Parsedown;
        
        HTML::div( ['class' => 'help_container'] );
        echo $md->text( file_get_contents( $this->markdown_file ) );
        Draw::lines( 3 );
        $timestamp = date( 'l, d F Y, H:i', @filemtime( $this->markdown_file ) ?? time() );
        echo "<i>This page was last edited on: {$timestamp}</i>";
        HTML::close_div();
    }

}