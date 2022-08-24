<?php

namespace LBS\Tools\CSV;

/**
 * Read and extract the data of a Comma Seperated Value (.csv) Spreadsheet.
 * 
 * use LBS\Tools\CSV\ReadCSV;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.12.0
 */

class ReadCSV {

    /**
     * The path to the .csv file to be read. This can be set as a property or assigned as a parameter when this object is created
     * 
     * @var string $csv_file_path
     * 
     * @access  public
     * @since   3.12.0
     */

    public string $csv_file_path;

    /**
     * The property containing an array of the extracted data from the .csv file
     * 
     * @var array   $data
     * 
     * @access  public
     * @since   3.12.0
     */

    public array $data = [];

    /**
     * Whether or not the .csv document has a header row
     * 
     * @var boolean $header_row
     * 
     * @access  public
     * @since   3.12.0
     */

    public bool $header_row = true;

    /**
     * A list of headers, used if $this->header_row is true
     * 
     * @var array   $headers
     * 
     * @access  public
     * @since   3.12.0
     */

    public array $headers = [];

    /**
     * Whether or not to delete the .xlsx file.
     * 
     * @var boolean keep_csv    Default: false
     * 
     * @access  public
     * @since   3.12.0
     */

    public bool $keep_csv = false;


    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @param   string|null $path   Path to file being handled
     * 
     * @access  public
     * @since   3.12.0
     */

    public function __construct( ?string $path = null ) {
        if ( !is_null( $path ) ) {
            $this->csv_file_path = $path;
            if ( !is_file( $this->csv_file_path ) ) {
                echo "File not found";
                die;
            }
        }
    } //__construct


    /**
     * Execute the reading of the .csv and assign the extracted data to $this->data.
     * 
     * @access  public
     * @since   3.12.0
     */

    public function read_csv_file(): void {
        if ( ( $handle = fopen( $this->csv_file_path, "r" ) ) !== false ) {
            $i = 0;
            while ( ( $row = fgetcsv( $handle, 1000, "," ) ) !== false ) {
                foreach ( $row as $k => $cell ) {
                    if ( $i == 0 ) {
                        if ( $this->header_row ) {
                            $this->headers[$k] = protect( $cell );
                        } else {
                            $this->data[$i][$k] = protect( $cell );
                        }
                    } else {
                        if ( $this->header_row ) {
                            $this->data[$i][$this->headers[$k]] = protect( iconv( "cp1252", "utf-8", $cell ) );
                        } else {
                            $this->data[$i][$k] = protect( iconv( "cp1252", "utf-8", $cell ) );
                        }
                    }
                }
                $i++;
            }
        } else {
            echo "Cannot read the file {$this->csv_file_path}";
        }
        fclose( $handle );
    }


    /**
     * Clean up all the files if desired. This process is called in $this->__destruct() also.
     * You may wish to call this method if you are going to read and interpret a new .csv file without creating a new instance of the class
     * 
     * @access  public
     * @since   3.12.0
     */

    public function clean_up_files(): void {
        if ( !$this->keep_csv ) {
            unlink( $this->csv_file_path );
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