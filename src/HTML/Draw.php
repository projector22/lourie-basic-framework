<?php

namespace LBS\HTML;

use App\Enums\SVGImages;
use LBS\HTML\JS;
use LBS\HTML\HTML;
use SVGTools\SVG;

/**
 * This class is to draw out various commonly used UI elements
 * 
 * use LBS\HTML\Draw;
 * 
 * @composer-requires    SVGTools\SVG
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.1.0
 * @since   3.12.5      Moved to LBS\HTML\Draw from PageElements
 */

class Draw {

    /**
     * Whether to echo or return the string item
     * 
     * @var boolean $echo
     * 
     * @access  public
     * @since   3.6.0
     */

    public static bool $echo = true;


    /**
     * Place the site logo anywhere on the site as required
     * 
     * @param   int $width  Define width of logo. Default: 200px
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.1.0
     */

    public static function site_logo( int $width = 30 ) {
        $logo = site_logo();

        if ( $logo !== false ) {
            $img = "<img src='" . BASE_URL . "{$logo}' alt='School Logo' width='{$width}px'>";
        } else {
            return;
        }

        if ( self::$echo ) {
            echo $img;
        } else {
            return $img;
        }
    }


    /**
     * Providing a spacing element as required
     * 
     * @access  public
     * @since   3.1.0
     */

    public static function element_spacer_one(): void {
        echo "<span class='spacer_one'></span><span></span>";    
    }


    /**
     * Providing a spacing element as required
     * 
     * @access  public
     * @since   3.1.0
     */
    
    public static function element_spacer_two(): void {
        echo "<span class='spacer_two'></span><span></span>";
    }


    /**
     * Draws a dot on the screen as required
     * 
     * @param   int     $k  The number to draw  Default: 1
     * 
     * @access  public
     * @since   3.1.0
     */

    public static function dot( int $k = 1 ): void {
        $dot = CLI_INTERFACE ? '.' : '<b>.</b> ';
        for ( $i = 0; $i < $k; $i++) {
            echo $dot;
        }
    }


    /**
     * Draws the number of html line breaks required
     * 
     * @param   integer     $k  The number of lines to draw
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.1.0
     * @since   3.4.0   Added @param $inline
     * @since   3.6.0   Removed @param $inline to use property self::$echo
     * @since   3.14.0  Added a catch for CLI_INTERFACE
     */

    public static function lines( int $k ) {
        $lb = CLI_INTERFACE ? "\n" : "<br>";
        $line = '';
        for ( $i = 0; $i < $k; $i++ ) {
            $line .= $lb;
        }
        if ( self::$echo ) {
            echo $line;
        } else {
            return $line;
        }
    }


    /**
     * Draws a dividing line separator as required.
     * 
     * @param   integer     $k      Number of lines to draw.
     *                              Default: 1
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.17.4
     */

    public static function line_separator( int $k = 1 ) {
        $line = '';
        for ( $i = 0; $i < $k; $i++ ) {
            $line .= '<hr>';
        }
        if ( self::$echo ) {
            echo $line;
        } else {
            return $line;
        }
    }


    /**
     * Draws a page break when printing
     * 
     * @access  public
     * @since   3.1.0
     */
    
    public static function page_break(): void {
        echo "<div class='page_break'></div>";
    }


    /**
     * Draws an appropriate error message
     * 
     * @param   string  $info   What message to show    Default: ''
     * 
     * @return  string
     * 
     * @access  public
     * @since   3.1.0
     */
    
    public static function action_error( string $info = '' ): string {
        if ( $info == '' ) {
            $error = "An error has occured ";
        } else {
            $error = "An error has occured, {$info} ";
        }
        if ( self::$echo ) {
            echo $error;
        }
        return $error;
    }


    /**
     * Draw out a <span> which is used in the description in an admin or discipline page element
     * 
     * @param   string  $input  The string to be displayed on the screen
     * @param   int     $lines  The number of lines to be drawn before the text
     *                          Default: 0
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.1.0
     * @since   3.6.0   Changed $lines default to 0
     */

    public static function item_description( string $input, int $lines = 0 ) {
        if ( self::$echo ) {
            self::lines( $lines );
            echo "<span>$input</span>";
        } else {
            $element = self::lines( $lines );
            $element .= "<span>$input</span>";
            return $element;    
        }
    }


    /**
     * Draw out a span which will be used to draw the response text from JS AJAX calls 
     * 
     * @param   string  $id         Desired span id elemenet
     * @param   string  $content    Content of the span element
     * 
     * @access  public
     * @since   3.1.0
     */

    public static function response_text( string $id, string $content = '' ): void  {
        echo "<span id='$id'>$content</span>"; 
    }


    /**
     * The text area containing some text to be copied.
     * 
     * Note, if this is being loaded via AJAX, you will need to put the 
     * line `JS::clipboardButton();` into the host page, as the insert
     * here will not run via AJAX.
     * 
     * @param   string  $test   The text to be copied
     * @param   string  $id     The id of the input element to be copied
     * 
     * @access  public
     * @since   3.4.0
     */

    public static function copy_text_textbox( string $text, string $id ): void {
        HTML::div( ['class' => 'ctt_contain'] );
        echo "<input class='ctt_text_box' id='{$id}' value='{$text}' readonly>";
        echo "<button class='btn cct_bttn' data-clipboard-target='#{$id}' onClick='return false'>";
        echo "<img class='ctt_img' src='data:image/svg+xml;charset=UTF-8," . SVGImages::clippy->image() . "' alt='Copy to clipboard'>";
        echo "</button>";
        HTML::close_div(); // ctt_contain
        JS::clipboardButton();
    }


    /**
     * Draw out a block text header within the administrative side of things
     * 
     * @param   string  $text   The text to be drawn in the header
     * 
     * @access  public
     * @since   3.4.1
     */

    public static function structure_header_divider( string $text ): void {
        echo "<div class='admin_structure_header_divider'>";
        echo "<h1>{$text}</h1>";
        echo "</div>";
    }


    /**
     * Draw out the page response area in the bottom left
     * 
     * @param   string  $id     Desired id of elemenet
     * 
     * @access  public
     * @since   3.4.1
     */

    public static function ajax_response_bottom_left( string $id ): void {
        echo "<div class='ajax_response_bottom_left'>";
        self::response_text( $id );
        echo "</div>";
    }


    /**
     * Draw out a reports header on printed reports
     * 
     * @param   string  $report_title   A title to be placed on the top of the report. Default: "Report"
     * 
     * @access  public
     * @since   3.4.5
     */

    public static function printing_report_header( string $report_title = "Report" ): void {
        echo "<div class='print_report_header_contain'>";
        echo "<div class='prh_sch_name'>";
        echo "<i>" . SCHOOL_NAME . "</i>";
        echo "</div>";
        echo "<div class='prh_rpt_title'>";
        echo "<b>$report_title</b>";
        echo "</div>";
        echo "</div>";
    }


    /**
     * Add some CSS to force a page print to landscape
     * 
     * @access  public
     * @since   3.4.9
     */

    public static function print_landscape(): void {
        ?>
        <style type="text/css" media="print">
            @page {
                size: landscape;
            }
        </style>
        <?php
    }


    /**
     * Draw out the exectution box, where ajax code execution can report back to
     * 
     * @param   string  $id             Element ID for the feedback box
     * @param   string  $custom_height  Defines a custom height for the box
     *                                  Default: null
     * @param   string  $content        The content to load into the box
     * 
     * @access  public
     * @since   3.6.0
     * @since   3.14.0  Added param $content
     */

    public static function code_feedback_box( string $id, ?string $custom_height = null, string $content = '' ) {
        $params = ['class' => 'code_feedback_box', 'id' => $id];
        if ( !is_null( $custom_height ) ) {
            $params['style'] = "height: $custom_height";
        }
        HTML::div_container( $params, $content );
    }


    /**
     * Draws the console feedback header
     * 
     * @param   string  $header     The text to be displayed
     * 
     * @access  public
     * @since   3.6.0
     */

    public static function console_header( $header ): void {
        echo "<h2>$header</h2>";
    }


    /**
     * Draw the more info help link
     * 
     * @param   string  $page the page on the help form to go to
     * 
     * @access  public
     * @since   3.6.0
     */

    public static function more_info_help( $page ): void {
        self::lines( 2 );
        echo "<a href='help?p=$page'>More Information</a>";
    }


    /**
     * Draw an admin section item heading
     * 
     * @param   string  $text           The text to display
     * @param   boolean $line_break     Where or not to put in a line break
     *                                  Default: true
     * 
     * @access  public
     * @since   3.6.0
     */

    public static function item_heading( string $text, bool $line_break = true ): void {
        echo "<span class='item_heading'><b>$text</b></span>";
        if ( $line_break ) {
            self::lines( 1 );
        }
    }


    /**
     * Display the item input inline (using flex)
     * 
     * @var boolean $display_item_input_inline     
     * 
     * @access  public
     * @since   3.9.0
     */

    public static bool $display_item_input_inline = false;

    /**
     * Draw an admin section item input
     * 
     * @param   string  $input          The HTML input item to draw
     * @param   boolean $line_break     Where or not to put in a line break
     *                                  Default: true
     * @param   string  $id             The id to give the span to be drawn
     *                                  Default: null
     * 
     * @access  public
     * @since   3.6.0
     */

    public static function item_input( string $input, bool $line_break = true, ?string $id = null ): void {
        $set_id = '';
        if ( !is_null ( $id ) ) {
            $set_id = " id='{$id}'";
        }
        $set_class = 'item_input';
        if ( self::$display_item_input_inline ) {
            $set_class .= ' item_input_inline';
        }
        echo "<span class='{$set_class}'{$set_id}>{$input}</span> ";
        if ( $line_break ) {
            self::lines( 1 );
        }
    }


    /**
     * Draw an admin section item input
     * 
     * @param   string  $text           The HTML input item to draw
     * @param   boolean $line_break     Where or not to put in a line break
     *                                  Default: true
     * @param   string  $id             The id of the containing span. If you wish to do some Javascript on the element
     *                                  Default: null
     * 
     * @access  public
     * @since   3.6.0
     * @since   3.8.0   Added @param $id
     */

    public static function item_text( string $text, bool $line_break = true, ?string $id = null ): void {
        $element = "<span class='item_text'";
        if ( !is_null ( $id ) ) {
            $element .= " id='{$id}' ";
        }
        $element .= ">{$text}</span> ";
        echo $element;
        if ( $line_break ) {
            self::lines( 1 );
        }
    }


    /**
     * Draw an admin section item checkbox
     * 
     * @param   string  $checkbox       The HTML input checkbox to draw
     * @param   string  $label          The label for the checkbox
     * @param   boolean $line_break     Where or not to put in a line break
     *                                  Default: true
     * 
     * @access  public
     * @since   3.6.0
     */

    public static function item_checkbox( string $checkbox, string $label, bool $line_break = true ): void {
        echo "<span class='item_input'>$checkbox $label</span>";
        if ( $line_break ) {
            self::lines( 1 );
        }
    }


    /**
     * Draw an admin section item input
     * 
     * @param   string  $text          The text header
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.6.0
     */

    public static function item_description_header( string $text ) {
        $h = "<h1>{$text}</h1>";
        if ( self::$echo ) {
            echo $h;
        } else {
            return $h;
        }
    }


    /**
     * Draw a custom sized heading without interfering with other formatting of the text
     * 
     * @param   string  $text   The text header
     * @param   string  $size   The font size desired
     *                          Default: 12px
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.6.0
     */

    public static function custom_header( string $text, string $size = '12px' ) {
        $div = "<div style='font-size: $size'>$text</div>";
        if ( self::$echo ) {
            echo $div;
        } else {
            return $div;
        }
    }


    /**
     * Draw the html elements to open a link in a new tab
     * 
     * @access  public
     * @since   3.7.1
     */

    const NEW_TAB = "target='_blank' rel='noopener'";


    /**
     * Draw the section break template
     * 
     * @param   string  $description    The text to go into the description
     * @param   string  $class          The class of the new element
     * 
     * @access  private
     * @since   3.12.8
     */

    private static function break_template( string $description, string $class ): void {
        $hold = HTML::$echo;
        HTML::$echo = true;
        HTML::close_div();
        HTML::div_container( ['class' => 'page_element_description'], $description );
        HTML::div( ['class' => $class] );
        HTML::$echo = $hold;
    }


    /**
     * Draw elements to draw a section break in the general admin layout
     * 
     * @param   string  $description    The description to be placed in this section break
     * 
     * @access  public
     * @since   3.8.0
     */

    public static function section_break( string $description = '' ): void {
        self::break_template( $description, 'page_element_main' );
    }


    /**
     * Draw out the toggle selection box and container, used mostly in accounts
     * 
     * @param   string  $group_name     The name to draw next to the toggle box
     * @param   string  $toggle         The toggle drawn from Form::toggle()
     * 
     * @access  public
     * @since   3.9.0
     */

    public static function accounts_toggle( string $group_name, string $toggle ): void {
        echo "<div class='group_container'>";
        echo "<span class='center_vertical'>$group_name</span>";
        echo "<span class='center_vertical'>$toggle</span>";
        echo "</div>";// group_container
    }


    /**
     * Draw page tabs
     * 
     * @param   array   $data       The data for each tab
     *                              Format: $name => $link
     *                              $link format: PAGE . ?p=page&t=tab
     * @param   string  $default    The default tab, in case $_GET is not set
     * 
     * @access  public
     * @since   3.12.0
     */

    public static function page_tabs( array $data, string $default ): void {
        echo "<ul class='general_page_tab'>";
        foreach ( $data as $name => $link ) {
            $tab = explode( '=', $link )[array_key_last ( explode( '=', $link ) )];
            $selected = isset( $_GET['t'] ) && $_GET['t'] == $tab ? '' : ' tab_not_selected';
            $selected = !isset( $_GET['t'] ) && $tab == $default ? '' : $selected;
            echo "<a href={$link}><li class='tab{$selected}'>{$name}</li></a>";
        }
        echo "<li class='tab_last_element'></li>";
        echo "</ul>";
    }


    /**
     * Return a number of tab breaks
     * 
     * @param   integer $n  Number of tabs
     * 
     * @return  string
     * 
     * @access  public
     * @since   3.14.0
     */

    public static function tabs( int $n ): string {
        $t = '';
        for ( $i = 0; $i < $n; $i++ ) {
            $t .= "\t";
        }
        return $t;
    }


    /**
     * Text to put at the end of all scripts.
     * 
     * @access  public
     * @since   3.14.0
     */

    public static function end_of_script(): void {
        self::lines( 2 );
    }


    /**
     * Draw a draggable list
     * 
     * @param   array   $data      The contents of each cell
     * 
     * @access  public
     * @since   3.15.0
     */

    public static function draggable_list( array $data ): void {
        $hold = HTML::$echo;
        HTML::$echo = true;
        $id = random_id_string( 5 );

        HTML::div( [
            'class' => 'draggable_container',
            'id'    => 'draggable_container' . $id
        ] );
        HTML::$echo = false;

        $svg = new SVG( SVGImages::grabber->image() );
        $svg->set_viewbox( 0, 0, 16, 16 );
        foreach ( $data as $item ) {
            $line = HTML::div_container( 
                ['class' => 'draggable_entry'], 
                HTML::span_container(
                    ['class' => 'de_item'], 
                    $item 
                ) . 
                HTML::span_container(
                    ['class' => "de_grabber de_grabber{$id}"],
                    $svg->return(),
                )
            );

            echo $line;
        }

        HTML::$echo = true;
        HTML::close_div();
        /**
         * @see https://github.com/SortableJS/Sortable
         * @see https://sortablejs.github.io/Sortable/
         * 
         * @since   3.15.0
         */
        JS::script_module( "
            import Sortable from './src/js/thirdparty/sortablejs/sortable.esm.js';
            const el = document.getElementById('draggable_container{$id}');
            const sortable = Sortable.create(el, {
                swap: true,
                swapClass: 'highlight',
                animation: 150,
                ghostClass: 'draggable_ghost',
                handle: '.de_grabber{$id}',
            });"
        );
        HTML::$echo = $hold;
    }


    /**
     * Draw the standard page left column
     * 
     * @param   array   $params     The params to add to the div.
     *                              Default: []
     * 
     * @access  public
     * @since   3.15.8
     */

    public static function standard_column_left( array $params = [] ): void {
        $hold = HTML::$echo;
        HTML::$echo = true;

        if ( isset ( $params['class'] ) ) {
            $params['class'] = 'page_element_description ' . $params['class'];
        } else {
            $params['class'] = 'page_element_description';
        }

        if ( !isset ( $params['name'] ) ) {
            $params['name'] = 'page_element_description';
        }

        HTML::div( $params );
        HTML::$echo = $hold;
    }


    /**
     * Draw the standard page right column
     * 
     * @param   array   $params             The params to add to the div.
     *                                      Default: []
     * @param   boolean $close_left_column  Whether or not to close the left hand column
     *                                      Default: true
     * 
     * @access  public
     * @since   3.15.8
     */

    public static function standard_column_right( array $params = [], bool $close_left_column = true ): void {
        $hold = HTML::$echo;
        HTML::$echo = true;

        if ( $close_left_column ) {
            HTML::close_div(); // page_element_description
        }

        if ( !isset( $params['maxmin'] ) ) {
            $params['maxmin'] = true;
        }

        if ( isset ( $params['class'] ) ) {
            $params['class'] = 'page_element_main ' . $params['class'];
        } else {
            $params['class'] = 'page_element_main';
        }

        HTML::div( $params );

        HTML::div( ['class' => 'main_page_maxmin__container'] );
        if( $params['maxmin'] ) {
            $id = random_id_string();
            HTML::div( [
                'class'     => 'main_page_maxmin',
                'name'      => 'minmax_bttn',
                'id'        => $id,
                'data-open' => 0,
            ] );
            HTML::span( [
                'id'   => "{$id}_max",
                'name' => 'minmax_max',
            ] );
            $svg_max = new SVG( SVGImages::maximize->image() );
            $svg_max->set_size( 18, 18 );
            $svg_max->echo();
            HTML::close_span();
            HTML::span( [
                'class' => 'hidden',
                'id'    => "{$id}_min",
                'name'  => 'minmax_min',
            ] );
            $svg_min = new SVG( SVGImages::minimize->image() );
            $svg_min->set_size( 18, 18 );
            $svg_min->echo();
            HTML::close_span();
            HTML::close_div();
        }
        HTML::close_div();

        HTML::$echo = $hold;
    }


    /**
     * Draw the standard single column page start
     * 
     * @param   array   $params     The params to add to the div.
     *                              Default: []
     * 
     * @access  public
     * @since   3.16.0
     */

    public static function standard_single_column( array $params = [] ): void {
        $hold = HTML::$echo;
        HTML::$echo = true;

        if ( isset ( $params['class'] ) ) {
            $params['class'] = 'page_element_main ' . $params['class'];
        } else {
            $params['class'] = 'page_element_main';
        }

        HTML::div( $params );
        HTML::$echo = $hold;
    }


    /**
     * Close the right / full length column
     * 
     * @access  public
     * @since   3.15.8
     */

    public static function close_standard_page(): void {
        echo "</div>"; // page_element_main
    }


    /**
     * Draw a number of input fields in a single line.
     * 
     * @access  public
     * @since   3.16.0
     */

    public static function set_input_multi_line(): void {
        echo "<div class='multi_line_input'>";
    }


    /**
     * End the line of input fields.
     * 
     * @access  public
     * @since   3.16.0
     */

    public static function close_multi_line_input(): void {
        echo "</div>";
    }


    /**
     * Draw a filter container.
     * 
     * @access  public
     * @since   3.16.0
     */

    public static function table_filters_container(): void {
        echo "<div class='table_filter_container'>";
    }


    /**
     * End the line of table filter container.
     * 
     * @access  public
     * @since   3.16.0
     */

    public static function close_table_filters_container(): void {
        echo "</div>";
    }


    /**
     * Draw an aligned left container.
     * 
     * @access  public
     * @since   3.16.0
     */

    public static function align_entries_left(): void {
        echo "<div class='flex_aligned_left center_vertical'>";
    }


    /**
     * End the blocks aligned left
     * 
     * @access  public
     * @since   3.16.0
     */

    public static function close_align_entries_left(): void {
        echo "</div>";
    }

}