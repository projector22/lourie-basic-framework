<?php

namespace LBF\Tools\PDF;

use TCPDF;
use Exception;
use LBF\Errors\MethodNotFound;

class CustomHeaderFooter extends TCPDF {
    private array $header_types = [];

    private string $header_id;

    /**
     * This calls the TCPDF constructor.
     * 
     * @param   string  $orientation page orientation. Possible values are (case insensitive):<ul><li>P or Portrait (default)</li><li>L or Landscape</li><li>'' (empty string) for automatic orientation</li></ul>
     * @param   string  $unit        User measure unit. Possible values are:<ul><li>pt: point</li><li>mm: millimeter (default)</li><li>cm: centimeter</li><li>in: inch</li></ul><br />A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm). This is a very common unit in typography; font sizes are expressed in that unit.
     * @param   mixed   $format      The format used for pages. It can be either: one of the string values specified at getPageSizeFromFormat() or an array of parameters specified at setPageFormat().
     * @param   boolean $unicode     TRUE means that the input text is unicode (default = true)
     * @param   string  $encoding    Charset encoding (used only when converting back html entities); default is UTF-8.
     * @param   integer $pdfa        If not false, set the document to PDF/A mode and the good version (1 or 3).
     * 
     * @see getPageSizeFromFormat(), setPageFormat()
     * 
     * @access	public
     * @since	3.6.0
     */

    public function __construct( 
        string   $orientation = 'P',
        string   $unit        = 'mm',
        mixed    $format      = 'A4',
        bool     $unicode     = true,
        string   $encoding    = 'UTF-8',
        bool|int $pdfa        = false
    ) {
        parent::__construct( $orientation, $unit, $format, $unicode, $encoding, false, $pdfa );
    }


    public function set_header_types( array $list ): void {
        $this->header_types = $list;
    }

    public function append_header_type( string $item ): void {
        $this->header_types[] = $item;
    }


    public function set_custom_header( string $id ): bool {
        if ( !in_array( $id, $this->header_types ) ) {
            echo "<pre>";
            throw new Exception( "Header type does not exist.", 404 );
        }
        $this->header_id = $id;
    }

    public function Header(): void {
        // if ( !method_exists( $this, $this->header_id ) ) {
        //     echo "<pre>";
        //     throw new MethodNotFound( "Method {$this->header_id} does not exist.", 404 );
        // }
        // $id = $this->header_id;
        // $this->$id();

        /**
         * Template class = new Template class if file exists.
         * $tc->draw_header
         */
    }
}