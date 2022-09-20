<?php

namespace LBF\Tools\PDF;

/**
 * ## IDEA
 * 
 * - set_document_properties array
 * - set_page_properties array overwrites doc properties
 * 
 * ## Order
 * 
 * new object
 * set_doc_properties
 * new page
 * set_page_properties
 * page content
 * ... repeat last 3 as much as needed
 * close document
 * generate_pdf
 */

use LBF\Tools\PDF\Enums\OutputTo;
use TCPDF;
use LBF\Tools\PDF\NewPDFCreatorBackend;

class NewPDFCreator extends NewPDFCreatorBackend {


    public function __construct(


        array $default_properties,
    ) {
        $this->set_document_default_properties( $default_properties );
        $this->pdf = new TCPDF(
            orientation: $this->orientation->value(), 
            unit:        PDF_UNIT, 
            format:      $this->page_size->value(), 
            unicode:     true, 
            encoding:    'UTF-8',
        );


        // set document information
        $this->pdf->SetCreator( PDF_CREATOR );
        $this->pdf->SetAuthor( $this->author ?? '' );
        $this->pdf->SetTitle( $this->title ?? '' );
        $this->pdf->SetSubject( $this->subject ?? '' );
        $this->pdf->SetKeywords( $this->keywords ?? '' );

        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

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

        $this->pdf->setCellHeightRatio( $this->line_height );

        $this->pdf->SetFont(
            family: $this->font_name,
            style: $this->font_style,
            size: $this->font_size,
            subset: true,
        );

        // set text shadow effect
        if ( $this->text_shadow ) {
            $this->pdf->setTextShadow( [
                'enabled'    => true,
                'depth_w'    => 0.2,
                'depth_h'    => 0.2,
                'color'      => [196,196,196],
                'opacity'    => 1,
                'blend_mode' => 'Normal'
            ] );
        }

    }

    /**
     * ### Properties
     * 
     * - header
     * - footer_margin
     * - footer
     * - footer_margin
     * - line_height
     * - font_name
     * - font_style (bold, italic etc.)
     * - font_size
     * - page_size (A4, A3 etc.)
     * - page_orientation
     * - text_shadow
     * - margin_left
     * - margin_right
     * - margin_top
     */

    public function set_document_default_properties( array $properties = [] ): static {
        $this->default_values = [
            'header'           => '\\' . trim( $properties['header'], '\\' ) ?? $this->default_properties['header'],
            'footer'           => '\\' . trim( $properties['footer'], '\\' ) ?? $this->default_properties['footer'],
            'header_margin'    => $properties['header_margin']    ?? $this->default_properties['header_margin'],
            'footer_margin'    => $properties['footer_margin']    ?? $this->default_properties['footer_margin'],
            'line_height'      => $properties['line_height']      ?? $this->default_properties['line_height'],
            'font_name'        => $properties['font_name']        ?? $this->default_properties['font_name'],
            'font_style'       => $properties['font_style']       ?? $this->default_properties['font_style'],
            'font_size'        => $properties['font_size']        ?? $this->default_properties['font_size'],
            'page_size'        => $properties['page_size']        ?? $this->default_properties['page_size'],
            'page_orientation' => $properties['page_orientation'] ?? $this->default_properties['page_orientation'],
            'text_shadow'      => $properties['text_shadow']      ?? $this->default_properties['text_shadow'],
            'margin_left'      => $properties['margin_left']      ?? $this->default_properties['margin_left'],
            'margin_right'     => $properties['margin_right']     ?? $this->default_properties['margin_right'],
            'margin_top'       => $properties['margin_top']       ?? $this->default_properties['margin_top'],
        ];

        foreach ( $this->default_values as $key => $value ) {
            $this->$key = $value;
        }
        return $this;
    }

    /**
     * Properties
     * 
     * - header
     * - footer_margin
     * - footer
     * - footer_margin
     * - line_height
     * - font_name
     * - font_style (bold, italic etc.)
     * - font_size
     * - page_size (A4, A3 etc.)
     * - page_orientation
     * - text_shadow
     * - margin_left
     * - margin_right
     * - margin_top
     */
    public function set_page_properties( array $properties ): static {
        foreach ( $this->default_properties as $key => $value ) {
            if ( $key == 'header' || $key == 'footer' ) {
                if ( $value !== 'default' && !is_null( $value ) ) {
                    $this->$key = '\\' . trim( $properties[$key], '\\' );
                } else {
                    $this->$key = $properties[$key];
                }
            } else {
                $this->$key = $properties[$key];
            }
        }
        return $this;
    }

    public function reset_page_properties_to_default(): static {
        foreach ( $this->default_properties as $key => $value ) {
            $this->$key = $this->default_values[$key];
        }
        return $this;
    }



    public function new_page( array $properties = [] ): static {
        $this->reset_page_properties_to_default();
        $this->set_page_properties( $properties );


        /**
         * INSERT HEADER
         */
        $this->pdf->setHeaderTemplateAutoreset();
        if ( is_null ( $this->header_type ) ) {
            // Disable header
            $this->pdf->setPrintHeader( false );
        } else {
            // SET HEADER
        }
        


        /**
         * INSERT FOOTER
         */
        if ( $this->footer_type == null ) {
            // Disable footer
            $this->pdf->setPrintFooter( false );
        } else {
            // SET HEADER
        }
        

        /**
         * SET PAGE MARGINS
         */
        $this->pdf->SetMargins( $this->margin_left, $this->margin_top, $this->margin_right );


        /**
         * ADD PAGE
         */

        $this->pdf->AddPage( 
            $this->orientation->value(), 
            $this->page_size->value(),
        );
        return $this;

    }


    /**
     * Close off the PDF document.
     * 
     * @return  static
     * 
     * @access  public
     * @since   LRS 3.20.0
     */

    public function end_pdf(): static {
        $this->generate_pdf();
        return $this;
    }














    public function set_output( OutputTo $output, string $save_path = ''  ): static {
        $this->save_path = $save_path;
        $this->output_method = $output;
        return $this;
    }
    

}