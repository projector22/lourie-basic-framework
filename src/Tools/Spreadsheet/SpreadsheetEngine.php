<?php

namespace LBF\Tools\Spreadsheet;

use Exception;

/**
 * This class handles the backend of spreadsheet tools.
 * 
 * use LBF\Tools\Spreadsheet\SpreadsheetEngine;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.22.0
 */

class SpreadsheetEngine {

    /**
     * The default width of each column if no user width is defined.
     * 
     * @var string  COLUMN_DEFAULT_WIDTH    Default: 150px
     * 
     * @access  public
     * @since   3.19.0
     */

    const COLUMN_DEFAULT_WIDTH = '150px';

    /**
     * The default maximum number of columns.
     * 
     * @var integer MAX_DEFAULT_COLUMNS Default: 50
     * 
     * @access  public
     * @since   3.19.0
     */

    const MAX_DEFAULT_COLUMNS = 50;

    /**
     * The default maximum number of columns.
     * 
     * @var integer MAX_DEFAULT_ROWS    Default: 200
     * 
     * @access  public
     * @since   3.19.0
     */

    const MAX_DEFAULT_ROWS = 200;


    /**
     * The path to the required JS path. Adjust as needed.
     * 
     * @var string  $js_path
     * 
     * @access  protected
     * @since   3.19.0
     */

    protected string $js_path = './src/js/lib/spreadsheetTool.js';

    /**
     * A column counter to indicate where we are in the spreadsheet
     * 
     * @var int $column
     * 
     * @access  protected
     * @since   3.19.0
     */

    protected int $column;

    /**
     * A row counter to indicate where we are in the spreadsheet
     * 
     * @var int $row
     * 
     * @access  protected
     * @since   3.19.0
     */

    protected int $row;

    /**
     * The default unit to use if no usit is specified.
     * By default, use 'px'. This can be changed by calling the `set_default_measurement_unit()` method.
     * 
     * @see https://www.w3schools.com/cssref/css_units.asp
     * 
     * @var string  $default_unit.  Default: 'px'
     * 
     * @access  protected
     * @since   3.19.0
     */

    protected string $default_unit = 'px';

    /**
     * The default width of each column.
     * Set by calling the `set_default_width()` method.
     * 
     * @var string  $default_column_width
     * 
     * @access  protected
     * @since   3.19.0
     */

    protected string $default_column_width;

    /**
     * If you wish to specify the width of a specific column. Set them here in this array.
     * Set by calling the `set_column_width_overwrite()` method.
     * 
     * @var array   $column_width_overwrite
     * 
     * @access  protected
     * @since   3.19.0
     */

    protected array $column_width_overwrite = [];


    /**
     * Draw out the <style> tags required for the drawing of a spreadsheet
     * 
     * @return  string
     * 
     * @access  protected
     * @since   3.19.0
     * @since   3.23.2  Moved styles into ./styles/spreadsheet.css
     */

    protected function spreadsheet_styles(): string {
        $style = file_get_contents( realpath( __DIR__ . '/styles/spreadsheet.css' ) );
        $style = str_replace( 'TABLE_ID', $this->table_id, $style );
        $style = str_replace( 'DEFAULT_COL_WIDTH', $this->default_column_width, $style );
        $style = str_replace( "/* CALC_WIDTH; */", $this->calculate_widths(), $style );
        return $style;
    }


    /**
     * Calculate and return the css required for calculating widths and grid-template-columns on the sheet.
     * 
     * @access  protected
     * @since   3.19.0
     */

    protected function calculate_widths(): string {
        $style = '';
        if ( count ( $this->column_width_overwrite ) > 0 ) {
            $rows = [];
            $st_rows = 0;
            $extra = 0;
            foreach ( range(1, $this->max_columns ) as $i ) {
                if ( isset( $this->column_width_overwrite[$i] ) ) {
                    $rows[] = $this->column_width_overwrite[$i];
                    if ( substr( $this->column_width_overwrite[$i], -2 ) == 'px' ) {
                        $extra += (int)str_replace( 'px', '', $this->column_width_overwrite[$i] );
                    } else {
                        $st_rows++;
                    }
                } else {
                    $rows[] = $this->default_column_width;
                    $st_rows++;
                }
            }
            $style .= "grid-template-columns: " . implode( ' ', $rows ) . ";\n";
            $style .= "        max-width: calc((var(--{$this->table_id}-cell_width) * {$st_rows}) + {$extra}px + {$this->max_columns}px);";
        } else {
            $style .= "grid-template-columns: repeat({$this->max_columns}, var(--{$this->table_id}-cell_width));\n";
            $style .= "        max-width: calc((var(--{$this->table_id}-cell_width) * {$this->max_columns}) + {$this->max_columns}px + 17px);";
        }
        return $style;
    }


    /**
     * Set the style grid areas for the header.
     * 
     * @param   array $cell  The cells required to be placed in the the column.
     * 
     * @return  string
     * 
     * @access  protected
     * @since   3.19.0
     */

    protected function set_header_grid_areas( array $cell = [] ): string {
        if ( count( $cell ) > 0 && $this->p == 1 ) {
            $this->end = ( $this->header_rows + 1 ) / count( $cell );
        }
        if ( count( $cell ) == 1 ) {
            $tot = $this->header_rows + 1;
            $grids = " grid-row-start: 1; grid-row-end: {$tot};";
        } else if ( count( $cell ) < $this->header_rows ) {
            $grids = " grid-row-start: {$this->p}; grid-row-end: {$this->end};";
            $this->p = $this->end;
            $this->end += $this->end;
        } else {
            $grids = "grid-row: {$this->row}; grid-column: {$this->column};";
        }
        return $grids;
    }


    /**
     * Format the set params correctly to fit in an input field.
     * 
     * @param   array   $params     Entries to squash.
     * 
     * @return  string
     * 
     * @access  private
     * @since   3.22.0
     */

    private function format_params( array $params ): string {
        $squashed = [];
        foreach ( $params as $key => $value ) {
            switch ( $key ) {
                case 'disabled':
                    $squashed[] = $key;
                    break;
                case 'readonly':
                    $squashed[] = $key;
                    break;
                case 'disabled':
                    $squashed[] = $key;
                    break;
                default:
                    $squashed[] = "{$key}='{$value}'";
            }                
        }
        return implode( ' ', $squashed );
    }


    /**
     * Set an ordinary text cell.
     * 
     * @param   array   $data   The general data being parsed
     * @param   mixed   $value  The specific data for the selected column being parsed.
     * 
     * @return  string
     * 
     * @access  protected
     * @since   3.22.0
     */

    protected function text_cell( array $data, mixed $value ): string {
        $params = [
            'class'         => "{$this->table_id}__spreadsheet_cell",
            'data-row'      => $this->row,
            'data-column'   => $this->column,
            'data-selected' => 0,
            'id'            => "{$this->table_id}__row_{$this->row}__column_{$this->column}",
            'style'         => "grid-row: {$this->row}; grid-column: {$this->column}",
            'name'          => "{$this->table_id}__spreadsheet_cell",
            'autocomplete'  => 'off'

        ];
        if ( is_string( $value ) || is_int( $value ) ) {
            if ( isset( $data[$this->column] ) ) {
                $params['value'] = $value;
            }
        } else {
            if ( isset( $data[$this->column] ) ) {
                foreach ( $value as $field => $property ) {
                    if ( $field == 'type' ) {
                        continue;
                    }
                    if ( $field == 'class' || $field == 'style' ) {
                        $params[$field] .= " {$property}";
                    } else {
                        $params[$field] = $property;
                    }
                }
            }
        }
        return "<input type='text' {$this->format_params( $params )}>";
    }


    /**
     * Set a select box cell.
     * 
     * @param   mixed   $entry  Cell specific params.
     * 
     * @return string
     * 
     * @access  protected
     * @since   3.22.0
     */

    protected function select_cell( mixed $entry ): string {
        if ( !isset( $entry['options'] ) ) {
            throw new Exception( "No options set for spreadsheet select cells." );
        }
        $params = [
            'class'         => "{$this->table_id}__spreadsheet_cell",
            'data-row'      => $this->row,
            'data-column'   => $this->column,
            'data-selected' => 0,
            'id'            => "{$this->table_id}__row_{$this->row}__column_{$this->column}",
            'style'         => "grid-row: {$this->row}; grid-column: {$this->column}",
            'name'          => "{$this->table_id}__spreadsheet_cell",
            'autocomplete'  => 'off'
        ];
        $skip_fields = ['type', 'options'];
        foreach ( $entry as $field => $property ) {
            if ( in_array( $field, $skip_fields ) ) {
                continue;
            }
            if ( $field == 'class' || $field == 'style' ) {
                $params[$field] .= " {$property}";
            } else {
                $params[$field] = $property;
            }
        }
        return "<select {$this->format_params( $params )}>{$entry['options']}</select>";
    }

}