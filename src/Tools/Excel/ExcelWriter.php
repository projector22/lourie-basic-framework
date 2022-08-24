<?php

namespace Framework\Tools\Excel;

use Exception;

/**
 * Basic methods for creating an excel file.
 * 
 * use Framework\Tools\Excel\ExcelWriter;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.19.6
 */

class ExcelWriter extends ExcelWriterBackend {

    /**
     * Constructor method, overwrites the constructor in the backend
     * 
     * @access  public
     * @since   3.19.6
     */

    public function __construct() {
        // Does nothing other than block the backend constructor.
    }


    /**
     * Add a new sheet to the new spreadsheet.
     * 
     * @param   string  $name
     * 
     * @access  public
     * @since   3.19.6
     */

    public function add_new_sheet( string $name ): void {
        if ( strlen( $name ) > 31 ) {
            /**
             * The max length of a sheet name is 31 chars. This 
             * protects the sheet if the name is longer than 
             * 31 chars.
             */
            $name = substr( $name, 0, 31 );
        }
        $this->number_of_sheets++;
        $this->sheet_names[$this->number_of_sheets] = $name;
    }


    /**
     * Set the active sheet being written to.
     * 
     * @param   integer $sheet_id
     * 
     * @access  public
     * @since   3.19.6
     */

    public function set_active_sheet( int $sheet_id ): void {
        if ( !isset( $this->sheet_names[$sheet_id] ) ) {
            throw new Exception( "Invalid sheet number selection" );
        }
        $this->selected_sheet = $sheet_id;
    }


    /**
     * Create and write an excel file from the provided data.
     * 
     * @access  public
     * @since   3.19.6
     */

    public function write(): void {
        if ( $this->generate_verbose_feedback ) {
            $this->start_time = microtime( true );
        }
        if ( !isset ( $this->file_path ) ) {
            echo "No file name and path set";
            die;
        }

        if ( count( $this->sheet_names ) == 0 ) {
            throw new Exception( 'You must add at least one sheet to your spreadsheet.' );
        }

        if ( substr( $this->file_path, -5 ) !== '.xlsx' ) {
            // Add file extension if not present.
            $this->file_path .= '.xlsx';
        }
        
        $this->file_name = array_values(
            array_slice(
                explode( '/', $this->file_path ), -1, 1, true
            )
        )[0];
        $this->file_name = str_replace( '.xlsx', '', $this->file_name );

        $this->create_path = str_replace( $this->file_name . '.xlsx', '', $this->file_path );

        if ( !isset ( $this->data ) || !is_array( $this->data ) ) {
            echo "Invalid data";
            die;
        }

        $required_files = self::REQUIRED_FILES;

        foreach ( $this->sheet_names as $index => $name ) {
            $required_files['xl']['worksheets']["sheet{$index}.xml"] = $this->worksheet_xml_creator( $index );
        }
        $required_files['xl']['_rels']['workbook.xml.rels'] = $this->build_rels();
        $required_files['xl']['workbook.xml'] = $this->build_workbook();
        $required_files['[Content_Types].xml'] = $this->build_content_types();

        $this->generate_files( $required_files );
    }


    /**
     * Execute the creation and download of an .xlsx file.
     * 
     * Note: doesn't work through AJAX.
     * 
     * @access  public
     * @since   3.19.6
     */

    public function write_and_download(): void {
        ob_start();
        $this->write();
        ob_end_clean();
        header( 'Content-Description: File Transfer' );
        header( 'Content-Type: application/octet-stream'  );
        header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'  );
        header( "Content-disposition: attachment; filename=\"" . basename( $this->file_path ) . "\"" );
        header( 'Content-Transfer-Encoding: binary' );
        header( 'Expires: 0' );
        header( 'Cache-Control: must-revalidate' );
        header( 'Pragma: public' );
        readfile( $this->file_path );
        unlink( $this->file_path );
        if ( !$this->generate_basic_feedback && !$this->generate_verbose_feedback ) {
            die;
        }
    }


    /**
     * Set the path and file name of the file you wish to create.
     * 
     * @param   string  $path   The path & file name required
     * 
     * @access  public
     * @since   3.19.6
     */

    public function set_file_path( string $path ): void {
        $this->file_path = $path;
    }


    /**
     * Set the data property. It must be in the form of an array of arrays.
     * 
     * @param   array   $data   The data to set.
     * 
     * @access  public
     * @since   3.19.6
     */

    public function set_data( array $data ): void {
        $this->data[$this->selected_sheet] = $data;
    }


    /**
     * Add a row of data to the existing data property.
     * 
     * @param   array   $data   The row to append.
     * 
     * @access  public
     * @since   3.19.6
     */

    public function append_to_data( array $data ): void {
        $this->data[$this->selected_sheet][] = $data;
    }


    /**
     * Set the first row as a 'header row'
     * 
     * @param   boolean $is_header  Whether or not the 1st row is a header.
     * 
     * @access  public
     * @since   3.19.6
     */

    public function set_header_row( bool $is_header ): void {
        $this->header_row = $is_header;
    }


    /**
     * Set if the user should get some basic feedback when the process is complete.
     * 
     * @param   boolean $set_feedback   Whether or not to return feedback.
     * 
     * @access  public
     * @since   3.19.6
     */

    public function set_basic_feedback( bool $set_feedback ): void {
        $this->generate_basic_feedback = $set_feedback;
    }


    /**
     * Set if the user should get verbose feedback when the process is complete.
     * 
     * Feedback will be in the form of an array
     * 
     * @param   boolean $set_feedback   Whether or not to return feedback.
     * 
     * @access  public
     * @since   3.19.6
     */

    public function set_verbose_feedback( bool $set_feedback ): void {
        $this->generate_verbose_feedback = $set_feedback;
    }


    /**
     * Get the basic feedback.
     * 
     * @return string
     * 
     * @access  public
     * @since   3.19.6
     */

    public function get_basic_feedback(): string {
        return $this->basic_feedback;
    }


    /**
     * Get the verbose feedback.
     * 
     * @param   null|string $param  Call the specific desired parameter.
     *                          ## Options
     *                          - 'file_path'
     *                          - 'rows_created'
     *                          - 'cols_created'
     *                          - 'time_taken'
     *                          Default: null
     * 
     * @return string|array
     * 
     * @access  public
     * @since   3.19.6
     */

    public function get_verbose_feedback( ?string $param = null ): string|array {
        if ( !is_null ( $param ) ) {
            if ( !isset( $this->verbose_feedback[$param] ) ) {
                throw new Exception( "The parameter is not defined." );
            }
            return $this->verbose_feedback[$param];
        }
        return $this->verbose_feedback;
    }

}