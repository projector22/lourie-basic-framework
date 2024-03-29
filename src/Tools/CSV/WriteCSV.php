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
 * @since   LRS 3.12.0
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                      Namespace changed from `Framework` to `LBF`.
 */

class WriteCSV {

    /**
     * The name of the file which should be saved.
     * 
     * @var string  $file_name  Default: temporary_file.csv - but can be changed
     * 
     * @access  public
     * @since   LRS 3.12.0
     */

    public string $file_name = 'temporary_file.csv';

    /**
     * An array of entries to be used as the heading row
     * 
     * @var array   $headings   Default: []
     * 
     * @access  public
     * @since   LRS 3.12.0
     */

    public array $headings = [];

    /**
     * An array of arrays. Each array is a row of the CSV
     * 
     * @var array   $data   Default: []
     * 
     * @access  public
     * @since   LRS 3.12.0
     */

    public array $data = [];

    /**
     * Whether or not to keep or delete the file once everything is finished
     * 
     * @var boolean $keep_file  Default: false
     * 
     * @access  public
     * @since   LRS 3.12.0
     */

    public bool $keep_file = false;


    /**
     * Class constructor.
     * 
     * @param   string  $save_path  The path to which CSV files should be written.
     * 
     * @access  public
     * @since   LRS 3.28.0
     */

    public function __construct(
        /**
         * The path (without file name) to which the file is to be saved.
         * 
         * @var string  $save_path  In LRS, should be set to TEMPLATES_PATH
         * 
         * @access  public
         * @since   LRS 3.12.0
         */

        public string $save_path
    ) {
        // Nothing more to be done.        
    }


    /**
     * Write the assigned data to the assigned file
     * 
     * @access  public
     * @since   LRS 3.12.0
     */

    public function write_file(): void {
        $file = $this->save_path . $this->file_name;
        $csv_file = fopen($file, 'w');

        if (count($this->headings) > 0) {
            fputcsv($csv_file, $this->headings);
        }
        foreach ($this->data as $fields) {
            fputcsv($csv_file, $fields);
        }
        fclose($csv_file);
    }


    /**
     * Execute download the file to user
     * 
     * @access  public
     * @since   LRS 3.12.0
     */

    public function download(): void {
        DownloadHandler::file($this->save_path . $this->file_name)->download();
    }


    /**
     * If $this->keep_file is false, delete the generated CSV
     * 
     * @access  public
     * @since   LRS 3.12.0
     */
    public function clean_up_files(): void {
        if (file_exists($this->save_path . $this->file_name) && !$this->keep_file) {
            unlink($this->save_path . $this->file_name);
        }
    }


    /**
     * Destructor method, things to do when the class is closed.
     * Delete the .zip, the unzipped folder and if desired, the .csv
     * 
     * Closes the open database connection
     * 
     * @access  public
     * @since   LRS 3.12.0
     */

    public function __destruct() {
        $this->clean_up_files();
    }
}
