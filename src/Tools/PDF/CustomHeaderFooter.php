<?php

namespace LBF\Tools\PDF;

use TCPDF;
use TCPDF_IMAGES;
use LBF\Errors\ClassNotFound;


class CustomHeaderFooter extends TCPDF {
     /**
     * String to ID the custom header
     * 
     * @var string	$header_type	Default: null
     * 
     * @access	public
     * @since	3.6.0
     */

    public ?string $header_type = null;

    /**
     * String to ID the custom footer
     * 
     * @var string	$footer_type	Default: null
     * 
     * @access	public
     * @since	3.6.0
     */

    public ?string $footer_type = null;

    /**
     * Custom header text to apply
     * 
     * @var string  $header_custom_text		Default: ''
     * 
     * @access	public
     * @since   3.6.3
     */

    public string $header_custom_text = '';

    /**
     * Custom footer text to apply
     * 
     * @var string  $footer_custom_text		Default: ''
     * 
     * @access	public
     * @since   3.6.3
     */

    public string $footer_custom_text = '';

    /**
     * The date on the report
     * 
     * @var string	$set_date	Default: date( 'Y-m-d' )
     * 
     * @access	public
     * @since	3.6.0
     */

    public string $set_date;


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


    /**
     * Draw out the default header taken directly from TCPDF
     * 
     * This method is used to render the page header.
     * It is automatically called by AddPage() and could be overwritten in your own inherited class.
     * 
     * @access	public
     * @since	3.6.0
     * @since	3.18.0	Renamed version of the origonal TCPDF class Header with a few minor tweaks (removing of K_PATH_IMAGES.)
     */

    public function default_header(): void {
        if ($this->header_xobjid === false) {
            // start a new XObject Template
            $this->header_xobjid = $this->startTemplate($this->w, $this->tMargin);
            $headerfont = $this->getHeaderFont();
            $headerdata = $this->getHeaderData();
            $this->y = $this->header_margin;
            if ($this->rtl) {
                $this->x = $this->w - $this->original_rMargin;
            } else {
                $this->x = $this->original_lMargin;
            }
            if (($headerdata['logo']) AND ($headerdata['logo'] != K_BLANK_IMAGE)) {
                $imgtype = TCPDF_IMAGES::getImageFileType($headerdata['logo']);
                if (($imgtype == 'eps') OR ($imgtype == 'ai')) {
                    $this->ImageEps($headerdata['logo'], '', '', $headerdata['logo_width']);
                } elseif ($imgtype == 'svg') {
                    $this->ImageSVG($headerdata['logo'], '', '', $headerdata['logo_width']);
                } else {
                    $this->Image($headerdata['logo'], '', '', $headerdata['logo_width']);
                }
                $imgy = $this->getImageRBY();
            } else {
                $imgy = $this->y;
            }
            $cell_height = $this->getCellHeight($headerfont[2] / $this->k);
            // set starting margin for text data cell
            if ($this->getRTL()) {
                $header_x = $this->original_rMargin + ($headerdata['logo_width'] * 1.1);
            } else {
                $header_x = $this->original_lMargin + ($headerdata['logo_width'] * 1.1);
            }
            $cw = $this->w - $this->original_lMargin - $this->original_rMargin - ($headerdata['logo_width'] * 1.1);
            $this->SetTextColorArray($this->header_text_color);
            // header title
            $this->SetFont($headerfont[0], 'B', $headerfont[2] + 1);
            $this->SetX($header_x);
            $this->Cell($cw, $cell_height, $headerdata['title'], 0, 1, '', 0, '', 0);
            // header string
            $this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
            $this->SetX($header_x);
            $this->MultiCell($cw, $cell_height, $headerdata['string'], 0, '', 0, 1, '', '', true, 0, false, true, 0, 'T', false);
            // print an ending header line
            $this->SetLineStyle(array('width' => 0.85 / $this->k, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $headerdata['line_color']));
            $this->SetY((2.835 / $this->k) + max($imgy, $this->y));
            if ($this->rtl) {
                $this->SetX($this->original_rMargin);
            } else {
                $this->SetX($this->original_lMargin);
            }
            $this->Cell(($this->w - $this->original_lMargin - $this->original_rMargin), 0, '', 'T', 0, 'C');
            $this->endTemplate();
        }
        // print header template
        $x = 0;
        $dx = 0;
        if (!$this->header_xobj_autoreset AND $this->booklet AND (($this->page % 2) == 0)) {
            // adjust margins for booklet mode
            $dx = ($this->original_lMargin - $this->original_rMargin);
        }
        if ($this->rtl) {
            $x = $this->w + $dx;
        } else {
            $x = 0 + $dx;
        }
        $this->printTemplate($this->header_xobjid, $x, 0, 0, 0, '', '', false);
        if ($this->header_xobj_autoreset) {
            // reset header xobject template at each page
            $this->header_xobjid = false;
        }
    }


    /**
     * Replacement Header method
     * 
     * @access	public
     * @since   3.6.0
     */

    public function Header(): void {
        $class = $this->header_type;
        if ( $class == 'default' ) {
            $this->default_header();
        } else if ( !class_exists( $class ) ) {
            echo "<pre>";
            throw new ClassNotFound( "Specified Class {$class} does not exist." );
        } else {
            $header = new $class( $this );
            $header->set_header();
        }
    }


    /**
     * Replacement Footer method
     * 
     * @access	public
     * @since	3.6.0
     */

    public function Footer(): void {
        $class = $this->footer_type;
        if ( $class == 'default' ) {
            $this->default_footer();
        } else if ( !class_exists( $class ) ) {
            echo "<pre>";
            throw new ClassNotFound( "Specified Class {$class} does not exist." );
        } else {
            $header = new $class( $this );
            $header->set_header();
        }
    }


    /**
     * Draw out the default footer taken directly from TCPDF
     * 
     * @access	private
     * @since	3.6.0
     */

    private function default_footer(): void {
        $cur_y = $this->y;
        $this->SetTextColorArray( $this->footer_text_color );
        // set style for cell border
        $line_width = ( 0.85 / $this->k );
        $this->SetLineStyle( array( 'width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->footer_line_color ) );
        // print document barcode
        $barcode = $this->getBarcode();
        if ( !empty( $barcode ) ) {
            $this->Ln( $line_width );
            $barcode_width = round( ( $this->w - $this->original_lMargin - $this->original_rMargin ) / 3 );
            $style = array( 
                'position' => $this->rtl?'R':'L',
                'align' => $this->rtl?'R':'L',
                'stretch' => false,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'padding' => 0,
                'fgcolor' => array( 0,0,0 ),
                'bgcolor' => false,
                'text' => false
             );
            $this->write1DBarcode( $barcode, 'C128', '', $cur_y + $line_width, '', ( ( $this->footer_margin / 3 ) - $line_width ), 0.3, $style, '' );
        }
        $w_page = isset( $this->l['w_page'] ) ? $this->l['w_page'] . ' ' : '';
        if ( empty( $this->pagegroups ) ) {
            $pagenumtxt = $w_page . $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages();
        } else {
            $pagenumtxt = $w_page . $this->getPageNumGroupAlias() . ' / ' . $this->getPageGroupAlias();
        }
        $this->SetY( $cur_y );
        // Print page number
        if ( $this->getRTL() ) {
            $this->SetX( $this->original_rMargin );
            $this->Cell( 0, 0, $pagenumtxt, 'T', 0, 'L' );
        } else {
            $this->SetX( $this->original_lMargin );
            $this->Cell( 0, 0, $this->getAliasRightShift() . $pagenumtxt, 'T', 0, 'R' );
        }
    }

}