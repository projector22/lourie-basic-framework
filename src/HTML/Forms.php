<?php

namespace LBS\HTML;

use Exception;
use App\Enums\SVGImages;
use LBS\HTML\JS;
use LBS\HTML\HTML;
use SVGTools\SVG;

/**
 * This class is to draw out form elements on the page
 * 
 * use LBS\HTML\Forms;
 * 
 * @composer-requires    SVGTools\SVG
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.6.0
 * @since   3.11.0  Moved to LBS\HTML namespace from PageElements
 */

class Forms {

    /**
     * Whether or not to echo the element. If false the element will be returned instead
     * 
     * @var boolean $echo  Default: true
     * 
     * @access  public
     * @since   3.6.0
     */

    public static bool $echo = true;


    /**
     * Draw and add the a label onto an html element
     * 
     * @param   array   $params     The parameters parsed to draw onto the input field
     * 
     * @return  string
     * 
     * @access  private
     * @since   3.12.8
     * @since   3.16.0 Added a built in 'required' element.
     */

    private static function label( array $params ): string {
        $item = '';
        if ( isset ( $params['label'] ) ) {
            $set_required = isset ( $params['required'] ) && $params['required'] ? self::$required_default : '';
            $item .= "<label for='{$params['id']}' class='item_heading'>{$params['label']}{$set_required} </label><br>";
        }
        return $item;
    }


    /**
     * Set the container element of an input element.
     * 
     * @param   array   $params     The parameters parsed to draw onto the container field
     * @param   string  $class      The class of div
     * 
     * @return  string
     * 
     * @access  private
     * @since   3.16.0
     */

    private static function element_container( array $params, string $class ): string {
        $set_flex = '';
        if ( isset ( $params['flex'] ) ) {
            if ( !is_numeric( $params['flex'] ) ) {
                throw new \Exception( "The flex parameter must be a number" );
            }
            $set_flex = " style='flex: {$params['flex']};'";
        }

        $element = "<div id='{$params['id']}__container' class='{$class}'{$set_flex}";
        if ( isset( $params['container']['name'] ) ) {
            $element .= " name='{$params['container']['name']}'";
        }
        $element .= ">";
        return $element;
    }


    /**
     * Set the container element of a text / textarea / selectbox type input
     * 
     * @param   array   $params     The parameters parsed to draw onto the container field
     * 
     * @return  string
     * 
     * @access  private
     * @since   3.16.0
     */

    private static function text_input_container( array $params ): string {
        $class = 'standard_text_input_container';
        if ( isset( $params['container']['class'] ) ) {
            $class .= " {$params['container']['class']}";
        }
        if ( isset( $params['hidden'] ) && $params['hidden'] ) {
            $class .= " hidden";
        }
        return self::element_container( $params, $class );
    }


    /**
     * Set the container element of a text / textarea / selectbox type input
     * 
     * @param   array   $params     The parameters parsed to draw onto the container field
     * 
     * @return  string
     * 
     * @access  private
     * @since   3.16.0
     */

    private static function checkbox_input_container ( array $params ): string {
        $class = 'standard_checkbox_input_container';
        if ( isset( $params['label'] ) ) {
            $class .= ' center_vertical';
        }
        return self::element_container( $params, $class );
    }


    /**
     * Set the container element of a toggle switch element
     * 
     * @param   array   $params     The parameters parsed to draw onto the container field
     * 
     * @return  string
     * 
     * @access  private
     * @since   3.16.1
     */

    private static function toggle_container( array $params ): string {
        $class = 'standard_toggle_input_container';
        if ( isset( $params['container']['class'] ) ) {
            $class .= " {$params['container']['class']}";
        }
        return self::element_container( $params, $class );
    }


    /**
     * Set input field hint.
     * 
     * @param   array   $params     The parameters parsed to draw onto the hint field
     * 
     * @return  string
     * 
     * @access  private
     * @since   3.16.0
     */

    private static function element_hint( array $params ): string {
        $item = '';
        if ( isset ( $params['hint'] ) ) {
            $item .= "<div id='{$params['id']}__hint' class='standard_text_input_hint'>{$params['hint']}</div>";
        }
        return $item;
    }


    /**
     * Set an element validation entry.
     * 
     * @param   array   $params     The parameters parsed to draw onto the validation field
     * 
     * @return  string
     * 
     * @access  private
     * @since   3.16.0
     */

    private static function element_validation( array $params ): string {
        $item = '';
        if ( isset ( $params['validate'] ) || isset ( $params['required'] ) && $params['required'] ) {
            $item .= "<div id='{$params['id']}__validation_feedback' class='std_validation_feedback'></div>";

            $hold = JS::$echo;
            JS::$echo = false;
    
            $validate = [];
            $nil_value = '';
            
            if ( isset ( $params['validate'] ) ) {
                if ( !is_array( $params['validate'] ) ) {
                    throw new \Exception( "Validations must be parsed as an array" );
                }
                $validate += $params['validate'];
            }
            if ( isset ( $params['required'] ) && $params['required'] ) {
                $validate += ['required' => true];
                if ( isset ( $validate['nil_value'] ) ) {
                    $nil_value = $validate['nil_value'];
                    unset( $validate['nil_value'] );
                }
            }
            $validate  = json_encode( $validate );
            $validator = 'validator' . random_id_string( 5 );
            $item .= JS::script_module( "
import Input_Validation from './src/js/lib/input_validation.js';
const {$validator} = new Input_Validation('{$params['id']}','{$params['id']}__validation_feedback', '{$nil_value}');
{$validator}.general_validator({$validate});"
            );
            JS::$echo = $hold;
        }
        return $item;
    }


    /**
     * Draw the text of putting a red asterisk next to a field label to indicate
     * the field is required.
     * 
     * @var string  REQUIRED_STAR
     * 
     * @access  public
     * @since   3.16.0
     */

    const REQUIRED_STAR = " <span class='required_star'>*</span>";

    /**
     * Draw the text `(Required)` next to a field label to indicate the field 
     * is required.
     * 
     * @var string  REQUIRED_TEXT
     * 
     * @access  public
     * @since   3.16.0
     */

    const REQUIRED_TEXT = " (Required)";

    /**
     * Set the default method of indicating a field is required.
     * 
     * ## Options
     * - Form::REQUIRED_STAR
     * - Form::REQUIRED_TEXT
     * 
     * @var string  $required_default   Default: `Form::REQUIRED_TEXT`
     * 
     * @access  public
     * @since   3.16.0
     */

    public static string $required_default = self::REQUIRED_TEXT;


    /**
     * Draw the new text input field. This text needs to be updated
     * 
     * Use the size attribute to overwrite the default length.
     * 
     * @param   array   $params     The params to parse into the text field.
     *                              Default: `[]`
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.12.8
     * @since   3.16.0 Revamped with new styling, labels, hints and validation.
     */

    public static function text( array $params = [] ) {
        if ( !isset( $params['id'] ) ) {
            throw new \Exception( "Text field ID not set" );
        }
        $skip_fields = ['label', 'hint', 'validate', 'flex', 'container', 'hidden'];
        $item = '';

        if ( !isset ( $params['type'] ) ) {
            $params['type'] = 'text';
        }

        $container = isset( $params['container'] ) ? $params['container'] : [
            'overwrite' => false,
        ];
        if ( !isset( $container['overwrite'] ) ) {
            $container['overwrite'] = false;
        }

        /**
         * @todo
         * 
         * Create a list of 'illegal' inputs, such as 'checkbox'
         * 
         * @since   3.16.0
         */

        /**
         * Set a default size
         */
        if ( !isset ( $params['size'] ) ) {
            switch ( $params['type'] ) {
                case 'date':
                    $params['size'] = 20;
                    break;
                case 'time':
                    $params['size'] = 8;
                    break;
                case 'search':
                    $params['size'] = 35;
                    break;
            }
        }

        /**
         * Container
         */

        if ( !$container['overwrite'] ) {
            $item .= self::text_input_container( $params );
        }

        /**
         * Label
         */

        $item .= self::label( $params );

        /**
         * Input Field
         */

        $standard_class = 'basic_form_properties form_border standard_text_input';
        if ( !isset ( $params['size'] ) ) {
            $standard_class .= ' st_input_width_default';
        }

        if ( isset ( $params['class'] ) ) {
            $params['class'] = $standard_class . ' ' . $params['class'];
        } else {
            $params['class'] = $standard_class;
        }
        $item .= "<input";
        foreach ( $params as $field => $value ) {
            if ( in_array( $field, $skip_fields ) ) {
                continue;
            }
            switch ( $field ) {
                case 'autofocus':
                    $item .= $value ? ' autofocus' : '';
                    break;
                case 'disabled':
                    $item .= $value ? ' disabled' : '';
                    break;
                case 'readonly':
                    $item .= $value ? ' readonly' : '';
                    break;
                case 'required':
                    $item .= $value ? ' required' : '';
                    break;
                default:
                    $item .= " {$field}='{$value}'";
            }
        }

        if ( isset ( $params['validate'] ) || isset( $params['required'] ) && $params['required'] ) {
            $item .= " data-requires-validation=1";
        }

        $item .= ">";

        /**
         * Hint
         */

        $item .= self::element_hint( $params );

        
        /**
         * Validation
         */

        $item .= self::element_validation( $params );

        if ( !$container['overwrite'] ) {
            $item .= "</div>"; // Container
        }

        if ( self::$echo ) {
            echo $item;
        } else {
            return $item;
        }
    }


    /**
     * Call the name of a text field elements
     * 
     * @param   string  $name   The name of the method called
     * @param   array   $arguments
     * 
     * @access  public
     * @since   3.16.0
     */

    public static function __callStatic( $name, $arguments ) {
        if ( $name == 'colour' ) {
            $name = 'color';
        }
        $text_types = [
            'email', 'color', 'date', 'email', 'month', 'number',
            'password', 'range', 'search', 'tel', 'time', 'url',
            'week',
        ];
        if ( !in_array ( $name, $text_types ) ) {
            throw new \Exception( "Method '{$name}' does not exist." );
        }
        $arguments[0]['type'] = $name;
        self::text( $arguments[0] );
    }


    /**
     * Draw a html <select></select> box.
     * 
     * @param   string  $params         The parameters to add to the select box.
     *                                  Default: `[]`
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.8.0
     * @since   3.13.0  Rewritten and revamped
     * @since   3.13.3  Added param $is_multiple
     * @since   3.16.0  Revamped with new styling, labels, hints and validation. Removed params $data, $is_multiple
     */

    public static function select_box( array $params = [] ) {
        if ( !isset ( $params['id'] ) ) {
            throw new \Exception( "Select box field ID not set" );
        }
        if ( !isset ( $params['data'] ) ) {
            throw new \Exception( "Select box field data not set" );
        }

        $skip_fields = [
            'label', 'hint', 'validate', 'flex', 'data', 'container',
            'hidden',
        ];
        $item = '';

        $container = isset( $params['container'] ) ? $params['container'] : [
            'overwrite' => false,
        ];
        if ( !isset( $container['overwrite'] ) ) {
            $container['overwrite'] = false;
        }

        /**
         * Container
         */
        if ( !$container['overwrite'] ) {
            $item .= self::text_input_container( $params );
        }

        /**
         * Label
         */

        $item .= self::label( $params );

        /**
         * Input Field
         */

        $standard_class = 'basic_form_properties form_border standard_text_input st_selbox_width_default';

        if ( isset ( $params['class'] ) ) {
            $params['class'] = $standard_class . ' ' . $params['class'];
        } else {
            $params['class'] = $standard_class;
        }
        $item .= "<select";
        foreach ( $params as $field => $value ) {
            if ( in_array( $field, $skip_fields ) ) {
                continue;
            }
            switch ( $field ) {
                case 'autofocus':
                    $item .= $value ? ' autofocus' : '';
                    break;
                case 'disabled':
                    $item .= $value ? ' disabled' : '';
                    break;
                case 'multiple':
                    $item .= $value ? ' multiple' : '';
                    break;
                case 'required':
                    $item .= $value ? ' required' : '';
                    break;
                default:
                    $item .= " {$field}='{$value}'";
            }
        }
        if ( isset ( $params['validate'] ) || isset( $params['required'] ) && $params['required'] ) {
            $item .= " data-requires-validation=1";
            if ( isset( $params['validate']['nil_value'] ) ) {
                if ( !str_contains( $params['data'], "value='{$params['validate']['nil_value']}'" ) ) {
                    $item .= " data-validated=1";
                }
            }
        }
        $item .= ">{$params['data']}</select>";

        /**
         * Hint
         */

        $item .= self::element_hint( $params );

        /**
         * Validation
         */

        $item .= self::element_validation( $params );

        if ( !$container['overwrite'] ) {
            $item .= "</div>"; // Container
        }

        if ( self::$echo ) {
            echo $item;
        } else {
            return $item;
        }
    }


    /**
     * Draw a text area, using the <textarea> tag
     * 
     * @param   string  $params         The parameters to be attached to the text area
     *                                  Default: `[]`
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.8.0
     * @since   3.13.0  Revamped and replaced all params
     * @since   3.16.0  Revamped with new styling, labels, hints and validation. Removed param  $draw_counter
     */

    public static function textarea( array $params = [] ) {
        if ( !isset ( $params['id'] ) ) {
            throw new \Exception( "Select box field ID not set" );
        }
        
        $skip_fields = [
            'label', 'hint', 'validate', 'flex', 'value', 'counter', 'resize',
            'container', 'hidden',
        ];
        $item = '';

        /**
         * Container
         */
        $container = isset( $params['container'] ) ? $params['container'] : [
            'overwrite' => false,
        ];
        if ( !isset( $container['overwrite'] ) ) {
            $container['overwrite'] = false;
        }
        if ( !$container['overwrite'] ) {
            $item .= self::text_input_container( $params );
        }

        /**
         * Label
         */

        $item .= self::label( $params );

        /**
         * Input Field
         */

        $standard_class = 'basic_form_properties form_border standard_text_input';
        if ( !isset ( $params['size'] ) ) {
            $standard_class .= ' st_input_width_default';
        }

        if ( !isset( $params['resize'] ) ) {
            $standard_class .= ' resize_none';
        } else {
            switch ( $params['resize'] ) {
                case "1": // true
                    $standard_class .= ' resize_both';
                    break;
                case 'both':
                    $standard_class .= ' resize_both';
                    break;
                case 'none':
                    $standard_class .= ' resize_none';
                    break;
                case "0": // false
                    $standard_class .= ' resize_none';
                    break;
                case 'horizonal':
                    $standard_class .= ' resize_horizonal';
                    break;
                case 'vertical':
                    $standard_class .= ' resize_vertical';
                    break;
                default:
                    $standard_class .= ' resize_none';
            }
        }
        
        if ( !isset ( $params['value'] ) ) {
            $params['value'] = '';
        }

        if ( !isset( $params['counter'] ) ) {
            $params['counter'] = true;
        }
        
        if ( isset ( $params['class'] ) ) {
            $params['class'] = $standard_class . ' ' . $params['class'];
        } else {
            $params['class'] = $standard_class;
        }
        $item .= "<textarea";
        foreach ( $params as $field => $value ) {
            if ( in_array( $field, $skip_fields ) ) {
                continue;
            }
            switch ( $field ) {
                case 'autofocus':
                    $item .= $value ? ' autofocus' : '';
                    break;
                case 'disabled':
                    $item .= $value ? ' disabled' : '';
                    break;
                case 'readonly':
                    $item .= $value ? ' readonly' : '';
                    break;
                case 'required':
                    $item .= $value ? ' required' : '';
                    break;
                default:
                    $item .= " {$field}='{$value}'";
            }
        }
        if ( isset ( $params['validate'] ) || isset( $params['required'] ) && $params['required'] ) {
            $item .= " data-requires-validation=1";
        }

        $item .= ">{$params['value']}</textarea>";

        if ( $params['counter'] && $params['id'] != '' ) {
            $div_id = random_id_string( 7 );
            $input = 'counter' . random_id_string( 5 );
            $item .= "<div id='{$div_id}' class='text_area_counter'></div>";
            $js_echo_hold = JS::$echo;
            JS::$echo = false;
            $item .= JS::script_module( "
                import { text_area_text_counter } from './src/js/lib/forms.js';
                const $input = document.getElementById('{$params['id']}');
                $input.addEventListener('keyup', function(event) {
                    text_area_text_counter('{$params['id']}', '{$div_id}')
                });"
            );
            JS::$echo = $js_echo_hold;
        }

        /**
         * Hint
         */

        $item .= self::element_hint( $params );

        /**
         * Validation
         */

        $item .= self::element_validation( $params );

        $item .= "</div>"; // Container

        if ( self::$echo ) {
            echo $item;
        } else {
            return $item;
        }
    }


    /**
     * Draw an <input type='checkbox'> element.
     * 
     * @param   string  $params     The fields to be added to the checkbox input.
     *                              Default: []
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.8.0
     * @since   3.16.0  Revamped
     */

    public static function checkbox( array $params = [] ) {
        if ( !isset( $params['id'] ) ) {
            throw new \Exception( "Checkbox field ID not set" );
        }

        $skip_fields = ['label', 'position', 'container'];
        $item = '';

        $container = isset( $params['container'] ) ? $params['container'] : [
            'overwrite' => false,
        ];
        if ( !isset( $container['overwrite'] ) ) {
            $container['overwrite'] = false;
        }

        if ( !isset ( $params['position'] ) ) {
            $params['position'] = 'after';
        } else {
            if ( !in_array( $params['position'], ['before', 'after'] ) ) {
                throw new \Exception( "Parameter 'Position' may be either 'before' or 'after', not '{$params['position']}'" );
            }
        }

        // Container
        if ( !$container['overwrite'] ) {
            $item .= self::checkbox_input_container( $params );
        }

        if ( $params['position'] == 'before' ) {
            if ( isset( $params['label'] ) ) {
                $item .= "<label for='{$params['id']}'>{$params['label']} </label>";
            }
        }

        if ( !isset ( $params['class'] ) ) {
            $params['class'] = 'standard_checkbox';
        } else {
            $params['class'] = 'standard_checkbox ' . $params['class'];
        }

        $item .= "<input type='checkbox'";
        foreach ( $params as $field => $value ) {
            if ( in_array( $field, $skip_fields ) ) {
                continue;
            }
            switch ( $field ) {
                case 'autofocus':
                    $item .= $value ? ' autofocus' : '';
                    break;
                case 'checked':
                    $item .= $value ? ' checked' : '';
                    break;
                case 'disabled':
                    $item .= $value ? ' disabled' : '';
                    break;
                case 'indeterminate':
                    $item .= $value ? ' indeterminate' : '';
                    break;
                case 'required':
                    $item .= $value ? ' required' : '';
                    break;
                default:
                    $item .= " {$field}='{$value}'";
            }
        }
        $item .= ">";
        if ( $params['position'] == 'after' ) {
            if ( isset( $params['label'] ) ) {
                $item .= "<label for='{$params['id']}'> {$params['label']}</label>";
            }
        }

        if ( !$container['overwrite'] ) {
            $item .= "</div>";
        }

        if ( self::$echo ) {
            echo $item;
        } else {
            return $item;
        }
    }


    /**
     * Draw a standard radio input
     * 
     * @param   string  $params     The fields to be added to the radio input.
     *                              Default: []
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.16.1
     */

    public static function radio( array $params = [] ) {
        if ( !isset( $params['name'] ) ) {
            throw new Exception( "Missing \$param attribute 'name'. Radio buttons require the 'name' attribute to be set." );
        }

        if ( !isset( $params['id'] ) ) {
            $params['id'] = random_id_string( 7 );
        }

        if ( isset ( $param['label'] ) && !isset ( $param['id'] ) ) {
            throw new Exception( "Missing \$param attribute 'id'. A label require the 'id' attribute to be set." );
        }

        $skip_fields = ['label', 'position'];
        $item = '';

        if ( !isset ( $params['position'] ) ) {
            $params['position'] = 'after';
        } else {
            if ( !in_array( $params['position'], ['before', 'after'] ) ) {
                throw new \Exception( "Parameter 'Position' may be either 'before' or 'after', not '{$params['position']}'" );
            }
        }

        // Container
        $item .= self::checkbox_input_container( $params );

        if ( $params['position'] == 'before' ) {
            if ( isset( $params['label'] ) ) {
                $item .= "<label for='{$params['id']}'>{$params['label']} </label>";
            }
        }

        if ( !isset ( $params['class'] ) ) {
            $params['class'] = 'standard_radiobox';
        } else {
            $params['class'] = 'standard_radiobox ' . $params['class'];
        }

        $item .= "<input type='radio'";
        foreach ( $params as $field => $value ) {
            if ( in_array( $field, $skip_fields ) ) {
                continue;
            }
            switch ( $field ) {
                case 'checked':
                    $item .= $value ? ' checked' : '';
                    break;
                case 'disabled':
                    $item .= $value ? ' disabled' : '';
                    break;
                default:
                    $item .= " {$field}='{$value}'";
            }
        }
        $item .= ">";
        if ( $params['position'] == 'after' ) {
            if ( isset( $params['label'] ) ) {
                $item .= "<label for='{$params['id']}'> {$params['label']}</label>";
            }
        }

        $item .= "</div>";

        if ( self::$echo ) {
            echo $item;
        } else {
            return $item;
        }
    }


    /**
     * Draw a toggle check box.
     * 
     * ### Options
     * 
     * - Set $param['on'] and $param['off'] to put a label on either side of the toggle.
     * - Set $param['label'] to put a label on top of the toggle.
     * - Set $param['hint'] to put a descriptive hint underneath the toggle.
     * - $param['container] must be set as an array and may contain the following params
     * - 'overwrite' => boolean <- setting to true will remove the container.
     * - 'class'     => string.
     * - $param['checked'] & $param['disabled'] should be set to true or false. It defaults to false
     * 
     * @param   array   $params         The properties to add to the toggle.
     *                                  Default: []
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.7.6
     * @since   3.13.0    Updated and split the labels into it's own method
     * @since   3.16.1  Revamped and removed params $id & $checked.
     */

    public static function toggle( array $params = [] ) {
        if ( !isset( $params['id'] ) ) {
            throw new \Exception( "Text field ID not set" );
        }
        if ( !isset ( $params['checked'] ) ) {
            $params['checked'] = false;
        }
        if ( $params['checked'] != true && $params['checked'] != false ) {
            throw new \Exception( "Invalid value for param 'checked' set. It must be either true or false" );
        }

        $skip_fields = ['label', 'hint', 'container', 'off', 'on'];
        $item = '';

        $container = isset( $params['container']['overwrite'] ) ? ['overwrite' => $params['container']] : [
            'overwrite' => false,
        ];

        /**
         * Container
         */

        if ( !$container['overwrite'] ) {
            $item .= self::toggle_container( $params );
        }

        /**
         * Label
         */

        $item .= self::label( $params );

        /**
         * Off
         */

        if ( isset ( $params['on'] ) || isset( $params['off']) ) {
            $item .= "<div class='selector_contain'>";
            if ( isset( $params['off'] ) ) {
                $item .= "<span class='selector_text'>{$params['off']}</span>";
            }
        }

        /**
         * The toggle, wrapped in a label so the whole thing is clickable.
         */
        $item .= "<label class='selector_switch'>";
        $item .= "<input type='checkbox'";
        foreach ( $params as $field => $value ) {
            if ( in_array( $field, $skip_fields ) ) {
                continue;
            }
            switch ( $field ) {
                case 'disabled':
                    $item .= $value ? ' disabled' : '';
                    break;
                case 'checked':
                    $item .= $value ? ' checked' : '';
                    break;
                default;
                    $item .= " {$field}='{$value}'";
            }
        }

        $item .= "><span class='slider round'></span>";
        $item .= "</label>";

        /**
         * On
         */

        if ( isset ( $params['on'] ) || isset( $params['off']) ) {
            if ( isset( $params['on'] ) ) {
                $item .= "<span class='selector_text'>{$params['on']}</span>";
            }
            $item .= "</div>"; // On / Off Labels
        }

        /**
         * Hint
         */

        $item .= self::element_hint( $params );

        if ( !$container['overwrite'] ) {
            $item .= "</div>"; // Container
        }

        if ( self::$echo ) {
            echo $item;
        } else {
            return $item;
        }
    }


    /**
     * Draw a combo box.
     * 
     * ### Options
     * - $param['data'] & $param['id'] is required.
     * 
     * @see Form::text for all aditional params
     * 
     * @param   string  $params         The parameters to add to the combo box, list and autocomplete will be overwritten if set.
     *                                  Default: []
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.8.0
     * @since   3.13.0    Completely revamped
     * @since   3.16.1  Merged param $data into $params.
     */

    public static function combo_box( array $params = [] ) {
        if ( !isset( $params['data'] ) ) {
            throw new Exception( "\$params['data'] has not been set. This parameter must be set for a combo box." );
        }
        $data_id = random_id_string( 7 );
        $item = "<datalist id='{$data_id}'>{$params['data']}</datalist>";
        unset( $params['data'] );
        $params['list'] = $data_id;
        $params['autocomplete'] = 'off';
        $echo_hold = self::$echo;
        self::$echo = false;
        $item .= self::text( $params );
        self::$echo = $echo_hold;
        if ( self::$echo ) {
            echo $item;
        } else {
            return $item;
        }
    }


    /**
     * A Filter text box, which is used for filtering a list or table.
     * 
     * @param   array   $params     Parameters to attach to the filter.
     *                              Default: []
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.6.0
     * @since   3.11.1  Removed params, $id, $placeholder, $value; added param $params
     * @since   3.13.0  Revamped and removed param $onKeyUp
     * @since   3.16.0  Revamped with new styling, labels, hints and validation. Renamed to search_filter from filter_box
     */

    public static function filter( array $params = [] ) {
        if ( !isset( $params['id'] ) ) {
            throw new \Exception( "Text field ID not set" );
        }
        $skip_fields = ['label', 'hint', 'validate', 'flex', 'container'];
        $item = '';

        if ( !isset ( $params['type'] ) ) {
            $params['type'] = 'search';
        }

        $container = isset( $params['container'] ) ? $params['container'] : [
            'overwrite' => false,
        ];
        if ( !isset( $container['overwrite'] ) ) {
            $container['overwrite'] = false;
        }

        if ( !isset ( $params['autocomplete'] ) ) {
            $params['autocomplete'] = 'off';
        }

        /**
         * Container
         */

        if ( !$container['overwrite'] ) {
            $item .= self::text_input_container( $params );
        }

        /**
         * Label
         */

        $item .= self::label( $params );

        $item .= "<span class='form_border search_filter_container'>";

        /**
         * Input Field
         */

        $standard_class = 'standard_text_input search_filter';

        if ( isset ( $params['class'] ) ) {
            $params['class'] = $standard_class . ' ' . $params['class'];
        } else {
            $params['class'] = $standard_class;
        }

        $item .= "<input";
        foreach ( $params as $field => $value ) {
            if ( in_array( $field, $skip_fields ) ) {
                continue;
            }
            switch ( $field ) {
                case 'autofocus':
                    $item .= $value ? ' autofocus' : '';
                    break;
                case 'disabled':
                    $item .= $value ? ' disabled' : '';
                    break;
                case 'readonly':
                    $item .= $value ? ' readonly' : '';
                    break;
                case 'required':
                    $item .= $value ? ' required' : '';
                    break;
                default:
                    $item .= " {$field}='{$value}'";
            }
        }
        $item .= ">";
        $item .= SVGImages::search->image();
        $item .= "</span>";

        /**
         * Hint
         */

        $item .= self::element_hint( $params );

        if ( !$container['overwrite'] ) {
            $item .= "</div>"; // Container
        }

        if ( self::$echo ) {
            echo $item;
        } else {
            return $item;
        }
    }


    /**
     * Draw out an <input type='hidden'> element
     * 
     * @param   array   $params     The fields to be added to the hidden input, either id or name must be set.
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.8.0
     * @since   3.13.0  Rewritten and replaced the params
     * @since   3.16.1  Merged param $value into $params.
     */

    public static function hidden_input( array $params = [] ) {
        if ( !isset( $params['id'] ) && !isset( $params['name'] ) ) {
            throw new \Exception( "Identifier not set. There must be either an 'id' or a 'name' parameter set" );
        }
        if ( !isset( $params['value'] ) ) {
            throw new \Exception( "Hidden value not set. \$params['value'] must be set." );
        }

        $item = "<input type='hidden'";

        foreach ( $params as $field => $value ) {
            $item .= " {$field}='{$value}'";
        }

        $item .= ">";

        if ( self::$echo ) {
            echo $item;
        } else {
            return $item;
        }
    }


    /**
     * Draw out a content drawer arrow, with functions to open or close the assigned draw
     * 
     * @param   string  $element_id     The id of the the span which contains the arrow
     * @param   string  $show_hide_id   The id of the element you wish to show or hide
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.9.1
     * @since   3.13.0    Renamed $id to $show_hide_id and added $element_id
     */

    public static function content_drawer_arrow( string $element_id, string $show_hide_id ) {
        $hold = HTML::$echo;
        HTML::$echo = false;
        $svg = new SVG( SVGImages::content_draw_arrow->image() );
        $svg->set_size( 16, 16 )->set_viewbox( 0, 0, 16, 16 );
        /**
         * @see src\js\lib\ui.js
         * -> show_hide() is performed inline
         */
        $element = HTML::span( [
            'class' => 'center_vertical',
            'id'    => $element_id,
        ] );
        $element .= "<input type='checkbox' class='drawer_checkbox'>";
        $element .= $svg->return();
        $element .= HTML::close_span();
        JS::script_module( "
        import { show_hide } from './src/js/lib/ui.js';
        document.getElementById('$element_id').onclick = function () {
            show_hide('$show_hide_id');
        };" );
        HTML::$echo = $hold;
        if ( self::$echo ) {
            echo $element;
        } else {
            return $element;
        }
    }


    /**
     * Draw out an input type=file element.
     * 
     * ## Options
     * - label    - set $params['label'] to put a text label above the uploader.
     * - hint     - set $params['hint'] to put a small text hint underneath the uploader.
     * - content  - set $params['content'] to set the text of the uploader button. Default is 'Choose File'.
     * - multiple - set $params['multiple'] to either true or false. Allows multiple uploads. Default is false.
     * - hidden   - set $params['hidden'] to either true or false. Hides the whole element. Default is false.
     * 
     * @param   array   $params     All the elements to include on the input element
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.12.7
     * @since   3.16.1  Completely revamped, to use non default decorations and new standards.
     */

    public static function upload_file( array $params = [] ) {
        $skip_fields = ['label', 'hint', 'content', 'hidden'];
        $item = self::label( $params );

        if ( !isset( $params['content'] ) ) {
            $params['content'] = 'Choose File';
        }

        if ( !isset( $params['id'] ) ) {
            $params['id'] = 'upl_' . random_id_string();
        }

        $container_class = 'standard_button__margin upload_button_container';
        if ( isset( $params['hidden'] ) && $params['hidden'] ) {
            $container_class .= ' hidden';
        }
        $item .= "<div class='{$container_class}' id='{$params['id']}__container'>";
        $item .= "<label class='upload_button_wrapper' id='{$params['id']}__wrapper'>";
        $item .= "<span class='upload_button_padding upload_button_text' id='{$params['id']}__button'>";
        $item .= SVGImages::upload->image();
        $item .= "<span class='upload_btn_inner_txt'>{$params['content']}</span>";
        $item .= "</span>";

        $standard_class = 'upload_default_selector_element';
        if ( isset( $params['class'] ) ) {
            $params['class'] = "{$standard_class} {$params['class']}";
        } else {
            $params['class'] = $standard_class;
        }
        $item .= "<input type='file'";
        foreach ( $params as $field => $value ) {
            if ( in_array( $field, $skip_fields ) ) {
                continue;
            }
            switch ( $field ) {
                case 'multiple':
                    $item .= $value ? ' multiple' : '';
                    break;
                case 'checked':
                    $item .= $value ? ' checked' : '';
                default:
                    $item .= " {$field}='{$value}'";
            }
        }
        $item .= '>';
        $item .= "<span class='upload_button_padding upload_file_selected_feedback' id='{$params['id']}__text_feedback'>No file selected</span>";
        $item .= "</label>";

        /**
         * Hint
         */

        $item .= self::element_hint( $params );
        $item .= "</div>";

        JS::script_module( "import UploaderElement from './src/js/lib/uploader_element.js';\nconst upload = new UploaderElement('{$params['id']}');" );

        if ( self::$echo ) {
            echo $item;
        } else {
            return $item;
        }
    }


    /**
     * Draw a submit button
     * 
     * @param   array   $params     All the elements to include on the input element
     * 
     * @return  string|void
     * 
     * @access  public
     * @since   3.12.8
     */

    public static function submit( array $params = [] ) {
        $element = "<input type='submit'";
        foreach ( $params as $index => $value ) {
            $element .= " {$index}='{$value}'";
        }
        $element .= '>';
        if ( self::$echo ) {
            echo $element;
        } else {
            return $element;
        }
    }


    /**
     * Draw an include / exclude multi column list. This includes working buttons to move values betweek the two columns.
     * 
     * @param   string          $id             The id of the element container and part id of the left and right tables
     *                                          To select the left column, use $id . '_include_list'
     *                                          To select the right column, use $id . '_exclude_list'
     * @param   array|string    $data_left      An array of data to be put in the left hand column.
     *                                          If an array, the index will be the value of each option
     *                                          If a string, this will be set directly. String should be passed from build_item_droplist()
     *                                          Default: []
     * @param   array|string    $data_right     An array of data to be put in the left hand column.
     *                                          If an array, the index will be the value of each option
     *                                          If a string, this will be set directly. String should be passed from build_item_droplist()
     *                                          Default: []
     * @param   string|null     $title1         A title to put on the head of the left column. Leave as null to leave blank.
     *                                          Default: null
     * @param   string|null     $title2         A title to put on the head of the right column. Leave as null to leave blank.
     *                                          Default: null
     * @param   boolean         $disabled       Whether or not to disable each column
     * 
     * @access  public
     * @since   3.14.3
     */

    public static function include_exclude_columns (
        string $id,
        array|string $data_left = [],
        array|string $data_right = [],
        ?string $title1 = null,
        ?string $title2 = null,
        bool $disabled = false,
    ): void {
        $hold = self::$echo;
        self::$echo = true;
        HTML::div( [
            'class' => 'multi_class_selector_container',
            'id'    => $id,
        ] );

        if ( is_array ( $data_left ) ) {
            $values_left = build_item_droplist( $data_left, array_keys( $data_left) );
        } else if ( is_string ( $data_left ) ) {
            $values_left = $data_left;
        } else {
            throw new \Exception( 'Invalid data type for $data_left' );
        }

        $is_disabled = $disabled ? " disabled='disabled'" : '';

        HTML::div( ['class' => 'multicolumn_column multicolumn_buttons_vertical_inline'] );
        if ( !is_null( $title1 ) ) {
            HTML::heading( 4, $title1 );
        }

        echo "<select id='{$id}_included_list' class='multi_class_selector' multiple{$is_disabled}>{$values_left}</select>";

        HTML::close_div();

        HTML::div( ['class' => 'center_horizontal multicolumn_buttons_vertical_inline'] );

        $right = new SVG( SVGImages::circle_arrow_right->image() );
        /**
         * @see src/js/app/admin/registrationClasses.js
         * -> move_reg_class_out()
         */
        HTML::span_container(
            [
                'id'    => $id . '_right_arrow',
                'class' => 'class_lr_arrow',
            ],
            $right->set_size( 36, 36 )->return(),
        );

        $left = new SVG( SVGImages::circle_arrow_left->image() );
        /**
         * @see src/js/app/admin/registrationClasses.js
         * -> move_reg_class_in()
         */
        HTML::span_container(
            [
                'id'    => $id . '_left_arrow',
                'class' => 'class_lr_arrow',
            ],
            $left->set_size( 36, 36 )->return(),
        );

        HTML::close_div();

        if ( is_array ( $data_right ) ) {
            $values_right = build_item_droplist( $data_right, array_keys( $data_right ) );
        } else if ( is_string ( $data_right ) ) {
            $values_right = $data_right;
        } else {
            throw new \Exception( 'Invalid data type for $data_right' );
        }

        HTML::div( ['class' => 'multicolumn_column multicolumn_buttons_vertical_inline'] );
        if ( !is_null( $title2 ) ) {
            HTML::heading( 4, $title2 );
        }

        echo "<select id='{$id}_excluded_list' class='multi_class_selector' multiple{$is_disabled}>{$values_right}</select>";

        HTML::close_div();

        Scripts::script_module( "
        import {handle_column_changes} from './src/js/lib/forms.js';
        handle_column_changes('{$id}');
        " );
 
        HTML::close_div(); // multi_class_selector_container $id
        self::$echo = $hold;
    }


    /**
     * A simple form with a submit button to load a page based on the button click as 
     * a means of navigation.
     * 
     * @param   string  $button_text    The text of the button.
     * @param   string  $page           The ?p= page to load.
     * @param   string  $tab            The &t= tab to load.
     *                                  Default: null
     * 
     * @return  string|void
     * 
     * @access public
     * @since   3.15.4
     */

    public static function submit_load_page( string $button_text, string $page, ?string $tab = null ) {
        $button = "<form method='get'>";
        $hold = self::$echo;
        $button .= self::submit( ['value' => $button_text, 'class' => 'std_button'] );
        self::$echo = $hold;
        $button .= page( $page, false, true );
        if ( !is_null ( $tab ) ) {
            $button .= tab( $tab, true );
        }
        $button .= "</form>";

        if ( self::$echo ) {
            echo $button;
        } else {
            return $button;
        }   
    }

}