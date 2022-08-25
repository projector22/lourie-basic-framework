<?php

namespace LBF\Tools\CSV;

use LBF\Tools\Downloads\DownloadHandler;

/**
 * Write data of to a Comma Seperated Value (.csv) Spreadsheet.
 * 
 * use LBF\Tools\CSV\WriteCSV;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.12.0
 */

class WriteCSV {

    /**
     * The name of the file which should be saved.
     * 
     * @var string  $file_name  Default: temporary_file.csv - but can be changed
     * 
     * @access  public
     * @since   3.12.0
     */

    public string $file_name = 'temporary_file.csv';

    /**
     * The path (without file name) to which the file is to be saved.
     * 
     * @var string  $save_path  Default: TEMPLATES_PATH
     * 
     * @access  public
     * @since   3.12.0
     */

    public string $save_path = TEMPLATES_PATH;

    /**
     * An array of entries to be used as the heading row
     * 
     * @var array   $headings   Default: []
     * 
     * @access  public
     * @since   3.12.0
     */

    public array $headings = [];

    /**
     * An array of arrays. Each array is a row of the CSV
     * 
     * @var array   $data   Default: []
     * 
     * @access  public
     * @since   3.12.0
     */

    public array $data = [];

    /**
     * Whether or not to keep or delete the file once everything is finished
     * 
     * @var boolean $keep_file  Default: false
     * 
     * @access  public
     * @since   3.12.0
     */

    public bool $keep_file = false;


    /**
     * Write the assigned data to the assigned file
     * 
     * @access  public
     * @since   3.12.0
     */

    public function write_file(): void {
        $file = $this->save_path . $this->file_name;
        $csv_file = fopen( $file, 'w') ;

        if ( count( $this->headings ) > 0 ) {
            fputcsv( $csv_file, $this->headings );
        }
        foreach ($this->data as $fields ) {
            fputcsv( $csv_file, $fields );
        }
        fclose( $csv_file );
    }


    /**
     * Execute download the file to user
     * 
     * @access  public
     * @since   3.12.0
     */

    public function download(): void {
        $download = new DownloadHandler;
        $download->file = $this->save_path . $this->file_name;
        $download->file_name = $this->file_name;
        $download->mime_type = 'text/csv';
        $download->execute_download();
    }


    /**
     * If $this->keep_file is false, delete the generated CSV
     * 
     * @access  public
     * @since   3.12.0
     */
    public function clean_up_files(): void {
        if ( file_exists( $this->save_path . $this->file_name ) && !$this->keep_file ) {
            unlink( $this->save_path . $this->file_name );
        }
    }


    /**
     * Destructor method, things to do when the class is closed.
     * Delete the .zip, the unzipped folder and if desired, the .csv
     * 
     * Closes the open database connection
     * 
     * @access  public
     * @since   3.12.0
     */

    public function __destruct() {
        $this->clean_up_files();
    } //__destruct

}