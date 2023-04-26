<?php

namespace LBF\HTML;

use Feather\Icons;
use LBF\App\Config;
use LBF\Auth\Hash;
use LBF\HTML\HTML;
use LBF\HTML\HTMLMeta;
use LBF\HTML\JS;
use LBF\Img\SVGImages;
use SVGTools\SVG;

/**
 * This class is to draw out various commonly used UI elements
 * 
 * use LBF\HTML\Draw;
 * 
 * @composer-requires    SVGTools\SVG
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.1.0
 * @since   LRS 3.12.5      Moved to `Framework\HTML\Draw` from `PageElements`
 * @since   LRS 3.28.0      Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                          Namespace changed from `Framework` to `LBF`.
 * @since   LBF 0.1.6-beta  Added extension `HTMLMeta`.
 */

class Draw extends HTMLMeta {

    /**
     * Providing a spacing element as required
     * 
     * @static
     * @access  public
     * @since   LRS 3.1.0
     */

    public static function element_spacer_one(): void {
        echo "<span class='spacer_one'></span><span></span>";
    }


    /**
     * Providing a spacing element as required
     * 
     * @static
     * @access  public
     * @since   LRS 3.1.0
     */

    public static function element_spacer_two(): void {
        echo "<span class='spacer_two'></span><span></span>";
    }


    /**
     * A property for holding the value of whether the app is running in the CLI or Web interface.
     * 
     * @var boolean $cli
     * 
     * @access  private
     * @since   LRS 3.28.0
     */

    private static bool $cli;


    /**
     * Draws a dot on the screen as required
     * 
     * @param   int     $k  The number to draw  Default: 1
     * 
     * @static
     * @access  public
     * @since   LRS 3.1.0
     */

    public static function dot(int $k = 1): void {
        self::$cli ??= php_sapi_name() == 'cli';
        $dot = self::$cli ? '.' : '<b>.</b> ';
        for ($i = 0; $i < $k; $i++) {
            echo $dot;
        }
    }


    /**
     * Draws the number of html line breaks required
     * 
     * @param   integer     $k  The number of lines to draw
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.1.0
     * @since   LRS 3.4.0   Added @param $inline
     * @since   LRS 3.6.0   Removed @param $inline to use property self::$echo
     * @since   LRS 3.14.0  Added a catch for CLI_INTERFACE
     */

    public static function lines(int $k): string {
        self::$cli ??= php_sapi_name() == 'cli';
        $lb = self::$cli ? "\n" : "<br>";
        $line = '';
        for ($i = 0; $i < $k; $i++) {
            $line .= $lb;
        }

        self::handle_echo($line);
        return $line;
    }


    /**
     * Draws a dividing line separator as required.
     * 
     * @param   integer     $k      Number of lines to draw.
     *                              Default: 1
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.17.4
     */

    public static function line_separator(int $k = 1): string {
        self::$cli ??= php_sapi_name() == 'cli';
        $line = '';
        for ($i = 0; $i < $k; $i++) {
            if (self::$cli) {
                $line .= "\n--------------------------------------------------------------------------------------\n";
            } else {
                $line .= '<hr>';
            }
        }

        self::handle_echo($line);
        return $line;
    }


    /**
     * Draws a page break when printing
     * 
     * @static
     * @access  public
     * @since   LRS 3.1.0
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
     * @static
     * @access  public
     * @since   LRS 3.1.0
     */

    public static function action_error(string $info = ''): string {
        if ($info == '') {
            $error = "An error has occured ";
        } else {
            $error = "An error has occured, {$info} ";
        }
        if (self::$echo) {
            echo $error;
        }
        return $error;
    }


    /**
     * Draw out a <span> which is used in the description in an admin or discipline page element.
     * 
     * @param   string  $input  The string to be displayed on the screen.
     * @param   int     $lines  The number of lines to be drawn before the text.
     *                          Default: 0
     * 
     * @return  string|void
     * 
     * @static
     * @access  public
     * @since   LRS 3.1.0
     * @since   LRS 3.6.0       Changed $lines default to 0.
     * @since   LBF 0.1.6-beta  Revamped.
     */

    public static function item_description(string $input, int $lines = 0): string {
        $hold = self::temporary_change_echo(false);
        $element = self::lines($lines) . "<span>{$input}</span>";
        self::restore_origonal_echo($hold);
        self::handle_echo($element);
        return $element;
    }


    /**
     * Draw out a span which will be used to draw the response text from JS AJAX calls.
     * 
     * @param   string  $id         Desired span id elemenet.
     * @param   string  $content    Content of the span element.
     * 
     * @static
     * @access  public
     * @since   LRS 3.1.0
     */

    public static function response_text(string $id, string $content = ''): void {
        echo "<span id='{$id}'>{$content}</span>";
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
     * @static
     * @access  public
     * @since   LRS 3.4.0
     */

    public static function copy_text_textbox(string $text, string $id): void {
        HTML::div(['class' => 'ctt_contain']);
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
     * @static
     * @access  public
     * @since   LRS 3.4.1
     */

    public static function structure_header_divider(string $text): void {
        echo "<div class='admin_structure_header_divider'>";
        echo "<h1>{$text}</h1>";
        echo "</div>";
    }


    /**
     * Draw out the page response area in the bottom left
     * 
     * @param   string  $id     Desired id of elemenet
     * 
     * @static
     * @access  public
     * @since   LRS 3.4.1
     */

    public static function ajax_response_bottom_left(string $id): void {
        echo "<div class='ajax_response_bottom_left'>";
        self::response_text($id);
        echo "</div>";
    }


    /**
     * Add some CSS to force a page print to landscape
     * 
     * @static
     * @access  public
     * @since   LRS 3.4.9
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
     * @static
     * @access  public
     * @since   LRS 3.6.0
     * @since   LRS 3.14.0  Added param $content
     */

    public static function code_feedback_box(string $id, ?string $custom_height = null, string $content = ''): void {
        $params = ['class' => 'code_feedback_box', 'id' => $id];
        if (!is_null($custom_height)) {
            $params['style'] = "height: $custom_height";
        }
        HTML::div_container($params, $content);
    }


    /**
     * Draws the console feedback header
     * 
     * @param   string  $header     The text to be displayed
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.0
     */

    public static function console_header(string $header): void {
        self::$cli ??= php_sapi_name() == 'cli';
        if (self::$cli) {
            echo "### " . strtoupper($header) . " ###";
        } else {
            echo "<h2 class='console_header'>{$header}</h2>";
        }
    }


    /**
     * Draw the more info help link
     * 
     * @param   string  $page the page on the help form to go to
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.0
     */

    public static function more_info_help($page): void {
        self::lines(2);
        echo "<a href='help?p=$page'>More Information</a>";
    }


    /**
     * Draw an admin section item heading
     * 
     * @param   string  $text           The text to display
     * @param   boolean $line_break     Where or not to put in a line break
     *                                  Default: true
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.0
     */

    public static function item_heading(string $text, bool $line_break = true): void {
        echo "<span class='item_heading'><b>{$text}</b></span>";
        if ($line_break) {
            self::lines(1);
        }
    }


    /**
     * Display the item input inline (using flex)
     * 
     * @var boolean $display_item_input_inline
     * 
     * @access  public
     * @since   LRS 3.9.0
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
     * @static
     * @access  public
     * @since   LRS 3.6.0
     */

    public static function item_input(string $input, bool $line_break = true, ?string $id = null): void {
        $set_id = '';
        if (!is_null($id)) {
            $set_id = " id='{$id}'";
        }
        $set_class = 'item_input';
        if (self::$display_item_input_inline) {
            $set_class .= ' item_input_inline';
        }
        echo "<span class='{$set_class}'{$set_id}>{$input}</span> ";
        if ($line_break) {
            self::lines(1);
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
     * @static
     * @access  public
     * @since   LRS 3.6.0
     * @since   LRS 3.8.0   Added @param $id
     */

    public static function item_text(string $text, bool $line_break = true, ?string $id = null): void {
        $element = "<span class='item_text'";
        if (!is_null($id)) {
            $element .= " id='{$id}' ";
        }
        $element .= ">{$text}</span> ";
        echo $element;
        if ($line_break) {
            self::lines(1);
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
     * @static
     * @access  public
     * @since   LRS 3.6.0
     */

    public static function item_checkbox(string $checkbox, string $label, bool $line_break = true): void {
        echo "<span class='item_input'>$checkbox $label</span>";
        if ($line_break) {
            self::lines(1);
        }
    }


    /**
     * Draw an admin section item input
     * 
     * @param   string  $text          The text header
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.0
     */

    public static function item_description_header(string $text): string {
        $h = "<h1>{$text}</h1>";
        self::handle_echo($h);
        return $h;
    }


    /**
     * Draw a custom sized heading without interfering with other formatting of the text
     * 
     * @param   string  $text   The text header
     * @param   string  $size   The font size desired
     *                          Default: 12px
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.0
     */

    public static function custom_header(string $text, string $size = '12px'): string {
        $div = "<div style='font-size: {$size}'>{$text}</div>";
        self::handle_echo($div);
        return $div;
    }


    /**
     * Draw the html elements to open a link in a new tab
     * 
     * @var string  NEW_TAB
     * 
     * @access  public
     * @since   LRS 3.7.1
     */

    const NEW_TAB = "target='_blank' rel='noopener'";


    /**
     * Draw elements to draw a section break in the general admin layout
     * 
     * @param   string|null $title          The title to place on the page for the new section. Parse null to skip. Default: null
     * @param   string      $description    The description to be placed in this section break. May be parsed multiple times
     * 
     * @static
     * @access  public
     * @since   LRS 3.8.0
     * @since   LBF 0.4.1-beta  Added param title
     */

    public static function section_break(?string $title = null, string ...$description): void {
        self::close_standard_page();
        self::standard_column_left();
        if (!is_null($title)) {
            self::item_description_header($title);
        }
        foreach ($description as $i => $desc) {
            self::item_description($desc, $i == 0 ? 0 : 2);
        }
        self::standard_column_right();
    }


    /**
     * Draw out the toggle selection box and container, used mostly in accounts
     * 
     * @param   string  $group_name     The name to draw next to the toggle box
     * @param   string  $toggle         The toggle drawn from Form::toggle()
     * 
     * @static
     * @access  public
     * @since   LRS 3.9.0
     */

    public static function accounts_toggle(string $group_name, string $toggle): void {
        echo "<div class='group_container'>";
        echo "<span class='center_vertical'>$group_name</span>";
        echo "<span class='center_vertical'>$toggle</span>";
        echo "</div>"; // group_container
    }


    /**
     * Draw page tabs
     * 
     * @param   array   $tabs       The data for each tab
     *                              Format: $name => $link
     *                              $link format: page/subpage/tab
     * @param   string  $default    The default tab, in case $_GET is not set
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.0
     * @since   LBR 0.6.0-beta  Renamed $data param to $tabs
     */

    public static function page_tabs(array $tabs, string $default): void {
        echo "<ul class='general_page_tab'>";
        foreach ($tabs as $name => $link) {
            $selected = ' tab_not_selected';
            if (is_null(Config::current_page('tab')) && $link === $default) {
                $selected = '';
            } else {
                $selected = Config::current_page('tab') === $link ? '' : ' tab_not_selected';
            }
            $link = '/' . Config::current_page('page') . '/' . Config::current_page('subpage') . '/' . $link;
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
     * @static
     * @access  public
     * @since   LRS 3.14.0
     */

    public static function tabs(int $n): string {
        $t = '';
        for ($i = 0; $i < $n; $i++) {
            $t .= "\t";
        }
        return $t;
    }


    /**
     * Text to put at the end of all scripts.
     * 
     * @static
     * @access  public
     * @since   LRS 3.14.0
     */

    public static function end_of_script(): void {
        self::lines(2);
    }


    /**
     * Draw a draggable list
     * 
     * @param   array   $data   The contents of each cell
     * 
     * @static
     * @access  public
     * @since   LRS 3.15.0
     */

    public static function draggable_list(array $data): void {
        $id = Hash::random_id_string(5);

        HTML::div([
            'class' => 'draggable_container',
            'id'    => 'draggable_container' . $id
        ]);

        $svg = new SVG(SVGImages::grabber->image());
        $svg->set_viewbox(0, 0, 16, 16);
        foreach ($data as $item) {
            HTML::div_container(
                params: ['class' => 'draggable_entry'],
                content: HTML::span_container(
                    params: ['class' => 'de_item', 'echo' => false],
                    content: $item
                ) .
                    HTML::span_container(
                        params: ['class' => "de_grabber de_grabber{$id}", 'echo' => false],
                        content: $svg->return(),
                    )
            );
        }

        HTML::close_div();
        /**
         * @see https://github.com/SortableJS/Sortable
         * @see https://sortablejs.github.io/Sortable/
         * 
         * @since   LRS 3.15.0
         * 
         */
        $rand_id = Hash::random_id_string();
        HTML::inject_js(<<<JS
        import Sortable from 'lrs-sortablejs';
        JS);
        HTML::inject_js(<<<JS
        const el{$rand_id} = document.getElementById('draggable_container{$id}');
        const sortable{$rand_id} = Sortable.create(el{$rand_id}, {
            swap: true,
            swapClass: 'highlight',
            animation: 150,
            ghostClass: 'draggable_ghost',
            handle: '.de_grabber{$id}',
        });        
        JS);
    }


    /**
     * Draw the standard page left column
     * 
     * @param   array   $params     The params to add to the div.
     *                              Default: []
     * 
     * @static
     * @access  public
     * @since   LRS 3.15.8
     */

    public static function standard_column_left(array $params = []): void {
        $hold = HTML::temporary_change_echo(true);

        if (isset($params['class'])) {
            $params['class'] = 'page_element_description ' . $params['class'];
        } else {
            $params['class'] = 'page_element_description';
        }

        if (!isset($params['name'])) {
            $params['name'] = 'page_element_description';
        }

        HTML::div($params);
        HTML::restore_origonal_echo($hold);
    }


    /**
     * Draw the standard page right column
     * 
     * @param   array   $params             The params to add to the div.
     *                                      Default: []
     * @param   boolean $close_left_column  Whether or not to close the left hand column
     *                                      Default: true
     * 
     * @static
     * @access  public
     * @since   LRS 3.15.8
     */

    public static function standard_column_right(array $params = [], bool $close_left_column = true): void {
        $hold = self::temporary_change_echo(true);

        if ($close_left_column) {
            HTML::close_div(); // page_element_description
        }

        if (!isset($params['maxmin'])) {
            $params['maxmin'] = true;
        }

        if (isset($params['class'])) {
            $params['class'] = 'page_element_main ' . $params['class'];
        } else {
            $params['class'] = 'page_element_main';
        }

        HTML::div($params);

        HTML::div(['class' => 'main_page_maxmin__container']);
        if ($params['maxmin']) {
            $id = Hash::random_id_string();
            HTML::div([
                'class'     => 'main_page_maxmin',
                'name'      => 'minmax_bttn',
                'id'        => $id,
                'data-open' => 0,
            ]);
            HTML::span([
                'id'   => "{$id}_max",
                'name' => 'minmax_max',
            ]);
            $icon = new Icons;
            $svg_max = new SVG($icon->get('maximize', echo: false));
            $svg_max->set_size(18, 18);
            $svg_max->echo();
            HTML::close_span();
            HTML::span([
                'class' => 'hidden',
                'id'    => "{$id}_min",
                'name'  => 'minmax_min',
            ]);
            $svg_min = new SVG($icon->get('minimize', echo: false));
            $svg_min->set_size(18, 18);
            $svg_min->echo();
            HTML::close_span();
            HTML::close_div();
        }
        HTML::close_div();
        HTML::restore_origonal_echo($hold);
    }


    /**
     * Draw the standard single column page start
     * 
     * @param   array   $params     The params to add to the div.
     *                              Default: []
     * 
     * @static
     * @access  public
     * @since   LRS 3.16.0
     */

    public static function standard_single_column(array $params = []): void {
        $hold = self::temporary_change_echo(true);

        if (isset($params['class'])) {
            $params['class'] = 'page_element_main ' . $params['class'];
        } else {
            $params['class'] = 'page_element_main';
        }

        HTML::div($params);
        HTML::restore_origonal_echo($hold);
    }


    /**
     * Close the right / full length column
     * 
     * @static
     * @access  public
     * @since   LRS 3.15.8
     */

    public static function close_standard_page(): void {
        echo "</div>"; // page_element_main
    }


    /**
     * Draw a number of input fields in a single line.
     * 
     * @static
     * @access  public
     * @since   LRS 3.16.0
     */

    public static function set_input_multi_line(): void {
        echo "<div class='multi_line_input'>";
    }


    /**
     * End the line of input fields.
     * 
     * @static
     * @access  public
     * @since   LRS 3.16.0
     */

    public static function close_multi_line_input(): void {
        echo "</div>";
    }


    /**
     * Draw a filter container.
     * 
     * @static
     * @access  public
     * @since   LRS 3.16.0
     */

    public static function table_filters_container(): void {
        echo "<div class='table_filter_container'>";
    }


    /**
     * End the line of table filter container.
     * 
     * @static
     * @access  public
     * @since   LRS 3.16.0
     */

    public static function close_table_filters_container(): void {
        echo "</div>";
    }


    /**
     * Draw an aligned left container.
     * 
     * @static
     * @access  public
     * @since   LRS 3.16.0
     */

    public static function align_entries_left(): void {
        echo "<div class='flex_aligned_left center_vertical'>";
    }


    /**
     * End the blocks aligned left.
     * 
     * @static
     * @access  public
     * @since   LRS 3.16.0
     */

    public static function close_align_entries_left(): void {
        echo "</div>";
    }


    /**
     * When in CLI mode, draw the text in red.
     * 
     * @param   string  $text   The text to print.
     * 
     * @access  public
     * @since   LBF 0.7.0
     */

    public static function print_red(string $text): void {
        self::$cli ??= php_sapi_name() == 'cli';
        if (self::$cli) {
            echo "\033[31m{$text}\033[0m";
        } else {
            echo $text;
        }
    }


    /**
     * When in CLI mode, draw the text in green.
     * 
     * @param   string  $text   The text to print.
     * 
     * @access  public
     * @since   LBF 0.7.0
     */

    public static function print_green(string $text): void {
        self::$cli ??= php_sapi_name() == 'cli';
        if (self::$cli) {
            echo "\033[32m{$text}\033[0m";
        } else {
            echo $text;
        }
    }


    /**
     * When in CLI mode, draw the text in yellow.
     * 
     * @param   string  $text   The text to print.
     * 
     * @access  public
     * @since   LBF 0.7.0
     */

    public static function print_yellow(string $text): void {
        self::$cli ??= php_sapi_name() == 'cli';
        if (self::$cli) {
            echo "\033[33m{$text}\033[0m";
        } else {
            echo $text;
        }
    }

    /**
     * When in CLI mode, draw the text in blue.
     * 
     * @param   string  $text   The text to print.
     * 
     * @access  public
     * @since   LBF 0.7.0
     */

    public static function print_blue(string $text): void {
        self::$cli ??= php_sapi_name() == 'cli';
        if (self::$cli) {
            echo "\033[34m{$text}\033[0m";
        } else {
            echo $text;
        }
    }
}
