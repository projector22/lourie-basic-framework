<?php

namespace LBF\Tools\PDF;

use TCPDF;
use Exception;
use LBF\Tools\PDF\CustomHeaderFooter;
use LBF\Tools\PDF\Enums\OutputTo;
use LBF\Tools\PDF\Enums\PageOrientation;
use LBF\Tools\PDF\Enums\PaperSize;

class NewPDFCreatorBackend {

    /**
     * The variable assigned to the new TCPDF object
     * 
     * @var object  $pdf
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected object $pdf;

    protected readonly array $default_properties;

    protected array $default_values;

    protected ?string $header;
    protected ?string $footer;
    protected int $header_margin;
    protected int $footer_margin;
    protected float $line_height;
    protected string $font_name;
    protected string $font_style;
    protected int $font_size;
    protected PaperSize $page_size;
    protected PageOrientation $page_orientation;
    protected bool $text_shadow;
    protected int $margin_left;
    protected int $margin_right;
    protected int $margin_top;


    protected function set_default_values(): void {
        $this->default_properties = [
            'header'           => 'default',
            'footer'           => 'default',
            'header_margin'    => PDF_MARGIN_HEADER,
            'footer_margin'    => PDF_MARGIN_FOOTER,
            'line_height'      => 1.25,
            'font_name'        => 'helvetica',
            'font_style'       => '',
            'font_size'        =>  14,
            'page_size'        => PaperSize::A4,
            'page_orientation' => PageOrientation::PORTRAIT,
            'text_shadow'      => false,
            'margin_left'      => PDF_MARGIN_LEFT,
            'margin_right'     => PDF_MARGIN_RIGHT,
            'margin_top'       => PDF_MARGIN_TOP,
        ];
    }


    /**
     * Indicate what method of output should be used.
     * 
     * @var integer $output_method  
     * 
     * ## Options
     * - OutputTo::SCREEN
     * - OutputTo::DISK
     * - OutputTo::EMAIL
     * - OutputTo::DOWNLOAD
     * 
     * @access  protected
     * @since   LRS 3.20.0
     */

    protected OutputTo $output_method = OutputTo::SCREEN;




    /**
     * Generate the PDF file and display the file, usually the last method to call.
     * 
     * @access  protected
     * @since   LRS 3.6.0
     */

    protected function generate_pdf(): void {
        // if ( $this->missing_parameters() ) {
        //     echo "An error has occured: Parameters not set";
        //     return;
        // }

        // This seems to be required to flush and product the PDF - Gp
        ob_end_clean();

        // Ensure the file name has a trailing '.pdf'
        if ( !str_contains( $this->file_name ?? '', '.pdf' ) ) {
            $this->file_name .= '.pdf';
        }

        // Close and output PDF document
        switch ( $this->output_method ) {
            case OutputTo::SCREEN:
                $this->pdf->Output( $this->file_name, 'I' );
                break;
            case OutputTo::DISK:
                if ( !isset ( $this->save_path ) ) {
                    throw new Exception( 'No save path is set.' );
                }
                $this->pdf->Output( $this->save_path . $this->file_name, 'F' );
                break;
            case OutputTo::EMAIL:
                $this->pdf->Output( $this->file_name, 'E' );
                break;
            case OutputTo::DOWNLOAD:
                // Appears to be mutually exclusive with 'I'
                $this->pdf->Output( $this->file_name, 'D' );
                break;
        }

    }

}