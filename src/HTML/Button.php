<?php

namespace LBF\HTML;

use Feather\Icons;
use LBF\App\Config;
use LBF\Auth\Hash;
use LBF\Errors\IO\InvalidInput;
use LBF\Errors\IO\MissingRequiredInput;
use LBF\HTML\HTML;
use LBF\HTML\HTMLMeta;
use LBF\HTML\JS;

/**
 * This class is to draw out various buttons on the page.
 * 
 * use LBF\HTML\Button;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.11.0
 * @since   LRS 3.17.2      Seperated out as a shortcut class to `src/HTML/Buttons.php`.
 * @since   LRS 3.28.0      Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                          Namespace changed from `Framework` to `LBF`.
 * @since   LBF 0.1.6-beta  Added extension `HTMLMeta`.
 * @since   LBF 0.6.0-beta  Merged with `src/HTML/Buttons.php`, removing the need for a shortcut.
 */

class Button extends HTMLMeta {

    /**
     * Handle the creation of assigned class names to the button element.
     * 
     * @param   array   $params     The params parsed on the button.
     * 
     * @return  array   $params     The modified params.
     * 
     * @throws  InvalidInput   If a button colour value is invalid.
     * 
     * @static
     * @access  private
     * @since   LRS 3.16.1
     */

    private static function set_button_class(array $params): array {
        $default_class = 'standard_button';

        if ($params['padding']) {
            $default_class .= ' standard_button__padding';
        }

        if (!isset($params['container']['overwrite'])) {
            $default_class .= ' standard_button__margin';
        } else {
            if (!$params['container']['overwrite']) {
                $default_class .= ' standard_button__margin';
            }
        }

        $permitted_colours = ['default', 'green', 'blue', 'red', 'orange'];

        if (!isset($params['colour']) && isset($params['color'])) {
            $params['colour'] = $params['color'];
        }

        if (isset($params['colour']) && $params['colour'] !== 'default') {
            if (!in_array($params['colour'], $permitted_colours)) {
                throw new InvalidInput('Button colour selected is not a permitted colour');
            }
            $default_class .= " button_colour__{$params['colour']} coloured_button";
        }

        if (!isset($params['class'])) {
            $params['class'] = $default_class;
        } else {
            $params['class'] = "{$default_class} {$params['class']}";
        }

        return $params;
    }


    /**
     * Set the button's container
     * 
     * @param   array   $params     The params parsed on the button.
     * 
     * @return  string   $params     The container.
     * 
     * @static
     * @access  private
     * @since   LRS 3.16.1
     */

    private static function set_button_container(array $params, string $tag): string {
        $container_class = 'standard_button__container';
        if ($params['hidden']) {
            $container_class .= ' hidden';
        }

        if (isset($params['container']['class'])) {
            $container_class .= " {$params['container']['class']}";
        }

        return "<{$tag} class='{$container_class}'>";
    }


    /**
     * A general miscellaneous button with a lot of the styling, padding etc. built in.
     * 
     * ## Options to set
     * - *content*   - The words or images to put in the button.
     * - *container* - Whether or not to overwrite the default container. Should be sent as an array.
     * - *inline*    - Whether or not to draw the button inline by default.
     * - *padding*   - Whether to put in extra padding into the button. By default **true**.
     * - *hidden*    - Whether or not the button should load hidden. By default **false**.
     * - *linebreak* - Whether to put in a linebreak before or after. Options are **'before'** or **'after'**.
     * - *colour*    - The colour of the button. See below for available colours.
     * - *color*     - Same as *colour*; see above.
     * - *link*      - Navigate to this URI, if desired.
     * - *new_tab*   - If a *link* is set, add the option to make the click load a new page. Options are boolean.
     * - *reload*    - Add a JS script to make the button reload the page.
     * 
     * 
     * ## Colours available
     * - default <- default UI grey/brownish colour
     * - red     <- #f44336
     * - blue    <- #008CBA
     * - green   <- #4CAF50
     * 
     * @param   array   $params     Any elements to be added to the button.
     *                              Default: []
     * 
     * @return  string
     * 
     * @throws  MissingRequiredInput   Required param 'content' missing.
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.0
     * @since   LRS 3.11.0  Moved to LBF\HTML\Buttons.php from PageElements.php. Renamed to general() from custom_button()
     * @since   LRS 3.16.1  Revamped completely, now the base of almost all buttons. Removed params $content, $overwrite_class
     */

    public static function general(array $params = []): string {
        if (!isset($params['content'])) {
            throw new MissingRequiredInput("Button \$param attribute 'content' has not been set. \$paramT['content'] must be set.");
        }
        $skip_fields = [
            'content', 'container', 'inline', 'padding', 'hidden',
            'linebreak', 'colour', 'color', 'new_tab', 'reload',
            'tab',
            // 'label', 'hint',
            /**
         * @todo    Would like to add above but not sure how to do without
         *          breaking inline integrity
         */

        ];
        $item = '';

        if (!isset($params['id'])) {
            $params['id'] = Hash::random_id_string();
        }
        if (isset($params['linebreak']) && $params['linebreak'] == 'before') {
            $item .= "<div class='btn_lb'></div>";
        }

        if (isset($params['reload']) && $params['reload']) {
            if (isset($params['onclick'])) {
                $params['onclick'] .= ' window.location.reload()';
            } else {
                $params['onclick'] = 'window.location.reload()';
            }
        }

        /**
         * CONTAINER
         */
        $container = isset($params['container']) ? $params['container'] : [
            'overwrite' => false,
        ];
        if (!isset($container['overwrite'])) {
            $container['overwrite'] = false;
        }
        if (!isset($params['hidden'])) {
            $params['hidden'] = false;
        }
        if (!isset($params['inline'])) {
            $params['inline'] = true;
        }
        $container_tag = $params['inline'] ? 'span' : 'div';

        if (!$container['overwrite']) {
            $item .= self::set_button_container($params, $container_tag);
        }

        if (!isset($params['padding'])) {
            $params['padding'] = true;
        }

        /**
         * BUTTON CLASS
         */
        $params = self::set_button_class($params);

        $params['content'] = "<span id='{$params['id']}__spinner'></span>" . $params['content'];

        $item .= self::html_element_container('button', $params, $skip_fields);

        if (!$container['overwrite']) {
            $item .= "</$container_tag>";
        }

        if (isset($params['linebreak']) && $params['linebreak'] == 'after') {
            $item .= "<div class='btn_lb'></div>";
        }

        self::handle_echo($item, $params);
        return $item;
    }


    /**
     * Backend logic to draw "do this" button.
     * 
     * @param   array   $params     Any elements to be added to the button.
     * @param   string  $content    The content of the button.
     * @param   string  $colour     The colour of the button.
     *                              Default: null
     * 
     * @return  string
     * 
     * @static
     * @access  private
     * @since   LRS 3.16.1
     */

    private static function do_button(array $params, string $content, ?string $colour = null): string {
        $button = '';

        if (!isset($params['content'])) {
            $params['content'] = $content;
        }

        if (!is_null($colour) && !isset($params['colour'])) {
            $params['colour'] = $colour;
        }

        $hold = self::temporary_change_echo(false);
        $button .= self::general($params);
        self::restore_origonal_echo($hold);

        return $button;
    }


    /**
     * Apply button, to apply whatever changes need to be applied. For onClick type instructions, an ID must be set.
     * 
     * Default colour is green.
     * 
     * @param   array   $params     Any elements to be added to the button.
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.0
     * @since   LRS 3.11.0  Moved to LBF\HTML\Buttons.php from PageElements.php. Renamed to apply() from apply_button()
     * @since   LRS 3.16.1  Removed param $overwrite_class
     */

    public static function apply(array $params): string {
        $button = self::do_button($params, 'Apply', 'green');
        self::handle_echo($button, $params);
        return $button;
    }


    /**
     * Edit button, to edit whatever element need to be edited. For onClick type instructions, an ID must be set.
     * 
     * @param   array   $params     Any elements to be added to the button.
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.13.0
     * @since   LRS 3.16.1  Removed param $overwrite_class
     */

    public static function edit(array $params): string {
        $button = self::do_button($params, 'Edit');
        self::handle_echo($button, $params);
        return $button;
    }


    /**
     * Save button, to edit whatever element need to be edited. For onClick type instructions, an ID must be set.
     * 
     * Default colour: green.
     * 
     * @param   array   $params     Any elements to be added to the button.
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.4.0
     * @since   LRS 3.6.0     Added param $id
     * @since   LRS 3.11.0    Moved to LBF\HTML\Buttons.php from PageElements.php. Renamed to save() from save_button()
     * @since   LRS 3.13.0    Removed params $onclick, $id, added param $params - largely rewritten
     */

    public static function save(array $params): string {
        $button = self::do_button($params, 'Save', 'green');
        self::handle_echo($button, $params);
        return $button;
    }


    /**
     * Ok button, to acknowledge a change or a notice on a page.
     * 
     * @param   array   $params     Any elements to be added to the button.
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.16.1
     */

    public static function ok(array $params): string {
        if (isset($params['reload']) && $params['reload']) {
            if (!isset($params['padding'])) {
                $params['padding'] = false;
            }
            if (!isset($params['colour'])) {
                $params['colour'] = 'default';
            }
        }
        $button = self::do_button($params, 'OK', 'green');
        self::handle_echo($button, $params);
        return $button;
    }


    /**
     * Draw a search button, with a clickable, magnifying glass icon
     * 
     * @param   array   $params     Any elements to be added to the button.
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.16.1
     */

    public static function search(array $params): string {
        $icon = new Icons;
        $button = self::do_button($params, $icon->get('search', echo: false));
        self::handle_echo($button, $params);
        return $button;
    }


    /**
     * Draw a print button, with a clickable, printer icon
     * 
     * @param   array   $params         Any elements to be added to the button.
     * @param   boolean $print_dailogue Whether or not to use window.print().
     *                                  Default: true
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.3.2
     * @since   LRS 3.11.0  Moved to LBF\HTML\Buttons.php from PageElements.php. Renamed to print() from print_button()
     * @since   LRS 3.13.0  Revamped and removed param $onClick, added $print_dailogue & $params
     * @since   LRS 3.16.1  Revamped and turned into a simple button.
     */

    public static function print(array $params, bool $print_dailogue = false): string {
        if ($print_dailogue) {
            if (isset($params['onclick'])) {
                $params['onclick'] .= " window.print()";
            } else {
                $params['onclick'] = 'window.print()';
            }
        }
        $icon = new Icons;
        $button = self::do_button($params, $icon->get('printer', echo: false));

        self::handle_echo($button, $params);
        return $button;
    }


    /**
     * Draw a reset button to load the page without all the $_GET params
     * 
     * @param   array   $params     Any elements to be added to the button.
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.0
     * @since   LRS 3.11.0  Moved to LBF\HTML\Buttons.php from PageElements.php. Renamed to reset() from reset_button()
     * @since   LRS 3.16.1  Completely reworked to use new button methods.
     */

    public static function reset(array $params = []): string {
        $path = implode('/', Config::current_page());
        if (isset($params['onclick'])) {
            $params['onclick'] .= " window.location=`/{$path}`";
        } else {
            $params['onclick'] = "window.location=`/{$path}`";
        }
        $button = self::do_button($params, 'Reset', 'blue');

        self::handle_echo($button, $params);
        return $button;
    }


    /**
     * Perform the task of drawing a 'go to' type button such as 'back' or 'cancel'
     * 
     * @param   array   $params     The params to be added to the button
     * @param   string  $content    The text on the button, if not overwritten.
     * 
     * @return  string
     * 
     * @throws  MissingRequiredInput   When `$params['link']` is missing.
     * 
     * @static
     * @access  private
     * @since   LRS 3.16.1
     */

    private static function go_to_button_template(array $params, string $content): string {
        if (!isset($params['link'])) {
            throw new MissingRequiredInput("Button \$param attribute 'link' has not been set. \$paramT['link'] must be set.");
        }
        $button = '';

        if (!isset($params['content'])) {
            $params['content'] = $content;
        }
        if (!isset($params['colour'])) {
            $params['colour'] = 'default';
        }

        if (isset($params['class'])) {
            if (str_contains($params['class'], 'todobttns')) {
                $button .= "<div class='backbutton'>";
            }
        }

        $hold = self::temporary_change_echo(false);
        $button .= self::general($params);
        self::restore_origonal_echo($hold);

        if (isset($params['class'])) {
            if (str_contains($params['class'], 'todobttns')) {
                $button .= "</div>";
            }
        }

        return $button;
    }


    /**
     * A button to send the user back to an app defined page
     * 
     * @param   string  $loc    The location to which the button must point
     * 
     * @return  string
     * 
     * @static
     * @since   LRS 3.1.0
     * @since   LRS 3.6.0   Removed @param $class as this is moved to self::$button_class
     * @since   LRS 3.11.0  Moved to LBF\HTML\Buttons.php from PageElements.php. Renamed to back() from back_button()
     * @since   LRS 3.16.1  Rewritten to use the new button method.
     */

    public static function back(array $params): string {
        $button = self::go_to_button_template($params, 'Back');
        self::handle_echo($button, $params);
        return $button;
    }



    /**
     * A button to cancel the page you are working on and go back to the defined page
     * Functionally the same as self::back().
     * 
     * @param   string  $loc    The location to which the button must point
     * 
     * @return  string
     * 
     * @static
     * @since   LRS 3.7.6
     * @since   LRS 3.11.0  Moved to LBF\HTML\Buttons.php from PageElements.php. Renamed to cancel() from cancel_button()
     * @since   LRS 3.16.1  Rewritten to use the new button method.
     */

    public static function cancel(array $params): string {
        if (!isset($params['colour'])) {
            $params['colour'] = 'blue';
        }
        $button = self::go_to_button_template($params, 'Cancel');
        self::handle_echo($button, $params);
        return $button;
    }


    /**
     * Draw the floating submit button.
     * 
     * @param   array   $params     Any elements to be added to the button.
     *                              Default: []
     * 
     * @return  string
     * 
     * @static
     * @since   LRS 3.4.12
     * @since   LRS 3.9.0   Added @params $button_type & $onClick
     * @since   LRS 3.11.0  Moved to LBF\HTML\Buttons.php from PageElements.php. Renamed to floating_submit() from floating_submit_button()
     * @since   LRS 3.13.0  Reworked. Changed params to $button_type_submit, $params & $overwrite_class
     * @since   LRS 3.16.1  Reworked again, moved to new button standard - removed params $button_type_submit, $overwrite_class
     */

    public static function floating_submit(array $params = []): string {
        $button = '';
        if (!is_apple_mobile()) {
            $button .= "<div class='floaty_submit text_align_center' id='floaty_submit'>";
        } else {
            $button .= "<div class='floaty_submit text_align_center'>";
        }

        $params['type'] = 'submit';
        $params['container'] = ['overwrite' => true];
        if (!isset($params['id'])) {
            $params['id'] = 'reg_submit_bttn';
        }
        if (!isset($params['content'])) {
            $params['content'] = 'Submit';
        }
        if (isset($params['class'])) {
            $params['class'] .= " reg_submit_bttn";
        } else {
            $params['class'] = "reg_submit_bttn";
        }

        $hold = self::temporary_change_echo(false);
        $button .= self::general($params);
        self::restore_origonal_echo($hold);

        $button .= "</div>"; // floaty_submit

        self::handle_echo($button, $params);
        return $button;
    }


    /**
     * Draw the floating top and bottom buttons
     * 
     * @static
     * @since   LRS 3.6.0
     * @since   LRS 3.11.0  Moved to LBF\HTML\Buttons.php from PageElements.php.
     */

    public static function floating_top_bottom_buttons(): void {
        // if (is_apple_mobile()) {
        //     return;
        // }
        HTML::div(['class' => 'floating_tb_buttons_contain']);

        /**
         * The JS which handles the buttons going up & down.
         * 
         * @param   string  $id     The id of the element being clicked on.
         * 
         * @return  string
         * 
         * @since   LRS 3.21.0
         */
        $js = function ($id) {
            return <<<JS
            import { zoom_updown } from '/vendor/projector22/lourie-basic-framework/src/js/lib/ui.js';
            const btn = document.getElementById('{$id}');
            btn.addEventListener('click', function () {
                zoom_updown(this.id);
            });
            JS;
        };

        $icon = new Icons;

        HTML::div_container(
            ['class' => 'ftb_button', 'id' => 'ftb_up'],
            $icon->get('chevrons-up', echo: false) 
        );

        HTML::div_container(
            ['class' => 'ftb_button', 'id' => 'ftb_down'],
            $icon->get('chevrons-down', echo: false)
        );

        HTML::script($js('ftb_up'), ['echo' => true, 'type' => 'module']);
        HTML::script($js('ftb_down'), ['echo' => true, 'type' => 'module']);

        HTML::close_div(); // floating_tb_buttons_contain
    }


    /**
     * The main type of button
     * 
     * Choices: 'bttn' & 'subjectbttn'
     * 
     * @var string  $interface_bttn_class   Default: 'bttn'
     * 
     * @static
     * @since   LRS 3.9.0
     * @since   LRS 3.11.0  Moved to LBF\HTML\Buttons.php from PageElements.php.
     */

    public static string $interface_bttn_class = 'bttn';

    /**
     * Draw a menu button
     * 
     * @param   string  $button_text    The text which to draw.
     * @param   string  $link           The link to put on the button.
     * @param   array   $params         Any extra parameters to add to the button.
     *                                  Default: null
     * 
     * @return  string
     * 
     * @static
     * @since   LRS 3.9.0
     * @since   LRS 3.11.0  Moved to LBF\HTML\Buttons.php from PageElements.php. Renamed to interface() from interface_button()
     * @since   LRS 3.13.0  Revamped and removed params, $onclick, $extra_class, $id, added $params
     */

    public static function interface(string $button_text, string $link, array $params = []): string {
        $button = '';
        $button .= "<div class='general_button'>";
        if (isset($params['class']) && $params['class'] != '') {
            $params['class'] .= ' interface_button ' . self::$interface_bttn_class;
        } else {
            $params['class'] = 'interface_button ' . self::$interface_bttn_class;
        }
        $params['href'] = $link;
        $params['text'] = $button_text;

        $button .= HTML::a($params + ['echo' => false]);
        $button .= "</div>";
        self::handle_echo($button, $params);
        return $button;
    }


    /**
     * Draw a login password field hide / unhide eye button
     * 
     * @param   array   $params     All the fields to put onto the button
     * 
     * @return  string
     * 
     * @static
     * @access  public
     * @since   LRS 3.12.8
     */

    public static function login_eye(array $params = []): string {
        $button = '<button';
        foreach ($params as $index => $value) {
            $button .= " {$index}='{$value}'";
        }
        $icon = new Icons;
        $button .= '>' . $icon->get('eye-off', echo: false) . '</button>';
        self::handle_echo($button, $params);
        return $button;
    }


    /**
     * A 'link' button
     * 
     * @param   string  $text       The text of the link
     * @param   array   $params     Any parameters to add to the html element
     *                              Default: []
     * @param   string  $function   Which javascript function to run, this saves adding am onclick='' param
     *                              Default: 'void(0)'
     * 
     * @return  string|void
     * 
     * @static
     * @access  public
     * @since   LRS 3.13.0
     */

    public static function link_button(string $text, array $params = [], string $function = 'void(0)'): string {
        $params['href'] = "javascript:{$function}";
        $params['text'] = $text;
        $button = HTML::a($params);

        self::handle_echo($button, $params);
        return $button;
    }
}
