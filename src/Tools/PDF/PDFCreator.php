<?php

namespace LBF\Tools\PDF;

use Exception;
use TCPDF_STATIC;
use LBF\Tools\PDF\PDFCreatorBackend;

/**
 * Generate a PDF document, by leavering into TCPDF.
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.6.0
 * @since   3.11.0  Moved to `Framework\Tools\PDF`.
 * @since   3.20.0  Completely revamped and renamed `PDFCreator`.
 * @since   3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                  Namespace changed from `Framework` to `LBF`.
 */

class PDFCreator extends PDFCreatorBackend {

    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @access  public
     * @since   3.6.0
     */

    public function __construct(
        /**
         * The author of the document, can be set as the account name
         * 
         * @var string  $author     Default: APP_NAME
         * 
         * @access  public
         * @since   3.6.0
         */

        public string $author,

        /**
         * The title of the page and document
         * 
         * @var string  $title
         * 
         * @access  public
         * @since   3.6.0
         */

        public string $title,

        /**
         * The document's subject
         * 
         * @var string|null  $subject    Default: Document
         * 
         * @access  public 
         * @since   3.6.0
         */

        public ?string $subject = null,

        /**
         * The name of the file that is generated
         * 
         * @var string|null  $file_name  Default: APP_NAME Report
         * 
         * @access  public
         * @since   3.6.0
         */

        public ?string $file_name = null,

        /**
         * Keywords attached to the document
         * 
         * @var string|null  $keywords   Default: APP_NAME
         * 
         * @access  public
         * @since   3.6.0
         */

        public ?string $keywords = null,
    ) {
        $this->set_date = date( 'Y-m-d' );
    }


    /**
     * Start the PDF document.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function start_pdf(): static {
        $this->construct_pdf();
        return $this;
    }


    /**
     * Close off the PDF document.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function end_pdf(): static {
        $this->generate_pdf();
        return $this;
    }


    /**
     * Insert a page break.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function new_page(): static {
        $this->pdf->AddPage( $this->orientation, $this->page_size );
        return $this;
    }


    /**
     * Add text or html content into the document.
     * 
     * @param   string  $text   The text to append.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function add_content( string $text ): static {
        $this->content .= $text;
        return $this;
    }


    /**
     * Insert a text block.
     * 
     * @param   string  $alignment  Options 'L', 'C', 'R', ''
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function insert_block(
        string $alignment = 'L',
    ): static {
        if ( !$this->detect_style_tag( $this->style ) ) {
            $this->style = $this->add_style_tags( $this->style );
        }

        $this->pdf->setCellHeightRatio( $this->line_height );

        $this->pdf->SetFont(
            family: $this->font_name,
            style: $this->font_style,
            size: $this->font_size,
            subset: true,
        );

        // set text shadow effect
        if ( $this->use_text_shadow ) {
            $this->pdf->setTextShadow( [
                'enabled'    => true,
                'depth_w'    => 0.2,
                'depth_h'    => 0.2,
                'color'      => [196,196,196],
                'opacity'    => 1,
                'blend_mode' => 'Normal'
            ] );
        }

        $this->pdf->writeHTML(
            html: $this->style . $this->content, 
            reseth: true, 
            align: $alignment,
        );
        $this->empty_content_styles();
        return $this;
    }


    /**
     * Set the style of the page.
     * 
     * @param   string  $style  HTML style including <style></style>
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function set_style( string $style ): static {
        if ( $this->detect_style_tag( $style ) ) {
            $this->style = $style;
        } else {
            $this->style .= $style;
        }
        return $this;
    }


    /**
     * Reset font details to default values
     * 
     * @access  public
     * @since   3.6.0
     */

    public function reset_font(): void {
        $this->font_name  = 'helvetica';
        $this->font_style = '';
        $this->font_size  = 14;
        $this->use_text_shadow = false;
    }


    /**
     * Reset page details to default values
     * 
     * @access  public
     * @since   3.6.0
     */

    public function reset_page(): void {
        $this->page_size   = PDF_PAGE_FORMAT;
        $this->orientation = PDF_PAGE_ORIENTATION;
    }


    /**
     * Enable or disable, and set a custom header and / or footer.
     * 
     * @param   boolean         $use_custom Whether or not to use a custom header and footer.
     * @param   integer|null    $header_id  The id of the header to be used.
     * @param   string|null     $footer_id  The id of the footer to be used.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function set_custom_header_footer( bool $use_custom, ?int $header_id = null, ?string $footer_id = null ): static {
        $this->use_custom_header_footer = $use_custom;
        if ( $use_custom ) {
            $this->set_header_type = is_null( $header_id ) ? 1 : $header_id; // Default 1 if not explicitly called.
            $this->set_footer_type = $footer_id; // Default null (not custom) if not explicitly called.
        }
        return $this;
    }


    /**
     * Enabled or disable the header.
     * 
     * @param   boolean $hide   Whether or not to hide the header.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function hide_header( bool $hide ): static {
        $this->include_header = !$hide;
        return $this;
    }


    /**
     * Enabled or disable the footer.
     * 
     * @param   boolean $hide   Whether or not to hide the footer.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function hide_footer( bool $hide ): static {
        $this->include_footer = !$hide;
        return $this;
    }


    /**
     * Set the default font size of the document, if not overwritten explicitly.
     * 
     * @param   integer $size   The size the font should be.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function set_font_size( int $size ): static {
        $this->font_size = $size;
        return $this;
    }


    /**
     * Set the orientation of the page to portrait or landscape.
     * 
     * ## Options
     * - PDFCreator::ORIENTATION_PORTRAIT
     * - PDFCreator::ORIENTATION_LANDSCAPE
     * 
     * @param   string  $orientation    The orientation of the page
     * 
     * @access  public
     * @since   3.20.0
     */

    public function set_page_orientation( string $orientation ): static {
        $allowed_orientations = [
            self::ORIENTATION_PORTRAIT,
            self::ORIENTATION_LANDSCAPE,
        ];
        if ( !in_array( $orientation, $allowed_orientations, true ) ) {
            throw new Exception( "Invalid orienatation" );
        }
        $this->orientation = $orientation;
        return $this;
    }


    /**
     * Set the size of the page.
     * 
     * Options: Almost any standard page size - A4, A3, LETTER etc.
     * 
     * @see tcpdf_static:$page_formats
     * 
     * @param   string  $size   Page size.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function set_page_size( string $size ): static {
        if ( !in_array( $size, TCPDF_STATIC::$page_formats, true ) ) {
            throw new Exception( "Invalid page size" );
        }
        $this->page_size = $size;
        return $this;
    }


    /**
     * Set a font style. The following options can be sent in combination.
     * 
     * @example - B : bold
     * @example - BI : bold & italics
     * 
     * ## Options
     * - '' (empty string) regular
     * - B: bold 
     * - I: italic, 
     * - U: underline, 
     * - D: line through,
     * - O: overline
     * 
     * @param   string  $style      The style of the text.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function set_font_style( string $style ): static {
        $this->font_style = $style;
        return $this;
    }


    /**
     * Set the font of the text.
     * 
     * @param   string  $font   The font to use.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function set_font( string $font ): static {
        $this->font_name = $font;
        return $this;
    }


    /**
     * Toggle on or off the text shadow.
     * 
     * @param   boolean $shadow     Whether or not to use a text shadow.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function set_text_shadow( bool $shadow ): static {
        $this->use_text_shadow = $shadow;
        return $this;
    }


    /**
     * Set the output method used by the document.
     * 
     * @param   integer $method     The output method to be used.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function set_output_method( int $method ): static {
        $available_methods = [
            self::OUTPUT_TO_SCREEN,
            self::OUTPUT_TO_DISK,
            self::OUTPUT_TO_EMAIL,
            self::OUTPUT_TO_DOWNLOAD,
        ];
        if ( !in_array( $method, $available_methods, true ) ) {
            throw new Exception( "Invalid output method." );
        }
        $this->output_method = $method;
        return $this;
    }


    /**
     * Set the path to save the PDF to.
     * 
     * @param   string  $path   The path to save the file to.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function set_save_path( string $path ): static {
        $this->save_path = $path;
        return $this;
    }


    /**
     * Empty the style and content properties.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.20.0
     */

    public function empty_content_styles(): static {
        $this->style = '';
        $this->content = '';
        return $this;
    }


    /**
     * Append text to the the custom footer text property.
     * 
     * @param   string  $text   The text to append.
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.21.0
     */

    public function add_custom_footer_text( string $text): static {
        $this->footer_custom_text .= $text;
        return $this;
    }


    /**
     * Set the page to use multi columns.
     * 
     * @param   boolean $use_multi_columns
     * @param   integer $number_of_columns  Default: 2
     * @param   integer $width              Default: 2
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.26.0
     */

    public function set_multi_columns( bool $use_multi_columns, int $number_of_columns = 2, int $width = 57 ): static {
        if ( $use_multi_columns ) {
            $this->pdf->setEqualColumns( $number_of_columns, $width );
        } else {
            $this->pdf->resetColumns();
        }
        return $this;
    }


    /**
     * Select the appropriate column.
     * 
     * @param   integer $column
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.26.0
     */

    public function select_column( ?int $column ): static {
        $this->pdf->selectColumn( $column );
        return $this;
    }


    /**
     * Set the line height of the text.
     * 
     * @param   float   $height
     * 
     * @return  static
     * 
     * @access  public
     * @since   3.28.0
     */

    public function set_line_height( float $height ): static {
        $this->line_height = $height;
        return $this;
    }

}