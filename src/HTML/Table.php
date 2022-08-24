<?php

namespace LBS\HTML;

use \Exception;
use LBS\HTML\JS;
use LBS\HTML\Form;
use LBS\HTML\HTML;

/**
 * New class for generating tables within the app.
 * 
 * use LBS\HTML\Table;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.15.5
 */

class Table {

    /**
     * The id of the table.
     * 
     * @var string  $table_id
     * 
     * @access  public
     * @since   3.15.5
     */

    public string $table_id;

    /**
     * The index of the row.
     * 
     * @var integer $row_index
     * 
     * @access  private
     * @since   3.15.5
     */

    private int $row_index = 0;

    /**
     * The index of the cell within a row.
     * 
     * @var integer $cell_index
     * 
     * @access  private
     * @since   3.15.5
     */

    private int $cell_index;

    /**
     * Whether to draw an outer border lines.
     * 
     * @var boolean $border Default: true
     * 
     * @access  public
     * @since   3.15.5
     */

    public bool $border = true;

    /**
     * Draw horizontal table lines.
     * 
     * @var boolean $horizontal_lines   Default: false
     * 
     * @access  public
     * @since   3.15.5
     */

    public bool $horizontal_lines = false;

    /**
     * Draw vertical table lines.
     * 
     * @var boolean $vertical_lines     Default: false
     * 
     * @access  public
     * @since   3.15.5
     */

    public bool $vertical_lines = false;

    /**
     * Include selection checkbox for each row.
     * 
     * @var boolean $select_checkbox    Default: true
     * 
     * @access  public
     * @since   3.15.5
     */

    public bool $select_checkbox;

    /**
     * Include a select all checkbox at the top of the table.
     * 
     * @var boolean $select_all_checkbox    Default: true
     * 
     * @access  public
     * @since   3.15.5
     */

    public bool $select_all_checkbox;

    /**
     * Include space on the table for an edit link.
     * 
     * @var boolean $edit_link  Default: true
     * 
     * @access  public
     * @since   3.15.5
     */

    public bool $edit_link;
    
    /**
     * Draw the table as a full width table
     * 
     * @var boolean $full_width     Default: true
     * 
     * @access  public
     * @since   3.15.5
     */

    public bool $full_width = true;

    /**
     * The defined alignment of the header of the table.
     * 
     * ### Options
     * 
     * - 'L'
     * - 'C'
     * - 'R'
     * 
     * @var string  $heading_alignment  Default: 'C'
     */

    public string $heading_alignment = 'C';

    /**
     * Keys that should be skipped when iterating over attributes
     * 
     * @var array   SKIP_KEYS
     * 
     * @access  public
     * @since   3.15.5
     */

    const SKIP_KEYS = [
        'heading', 'headings', 'alignment', 'disable_check', 'highlight'
    ];


    /**
     * Constructor method, things to do when the class is loaded.
     * 
     * @param   string  $id                     The id of the table.
     * @param   boolean $select_checkbox        Whether or not to include the line by line select checkbox.
     * @param   boolean $select_all_checkbox    Whether or not to include the select all checkbox.
     * @param   boolean $edit_link              Whether or not to include the edit link.
     * 
     * @access  public
     * @since   3.15.5
     */

    public function __construct( string $id, bool $select_checkbox = true, bool $select_all_checkbox = true, bool $edit_link = true ) {
        $this->table_id            = $id;
        $this->select_checkbox     = $select_checkbox;
        $this->select_all_checkbox = $select_all_checkbox;
        $this->edit_link           = $edit_link;
    }


    /**
     * Start a new table with the opening <table> tag.
     * 
     * @param   array   $headings   The headings of the table
     *                              Default: []
     * @param   array   $params     Any parameters to add to the <table> tag.
     *                              Default: []
     * 
     * @return  self  $this
     * 
     * @access  public
     * @since   3.15.5
     */

    public function table_start( array $headings = [], array $params = [] ): self {
        if ( isset( $params['id'] ) ) {
            throw new Exception( "You cannot set an id for a Table" );
        }
        $params['id'] = $this->table_id . '_table';

        $class = 'standard_table';
        if ( $this->full_width ) {
            $class .= ' table_width--full';
        }
        if ( isset( $params['class'] ) ) {
            $params['class'] = "{$class} {$params['class']}";
        } else {
            $params['class'] = $class;
        }

        $border = $this->border ? ' table_border' : '';

        echo "<div class='table_container{$border}'>";
        $table = "<table";
        foreach ( $params as $key => $value ) {
            $table .= " {$key}='{$value}'";
        }
        $table .= ">";
        echo $table;
        if ( count( $headings ) > 0 ) {
            echo "<thead>";
            $this->headings( $headings, ['alignment' => $this->heading_alignment] );
            echo "</thead>";
        }
        echo "<tbody id='{$this->table_id}_unfiltered_body'>";
        return $this;
    }


    /**
     * The closing </table> tag.
     * 
     * @access  public
     * @since   3.15.5
     */

    public function table_end(): void {
        echo "</tbody>";
        echo "<tfoot id='{$this->table_id}_filtered_body'></tfoot>";
        echo "</table>";
        echo "</div>";
        JS::insert_shift_multiselect();
        if ( $this->select_all_checkbox ) {
            JS::script_module( "
                import { select_all_checkboxes } from './src/js/lib/table_filters.js';
                document.getElementById('{$this->table_id}_select_all').onchange = function () {
                    select_all_checkboxes(this, `{$this->table_id}_unfiltered_body`);
                };
                document.getElementById('{$this->table_id}_select_all_filtered').onchange = function () {
                    select_all_checkboxes(this, `{$this->table_id}_filtered_body`);
                };"
            );
        }
    }


    /**
     * Start a new table row <tr>.
     * 
     * @param   array   $params     Any parameters to add to the <table> tag.
     *                              Default: []
     * 
     * @return  self  $this
     * 
     * @access  public
     * @since   3.15.5
     */

    public function row_start( array $params = [] ): self {
        if ( isset( $params['id'] ) ) {
            throw new Exception( "You cannot set an id for a Table Row" );
        }
        
        $class = 'standard_row';
        
        if ( !isset( $params['heading'] ) || $params['heading'] == false ) {
            $params['id'] = $this->table_id . '--row_' . $this->row_index;
            $class .= ' body_row';
        }

        if ( $this->horizontal_lines ) {
            $class .= ' table_horizontal_border';
        }

        if ( isset( $params['highlight'] ) && $params['highlight'] == true ) {
            $class .= ' highlight_empty';
        }
        
        if ( isset( $params['class'] ) ) {
            $params['class'] = "{$class} {$params['class']}";
        } else {
            $params['class'] = $class;
        }

        $row = "<tr";
        foreach ( $params as $key => $value ) {
            if ( in_array ( $key, self::SKIP_KEYS ) ) {
                continue;
            }
            $row .= " {$key}='{$value}'";
        }
        $row .= ">";
        echo $row;
        $this->cell_index = 0;
        if ( $this->select_checkbox && !isset( $params['heading'] ) ) {
            $disabled = isset( $params['disable_check'] ) ? $params['disable_check'] : false;
            $this->select_checkbox( false, $disabled );
        }
        return $this;
    }


    /**
     * The closing </tr> tag.
     * 
     * @param   boolean $heading_row    Whether or not the row is a heading row
     *                                  Default: false
     * 
     * @access  public
     * @since   3.15.5
     */

    public function row_end( bool $heading_row = false ): void {
        if ( !$heading_row ) {
            $this->row_index++;
        }
        echo "</tr>";
    }


    /**
     * Headings of the table.
     * 
     * @param   array   $heading    The headings to place on top of the table
     *                              Default: []
     * @param   array   $params     Params to add to the cell. 
     *                              Can contain standard attributes like class or name as well as the following:
     *                              - 'alignment' ['L', 'C', 'R']
     *                              Default: []
     * 
     * @access  private
     * @since   3.15.5
     */

    private function headings( array $headings = [], array $params = [] ): void {
        $this->row_start( [
            'class'   => 'table_header', 
            'heading' => true
        ] );
        if ( $this->select_checkbox ) {
            if ( $this->select_all_checkbox ) {
                $this->select_checkbox( true );
            } else {
                echo "<th></th>";
            }
        }
        if ( $this->edit_link ) {
            echo "<th></th>";
        }

        $params = $this->set_text_alignment( $params );

        if ( $this->vertical_lines ) {
            if ( isset( $params['class'] ) ) {
                $params['class'] .= ' table_vertical_border';
            } else {
                $params['class'] = 'table_vertical_border';
            }
        }
        foreach ( $headings as $heading ) {
            $cell = "<th";
            foreach ( $params as $key => $value ) {
                if ( in_array ( $key, self::SKIP_KEYS ) ) {
                    continue;
                }
                $cell .= " {$key}='{$value}'";
            }
            $cell .= ">{$heading}</th>";
            echo $cell;
        }
        $this->row_end( true );
    }


    /**
     * A table cell <td></td>.
     * 
     * @param   mixed   $content    The content of the cell.
     * @param   array   $params     Params to add to the cell. 
     *                              Can contain standard attributes like class or name as well as the following:
     *                              - 'alignment' ['L', 'C', 'R']
     *                              Default: []
     * 
     * @return  self  $this
     * 
     * @access  public
     * @since   3.15.5
     */

    public function cell( mixed $content, array $params = [] ): self {
        if ( isset( $params['id'] ) ) {
            throw new Exception( "You cannot set an id for a Table Cell" );
        }
        if ( !isset ( $params['heading'] ) || $params['heading'] == false ) {
            $params['id'] = $this->get_cell_id();
        }
        $params = $this->set_vertical_lines( $params );
        $params = $this->set_text_alignment( $params );

        $tdh = isset ( $params['heading'] ) && $params['heading'] == true ? 'th' : 'td';

        $cell = "<{$tdh}";
        
        foreach ( $params as $key => $value ) {
            if ( in_array ( $key, self::SKIP_KEYS ) ) {
                continue;
            }
            $cell .= " {$key}='{$value}'";
        }
        $cell .= ">{$content}</{$tdh}>";
        echo $cell;
        if ( !isset ( $params['heading'] ) || $params['heading'] == false ) {
            $this->cell_index++;
        }
        return $this;
    }


    /**
     * Insert a cell edit link.
     * 
     * @param   string  $href   The link to navigate to. Something like '?p=example&x=y'
     * @param   array   $params     Params to add to the cell. 
     *                              Can contain standard attributes like class or name as well as the following:
     *                              - 'alignment' ['L', 'C', 'R']
     *                              Default: []
     * 
     * @return  self  $this
     * 
     * @access  public
     * @since   3.15.5
     */

    public function cell_edit_link( string $href, array $params = [] ): self {
        if ( isset( $params['id'] ) ) {
            throw new Exception( "You cannot set an id for a Table Cell" );
        }
        $params['id'] = $this->get_cell_id();
        $params = $this->set_vertical_lines( $params );
        $params = $this->set_text_alignment( $params );

        if ( isset( $params['class'] ) ) {
            $params['class'] .= ' edit_link_width';
        }

        $hold = HTML::$echo;
        HTML::$echo = false;
        $link = HTML::link( $href, 'Edit', ['new_tab' => true ] );
        HTML::$echo = $hold;
        $cell = "<td";
        foreach ( $params as $key => $value ) {
            $cell .= " {$key}='{$value}'";
        }
        $cell .= ">{$link}</td>";
        echo $cell;
        $this->cell_index++;
        return $this;
    }


    /**
     * Insert a hidden value or data into a row.
     * 
     * @param   string  $value      The value of the hidden input
     * @param   array   $params     Any parameters to add to the hidden input
     * 
     * @return  self  $this
     * 
     * @access  public
     * @since   3.15.5
     */

    public function hidden_data( mixed $value, array $params = [] ): self {
        if ( isset( $params['id'] ) ) {
            throw new Exception( "You cannot set an id for a Table Cell" );
        }
        $params['id'] = $this->get_cell_id();
        
        $input = "<input type='hidden' value='{$value}'";
        foreach ( $params as $key => $value ) {
            $input .= " {$key}='{$value}'";
        }
        $input .= '>';
        echo $input;
        $this->cell_index++;
        return $this;
    }


    /**
     * The row selection checkbox.
     * 
     * @param   boolean     $select_all_checkbox    If the box is in the header for selecting all
     *                                              Default: false
     * @param   boolean     $disabled               Whether or not to disable the checkbox
     *                                              Default: false
     * 
     * @access  private
     * @since   3.15.5
     */

    private function select_checkbox( bool $select_all_checkbox = false, bool $disabled = false  ): void {
        $hold = Form::$echo;
        Form::$echo = false;
        if ( $select_all_checkbox ) {
            $checkboxs = Form::checkbox( [
                'id'        => "{$this->table_id}_select_all",
                'class'     => 'selectable_checkbox',
                'disabled'  => $disabled,
                'container' => ['overwrite' => true],
            ] );
            $checkboxs .= Form::checkbox( [
                'id'        => "{$this->table_id}_select_all_filtered",
                'class'     => 'selectable_checkbox hidden',
                'disabled'  => $disabled,
                'container' => ['overwrite' => true],
            ] );
            $this->cell( $checkboxs, [
                'class'   => 'selectable_checkbox_width',
                'heading' => true,
            ] );
        } else {
            $this->cell( Form::checkbox( [
                'id' => "{$this->table_id}_checkbox{$this->row_index}",
                'class' => 'selectable_checkbox',
                'disabled' => $disabled,
                'container' => ['overwrite' => true],
            ] ), ['class' => 'selectable_checkbox_width'] );
        }
        Form::$echo = $hold;
    }


    /**
     * Returns the cell ID for the specific cell.
     * 
     * @return  string
     * 
     * @access  private
     * @since   3.15.5
     */

    private function get_cell_id(): string {
        return "{$this->table_id}--row_{$this->row_index}--cell_{$this->cell_index}";
    }


    /**
     * Sets vertical lines as desired.
     * 
     * @param   array   $params
     * 
     * @return  array   $params
     * 
     * @access  private
     * @since   3.15.5
     */

    private function set_vertical_lines( array $params ): array {
        if ( $this->vertical_lines ) {
            if ( isset( $params['class'] ) ) {
                $params['class'] .= ' table_vertical_border';
            } else {
                $params['class'] = 'table_vertical_border';
            }
        }
        return $params;
    }


    /**
     * Check for text alignment and retun class string as required.
     * 
     * @param   array  $params  The Params of the cell
     * 
     * @return  array  $params
     * 
     * @access  private
     * @since   3.15.5
     */

    private function set_text_alignment( array $params ): array {
        if ( !isset( $params['alignment'] ) ) {
            return $params;
        }
        switch ( $params['alignment'] ) {
            case 'L':
                $align = 'text_align_left';
                break;
            case 'C':
                $align = 'text_align_center';
                break;
            case 'R':
                $align = 'text_align_right';
                break;
            default:
                return $params;
        }

        if ( isset( $params['class'] ) ) {
            $params['class'] .= " {$align}";
        } else {
            $params['class'] = $align;
        }

        return $params;
    }


    /**
     * Return the row ID at the requested moment.
     * 
     * @return int
     * 
     * @access  public
     * @since   3.17.0
     */

    public function get_row_index(): int {
        return $this->row_index;
    }


    /**
     * Return the cell ID at the requested moment.
     * 
     * @return int
     * 
     * @access  public
     * @since   3.17.1
     */

    public function get_cell_index(): int {
        return $this->cell_index;
    }

}


/**
 * @todo
 * 
 * As a future idea:
 * 
 * Consolidate all the search methods into this class. Have them as as callable methods with 
 * automatic JS included, pointing back to a single script in the js
 * class table_filter class.
 * 
 * Basically the automation of table filters, as a drop in solution. Should save much effort.
 * 
 * @since   3.15.5
 */