<?php

namespace LBF\Tools\Spreadsheet;

use Exception;
use LBF\Tools\Spreadsheet\SpreadsheetEngine;

/**
 * This class handles any file upload requests
 * 
 * use LBF\Tools\Spreadsheet\SpreadsheetTool;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.19.0
 */

class SpreadsheetTool extends SpreadsheetEngine {

    /**
     * Whether or not to include a custom header on the spreadsheet.
     * 
     * @var boolean $include_header Default: false
     * 
     * @access  public
     * @since   3.19.0
     */

    public bool $include_header = false;

    /**
     * Number of rows in the custom header row. Default: 1
     * 
     * @var integer $header_rows    Default: 1
     * 
     * @access  public
     * @since   3.19.0
     */

    public int $header_rows = 1;

    /**
     * The html with the spreadsheet header elements.
     * 
     * @var string  $header
     * 
     * @access  public
     * @since   3.19.0
     */
    
    public string $header;


    /**
     * Class constructor, set the table id.
     * 
     * @param   string          $table_id       The id of the spreadsheet being constructed.
     * @param   null|integer    $max_columns    The maximum columns to be drawn. Leave null to set the max dynamically.
     * @param   null|integer    $max_rows       The maximum rows to be drawn. Leave null to set the max dynamically.
     * 
     * 
     * @access  public
     * @since   3.19.0
     */

    public function __construct( 
        /**
         * @access  protected
         * @since   3.19.0
         */
        protected string $table_id,
        /**
         * @access  protected
         * @since   3.19.0
         */
        protected ?int $max_columns = null,
        /**
         * @access  protected
         * @since   3.19.0
         */
        protected ?int $max_rows = null,
    ) {
        $this->row = 1;
        $this->column = 1;
        $this->default_column_width = self::COLUMN_DEFAULT_WIDTH;
    }


    /**
     * Generate $this->header_data to be placed on top of the spreadsheet.
     * 
     * @param   array   $data   The header data.
     * 
     * @access  public
     * @since   3.19.0
     */

    public function construct_header( array $data ): void {
        $this->include_header = true;
        $this->header_data = '';
        foreach ( $data as $entry ) {
            $e_count = count( $entry );
            if ( $e_count > $this->header_rows ) {
                $this->header_rows = $e_count;
            }
        }

        for ( $this->column = 1; $this->column <= $this->max_columns; $this->column++ ) {
            $this->p = 1;
            if ( !isset( $data[$this->column] ) ) {
                $this->header_data .= "<div id='{$this->table_id}__header__row_{$this->row}__column_{$this->column}'
                                            class='{$this->table_id}__spreadsheet_header_cell'
                                            data-row='{$this->row}'
                                            data-column='{$this->column}'
                                            'data-selected='0'
                                            style='{$this->set_header_grid_areas()}'
                                            name='{$this->table_id}__spreadsheet_header_cell'
                                       ></div>";
                continue;
            }
            $cell = $data[$this->column];
            foreach ( $cell as $this->row => $value ) {
                $this->header_data .= "<div id='{$this->table_id}__header__row_{$this->row}__column_{$this->column}'
                                            class='{$this->table_id}__spreadsheet_header_cell'
                                            data-row='{$this->row}'
                                            data-column='{$this->column}'
                                            'data-selected='0'
                                            style='{$this->set_header_grid_areas( $cell )}'
                                            name='{$this->table_id}__spreadsheet_header_cell'
                                       ><b>{$value}</b></div>";
            }
        }
    }


    /**
     * Contruct a basic blank sheet.
     * 
     * @access  public
     * @since   3.19.0
     */

    public function construct_blank_sheet(): void {
        $this->start_spreadsheet();
        for ( $this->row = 1; $this->row <= $this->max_rows; $this->row++ ) {
            for ( $this->column = 1; $this->column <= $this->max_columns; $this->column++ ) {
                echo "<input type='text'
                             class='{$this->table_id}__spreadsheet_cell'
                             data-row='{$this->row}'
                             data-column='{$this->column}'
                             data-selected='0'
                             id='{$this->table_id}__row_{$this->row}__column_{$this->column}'
                             style='grid-row: {$this->row}; grid-column: {$this->column}'
                             name='{$this->table_id}__spreadsheet_cell'
                             autocomplete='off'
                      >";
            }
        }
        $this->end_spreadsheet();
    }


    /**
     * Start off a spreadsheet.
     * 
     * @access  public
     * @since   3.19.0
     */

    public function start_spreadsheet(): void {
        $this->max_columns = is_null( $this->max_columns ) ? self::MAX_DEFAULT_COLUMNS : $this->max_columns;
        $this->max_rows    = is_null( $this->max_rows )    ? self::MAX_DEFAULT_ROWS    : $this->max_rows;
        echo "<style>{$this->spreadsheet_styles()}</style>";
        // echo "<canvas id='xyzr' class='{$this->table_id}__spreadsheet_canvas'></canvas>";
        echo "<div id='{$this->table_id}__canvas' class='{$this->table_id}__spreadsheet_container' id='{$this->table_id}' name='spreadsheet'>";
        if ( $this->include_header ) {
            echo $this->header_data;
        }
        $this->row = 1;
        if ( $this->include_header ) {
            $this->row += $this->header_rows;
        }
    }


    /**
     * Close off a spreadsheet.
     * 
     * @access  public
     * @since   3.19.0
     */

    public function end_spreadsheet(): void {
        echo "</div>"; // {$this->table_id}
        echo "<div class='{$this->table_id}__selection_window_container'>";
        echo "<div class='{$this->table_id}__selection_window'></div>";
        echo "</div>";
        echo "<script type='module'>
import SpreadsheetTool from '{$this->js_path}';
const spreadsheet = new SpreadsheetTool('{$this->table_id}');
</script>";
    }


    /**
     * Contruct a single row of cells.
     * 
     * @param   array   $data   The data for the row of cells.
     * 
     * @access  public
     * @since   3.19.0
     */

    public function construct_row( array $data = [] ): void {
        $this->column = 1;
        foreach ( $data as $value ) {
            switch ( $value['type'] ?? 'text' ) {
                case 'checkbox':
                    
                    break;
                case 'select':
                    echo $this->select_cell( $value );
                    break;
                default:
                    echo $this->text_cell( $data, $value );
            }
            $this->column++;
        }
        $this->row++;
    }


    /**
     * Set the value $this->max_rows.
     * 
     * @param   integer $rows   The number of rows that should be set.
     * 
     * @access  public
     * @since   3.19.0
     */

    public function set_max_rows( int $rows): void {
        $this->max_rows = $rows;
    }


    /**
     * Set the value $this->max_columns.
     * 
     * @param   integer $columns    The number of columns that should be set.
     * 
     * @access  public
     * @since   3.19.0
     */

    public function set_max_columns( int $columns ): void {
        $this->max_columns = $columns;
    }


    /**
     * Determine the maximum number of columns from the data being inserted.
     * 
     * @param   array   $rows   All the data being inserted.
     * 
     * @note    If relative values are used, this class will not be able to determine
     *          what the total width of the sheet is. This may result in some undesired
     *          visual results.
     * 
     * @todo    Account for blanks, not quite working
     * 
     * @access  public
     * @since   3.19.0
     */

    public function determine_max_columns ( array $rows ): void {
        if ( is_null( $this->max_columns ) ) {
            $this->max_columns = 1;
        }
        foreach ( $rows as $columns ) {
            if ( count( $columns ) > $this->max_columns ) {
                $this->set_max_columns( count( $columns ) );
            }
        }
    }


    /**
     * Set the default column width.
     * Should be parsed as either an integer, which will be combined with $this->default_unit;
     * Or as an string to set a custom width.
     * 
     * ## Examples
     * - 200
     * - '250px'
     * - '75vw'
     * - '50%'
     * 
     * @param   string|integer  $width  The desired width.
     * 
     * @access  public
     * @since   3.19.0
     */

    public function set_default_width( string|int $width ): void {
        if ( is_numeric( $width ) ) {
            $width .= $this->default_unit;
        }
        $this->default_column_width = $width;
    }


    /**
     * Set the column width for a specific column or columns.
     * 
     * @note    While all kinds of values may be set (px, %, em etc.). It is recommended to
     *          Not use relative values as the class will not be able to determine the total
     *          width of the spreadsheet, resulting in unexpected visual results.
     * 
     * @param   array   $entries    The data to be set. [int:col_num => int|str:width]
     * 
     * @access  public
     * @since   3.19.0
     */

    public function set_column_width_overwrite( array $entries ): void {
        foreach ( $entries as $index => $value ) {
            if ( is_numeric( $value ) ) {
                $value .= $this->default_unit;
            }
            $this->column_width_overwrite[$index] = $value;
        }
    }


    /**
     * Set property $this->default_unit, to set the default measuring unit if none is set.
     * 
     * @param   string  $unit   The default unit.
     * 
     * @access  public
     * @since   3.19.0
     */

    public function set_default_measurement_unit( string $unit ): void {
        $unit_types = ['cm', 'mm', 'in', 'px', 'pt', 'pc', 'em', 'ex', 'ch', 'rem', 'vw', 'vh', 'vmin', 'vmax', '%'];
        if ( !in_array( $unit, $unit_types ) ) {
            throw new Exception( "Invalid unit {$unit}. You must choose one of the following: " . implode( '; ', $unit_types ) );
        }
        $this->default_unit = $unit;
    }


    /**
     * Return the id of the table
     * 
     * @return  string
     * 
     * @access  public
     * @since   3.19.0
     */

    public function get_id(): string {
        return $this->table_id;
    }


    /**
     * Return the current column.
     * 
     * @return  int
     * 
     * @access  public
     * @since   3.19.0
     */

    public function get_current_column(): int {
        return $this->column;
    }


    /**
     * Return the current row.
     * 
     * @return  int
     * 
     * @access  public
     * @since   3.19.0
     */

    public function get_current_row(): int {
        return $this->row;
    }

}