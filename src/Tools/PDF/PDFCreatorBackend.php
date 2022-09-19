<?php

namespace LBF\Tools\PDF;

use TCPDF;
use Exception;
use App\Templates\PDF\CustomHeaderFooter;

/**
 * Class to interface with TCPDF and to simplify the generation of PDFs.
 * These are the backend tools which are not meant to be called in public.
 * 
 * @link    https://tcpdf.org/ Check the link for documentation in using TCPDF
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.20.0
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                      Namespace changed from `Framework` to `LBF`.
 * 
 * @todo    Figure out a way of allowing better custom headers and footers integeration without calling from the `App` namespace here.
 */

class PDFCreatorBackend {

    /**
     * The variable assigned to the new TCPDF object
     * 
     * @var object  $pdf
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected object $pdf;

    /**
     * @var string  $content    HTML content that is to be placed in the document
     *                          The main body of the PDF
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected string $content = '';

    /**
     * Whether or not to include the header on the document
     * 
     * @var boolean $include_header     Default: true
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected bool $include_header = true;

    /**
     * Whether or not to include the footer on the document
     * 
     * @var boolean $include_footer     Default: true
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected bool $include_footer = true;

    /**
     * The name of the desired font.
     * 
     * @var string  $font_name      Default: helvetica
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected string $font_name = 'helvetica';

    /**
     * The style of the font, using a combination of the options below
     * 
     * Options: 
     * - '' (empty string) regular
     * - B: bold 
     * - I: italic, 
     * - U: underline, 
     * - D: line through,
     * - O: overline
     * 
     * @var string  $font_style     Default: ''
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected string $font_style = '';

    /**
     * Size of the font
     * 
     * @var integer $font_size      Default: 14
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected int $font_size = 14;

    /**
     * Page size to generate
     * 
     * Options: A3, A4, A5, A6, A7 etc
     * 
     * @var string  $page_size      Default: A4 (PDF_PAGE_FORMAT)
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected string $page_size = 'A4';

    
    /**
     * Set page orientation to portrait.
     * 
     * @var string  ORIENTATION_PORTRAIT
     * 
     * @access  public
     * @since   LRS 3.20.0
     */
    
    const ORIENTATION_PORTRAIT = 'P';


    /**
     * Set page orientation to portrait.
     * 
     * @var string  ORIENTATION_LANDSCAPE
     * 
     * @access  public
     * @since   LRS 3.20.0
     */
    
    const ORIENTATION_LANDSCAPE = 'L';

    /**
     * Page orientation
     * 
     * Options: 
     * - P: Portait
     * - L: Landscape
     * 
     * @var string  $orientation    Default: P (PDF_PAGE_ORIENTATION)
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected string $orientation = self::ORIENTATION_PORTRAIT;

    /**
     * Whether to draw the text shadow
     * 
     * @var boolean $use_text_shadow    Default: false
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected bool $use_text_shadow = false;

    /**
     * Whether or not to draw the school letterhead
     * 
     * @var boolean $use_custom_header_footer   Default: true
     * 
     * @access  protected
     * @since   LRS 3.6.0
     * 
     * @deprecated  LBF 0.2.0-beta
     */

    protected bool $use_custom_header_footer = true;

    /**
     * ENUM to indicate how to export the final result.
     * 
     * @var int  OUTPUT_TO_SCREEN    Output to the screen.
     * 
     * @access  public
     * @since   LRS 3.20.0
     */

    const OUTPUT_TO_SCREEN = 0;

    /**
     * ENUM to indicate how to export the final result.
     * 
     * @var int  OUTPUT_TO_DISK  Output to the screen.
     * 
     * @access  public
     * @since   LRS 3.20.0
     */

    const OUTPUT_TO_DISK = 1;

    /**
     * ENUM to indicate how to export the final result.
     * 
     * @var int  OUTPUT_TO_EMAIL Output to the screen.
     * 
     * @access  public
     * @since   LRS 3.20.0
     */

    const OUTPUT_TO_EMAIL = 2;

    /**
     * ENUM to indicate how to export the final result.
     * 
     * @var int  OUTPUT_TO_DOWNLOAD  Output to the screen.
     * 
     * @access  public
     * @since   LRS 3.20.0
     */

    const OUTPUT_TO_DOWNLOAD = 3;

    /**
     * Indicate what method of output should be used.
     * 
     * @var integer $output_method  
     * 
     * ## Options
     * - self::OUTPUT_TO_SCREEN
     * - self::OUTPUT_TO_DISK
     * - self::OUTPUT_TO_EMAIL
     * - self::OUTPUT_TO_DOWNLOAD
     * 
     * @access  protected
     * @since   LRS 3.20.0
     */

    protected int $output_method = self::OUTPUT_TO_SCREEN;

    /**
     * Where to save the file by default.
     * 
     * @var string  $save_path
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected string $save_path;

    /**
	 * ID of the custom header
     * 
     * @var integer|null	$header_type	Default: null
	 * 
     * @access  protected
	 * @since   LRS 3.6.0
	 */

	protected ?int $header_type;

    /**
	 * ID of the custom footer
     * 
     * @var string|null	$footer_type	Default: null
	 * 
     * @access  protected
	 * @since   LRS 3.6.0
	 */

    protected ?string $footer_type;

    /**
	 * The date on the report
     * 
     * @var string	$set_date	Default: date( 'Y-m-d' )
	 * 
     * @access  protected
	 * @since   LRS 3.6.0
	 */

	protected string $set_date;

    /**
     * Custom header text to apply
     * 
     * @var string  $header_custom_text
     * 
     * @access  public
     * @since   LRS 3.6.3
     */

    public string $header_custom_text = '';

    /**
     * Custom footer text to apply
     * 
     * @var string  $footer_custom_text
     * 
     * @access  public
     * @since   LRS 3.6.3
     */

    public string $footer_custom_text = '';

    /**
     * Contains the custom styles for the page.
     * 
     * @var string  $style  CSS styles between <style></style> tags
     * 
     * @access  protected
     * @since   LRS 3.20.0
     */

    protected string $style = '';

    /**
     * Contains the line height ratio of the text.
     * 
     * @var float   $line_height    Default: 1.25
     * 
     * @access  protected
     * @since   LRS 3.28.0
     */

    protected float $line_height = 1.25;


    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @access  public
     * @since   LRS 3.6.0
     */

    public function __construct() {
        throw new Exception( "You may not invoke this class directly. It is extented to LBF\Tools\PDF\PDFCreator" );
    }


    /**
     * Construct all the aspects together to generate the PDF
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected function construct_pdf(): void {
        if ( isset( $this->header_type ) || isset( $this->footer_type ) ) {
            $this->pdf = new CustomHeaderFooter( 
                orientation: $this->orientation, 
                unit: PDF_UNIT, 
                format: $this->page_size, 
                unicode: true, 
                encoding: 'UTF-8'
            );
            $this->pdf->header_type = $this->header_type ?? null;
            $this->pdf->footer_type = $this->footer_type ?? null;
            $this->pdf->header_custom_text = $this->header_custom_text;
            $this->pdf->footer_custom_text = $this->footer_custom_text;
            $this->pdf->set_date = $this->set_date;
        } else {
            // create new PDF document
            $this->pdf = new TCPDF( 
                orientation: $this->orientation, 
                unit: PDF_UNIT, 
                format: $this->page_size, 
                unicode: true, 
                encoding: 'UTF-8'
            );
        }

        // set document information
        $this->pdf->SetCreator( PDF_CREATOR );
        $this->pdf->SetAuthor( $this->author ?? '' );
        $this->pdf->SetTitle( $this->title ?? '' );
        $this->pdf->SetSubject( $this->subject ?? '' );
        $this->pdf->SetKeywords( $this->keywords ?? '' );

        // Header
        if ( is_null ( $this->header_type ?? null ) ) {
            if ( $this->include_header ) {
                $logo = '';
                if ( is_file ( site_logo() ) ) {
                    $logo = HOME_PATH . site_logo() ;
                }
                // Set PDF Data, Font & Margin header
                $this->pdf->SetHeaderData( $logo, 10, SCHOOL_NAME, $this->title, [0,0,0], [0,64,128] );
                $this->pdf->setHeaderFont( [PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN] );
                $this->pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
            } else {
                // Disable header
                $this->pdf->setPrintHeader( false );
            }
        }

        // Footer
        if ( is_null ( $this->footer_type ?? null  ) || $this->footer_type = 'footer_with_date' ) {
            if ( $this->include_footer ) {
                // Set PDF Data, Font & Margin footer
                $this->pdf->setFooterData( [0,64,0], [0,64,128] );
                $this->pdf->setFooterFont( [PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA] );
                $this->pdf->SetFooterMargin( PDF_MARGIN_FOOTER );
            } else {
                // Disable footer
                $this->pdf->setPrintFooter( false );
            }
        }

        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

        // set margins
        if ( $this->header_type == 2 || $this->header_type == 3 || $this->header_type == 4  ) {
            $this->pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
            $this->pdf->SetMargins( PDF_MARGIN_LEFT + 10, 60, PDF_MARGIN_RIGHT + 10 ); // Left, Top, Right
        } else {
            $this->pdf->SetMargins( PDF_MARGIN_LEFT + 10, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT + 10 );
        }

        // set auto page breaks
        $this->pdf->SetAutoPageBreak( TRUE, PDF_MARGIN_BOTTOM );

        // set image scale factor
        $this->pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );

        // set some language-dependent strings ( optional )
        if ( @file_exists( dirname( __FILE__ ) . '/lang/eng.php' ) ) {
            require_once( dirname( __FILE__ ) . '/lang/eng.php' );
            $this->pdf->setLanguageArray( $l );
        }

        // set default font subsetting mode
        $this->pdf->setFontSubsetting( true );
    }


    /**
     * Generate the PDF file and display the file, usually the last method to call.
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected function generate_pdf(): void {
        if ( $this->missing_parameters() ) {
            echo "An error has occured: Parameters not set";
            return;
        }

        // This seems to be required to flush and product the PDF - Gp
        ob_end_clean();

        // Ensure the file name has a trailing '.pdf'
        if ( !str_contains( $this->file_name ?? '', '.pdf' ) ) {
            $this->file_name .= '.pdf';
        }

        // Close and output PDF document
        switch ( $this->output_method ) {
            case self::OUTPUT_TO_SCREEN:
                $this->pdf->Output( $this->file_name, 'I' );
                break;
            case self::OUTPUT_TO_DISK:
                if ( !isset ( $this->save_path ) ) {
                    throw new Exception( 'No save path is set.' );
                }
                $this->pdf->Output( $this->save_path . $this->file_name, 'F' );
                break;
            case self::OUTPUT_TO_EMAIL:
                $this->pdf->Output( $this->file_name, 'E' );
                break;
            case self::OUTPUT_TO_DOWNLOAD:
                // Appears to be mutually exclusive with 'I'
                $this->pdf->Output( $this->file_name, 'D' );
                break;
        }

    }


    /**
     * Check that everything that needs to be set
     * 
     * @return  boolean     Whether or not there are missing parameters set
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected function missing_parameters(): bool {
        // if ( !isset ( $this->title ) ) {
        //     return true;
        // }

        // if ( !isset ( $this->content ) ) {
        //     return true;
        // }

        return false;
    }


    /**
     * Check if the text starts with the <style> tag.
     * 
     * @param   string  $css    Text to test.
     * 
     * @return  string
     * 
     * @access  protected
     * @since   LRS 3.20.0
     */

    protected function detect_style_tag( string $css ): bool {
        return substr( $css, 0, 7 ) == '<style>';
    }

    /**
     * Add style tags to the css.
     * 
     * @param   string  $css    The css to be wrapped in <style> tags.
     * 
     * @return  string
     * 
     * @access  protected
     * @since   LRS 3.20.0
     */

    protected function add_style_tags( string $css ): string {
        if ( $css == '' ) {
            return '';
        }
        return "<style>{$css}</style>";
    }


    /**
     * DEBUG TOOLS
     */


    /**
     * Echo out the PDF content as a means of debugging the PDF.
     * 
     * @access  public
     * @since   LRS 3.26.0
     */

    public function debug_content(): void {
        echo $this->style . $this->content;
    }

}