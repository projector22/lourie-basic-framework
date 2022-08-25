<?php

namespace LBF\Tools\Excel;

use Debugger\Debug;
use \ZipArchive;
use \SimpleXMLElement;

/**
 * Read and extract the data of an Excel (.xlsx) Spreadsheet.
 * 
 * use LBF\Tools\Excel\ReadExcel;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.11.1
 */

class ReadExcel {

    /**
     * The path to the .xlsx file to be read. This can be set as a property or assigned as a parameter when this object is created
     * 
     * @var string $excel_file_path
     * 
     * @access  public
     * @since   3.11.1
     */

    public string $excel_file_path;

    /**
     * The path to the folder which is extracted and decompressed from the .xlsx and .zip files
     * 
     * @var string  $folder_path
     * 
     * @access  public
     * @since   3.11.1
     */

    public string $folder_path;

    /**
     * The public constant of the relative path to the default sheet xml
     * 
     * @var string  DEFAULT_SHEET_DATA
     * 
     * @since   3.11.1
     */

    const DEFAULT_SHEET_DATA  = '/xl/worksheets/sheet1.xml';

    /**
     * The public constant of the relative path to the shared strings xml
     * 
     * @var string  SHARED_STRINGS_DATA
     * 
     * @since   3.11.1
     */

    const SHARED_STRINGS_DATA = '/xl/sharedStrings.xml';

    /**
     * The absolute path of the data xml within the decompressed .zip file folder
     * 
     * @var string  $data_xml
     * 
     * @access  public
     * @since   3.11.1
     */

    public string $data_xml;

    /**
     * The absolute path of the shared string xml within the decompressed .zip file folder
     * 
     * @var string  $shared_strings_xml
     * 
     * @access  public
     * @since   3.11.1
     */

    public string $shared_strings_xml;

    /**
     * Whether or not the .xlsx file is compressed. This is usually detected by $this->unzip_excel()
     * 
     * @var boolean $compressed Default: false
     * 
     * @access  public
     * @since   3.11.1
     */

    public bool $compressed = false;

    /**
     * Converted data extracted from self::DEFAULT_SHEET_DATA
     * 
     * @var object  $extracted_data
     * 
     * @access  public
     * @since   3.11.1
     */

    public object $extracted_data;
    
    /**
     * Converted data extracted from self::SHARED_STRINGS_DATA
     * 
     * @var string  $parsed_strings
     * 
     * @access  public
     * @since   3.11.1
     */

    public object $parsed_strings;

    /**
     * The property containing an array of the extracted data from the .xlsx file
     * 
     * @var array   $data
     * 
     * @access  public
     * @since   3.11.1
     */

    public array $data = [];

    /**
     * Whether or not the .xlsx document has a header row
     * 
     * @var boolean $header_row
     * 
     * @access  public
     * @since   3.11.1
     */

    public bool $header_row = true;

    /**
     * A list of headers, used if $this->header_row is true
     * 
     * @var array   $headers
     * 
     * @access  public
     * @since   3.11.1
     */

    public array $headers = [];

    /**
     * Whether or not to delete the .xlsx file.
     * 
     * @var boolean $keep_xlsx  Default: false
     * 
     * @access  public
     * @since   3.11.1
     */

    public bool $keep_xlsx = false;

    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @param   integer|null    $path   The path of the file.
     * 
     * @access  public
     * @since   3.11.1
     */

    public function __construct( ?string $path = null ) {
        if ( !is_null( $path ) ) {
            $this->excel_file_path = $path;
            if ( !is_file( $this->excel_file_path ) ) {
                echo "File not found";
                die;
            }
        }
    } //__construct


    /**
     * Execute the reading of the .xlsx and assign the extracted data to $this->data.
     * 
     * @access  public
     * @since   3.11.1
     */

    public function read_excel_file(): void {
        $this->unzip_excel();
        $this->set_all_data();
    }


    /**
     * Convert the .xlsx file into a .zip and unzip. Then assign properties based on the extracted data
     * If no other properties are changed - this is generally step 1 in the process of extracting the data
     * 
     * @access  public
     * @since   3.11.1
     */

    public function unzip_excel(): void {
        $this->excel_file_path = normalize_path_string( $this->excel_file_path );
        $folder_name = explode( '.', explode( '/', $this->excel_file_path )[count( explode( '/', $this->excel_file_path ) ) - 1] )[0];
        $this->folder_path = UPLOADS_PATH . $folder_name;
        $rename_file = $this->folder_path . '.zip';
        copy( $this->excel_file_path, $rename_file );
        $zip = new ZipArchive;
        $res = $zip->open( "$rename_file" );
        if ( $res === TRUE ) {
            $zip->extractTo( UPLOADS_PATH . $folder_name );
            $zip->close();
        }

        $this->data_xml           = $this->folder_path . self::DEFAULT_SHEET_DATA;
        $this->shared_strings_xml = $this->folder_path . self::SHARED_STRINGS_DATA;

        if ( !is_file( $this->data_xml ) ) {
            echo "File unzipping failed";
            die;
        }
        if ( is_file ( $this->shared_strings_xml ) ) {
            $this->compressed = true;
        }
    }


    /**
     * Extract data from the unzipped xml files and assign to $this->date_add
     * If no other properties are changed - this is generally step 2 in the process of extracting the data
     * 
     * @access  public
     * @since   3.11.1
     */

    public function set_all_data(): void {
        $this->extracted_data = $this->extract_data( $this->data_xml );
        if ( $this->compressed ) {
            $this->parsed_strings = $this->extract_data( $this->shared_strings_xml );
            foreach ( $this->extracted_data->sheetData->row as $i => $row ) {
                foreach ( $row->c as $k => $cell ) {
                    if ( $i == 0 ) {
                        if ( $this->header_row ) {
                            $this->headers[$k] = protect( $this->get_compressed_value( $cell ) );
                        } else {
                            $this->data[$i][$k] = protect( $this->get_compressed_value( $cell ) );
                        }
                    } else {
                        if ( $this->header_row ) {
                            $value = $this->get_compressed_value( $cell );
                            $this->data[$i][$this->headers[$k]] = is_string( $value ) ? protect( $value ) : '';
                        } else {
                            $this->data[$i][$k] = protect( $this->get_compressed_value( $cell ) );
                        }
                    }
                }
            }
        } else {
            foreach ( $this->extracted_data->sheetData->row as $i => $row ) {
                foreach ( $row->c as $k => $cell ) {
                    if ( $i == 0 ) {
                        if ( $this->header_row ) {
                            $this->headers[$k] = protect( $this->get_uncompressed_value( $cell ) );
                        } else {
                            $this->data[$i][$k] = protect( $this->get_uncompressed_value( $cell ) );
                        }
                    } else {
                        if ( $this->header_row ) {
                            $this->data[$i][$this->headers[$k]] = protect( $this->get_uncompressed_value( $cell ) );
                        } else {
                            $this->data[$i][$k] = protect( $this->get_uncompressed_value( $cell ) );
                        }
                    }
                }
            }
        }
    }


    /**
     * Get the data value from the cell object when working on a compressed file
     * 
     * @param   object  $cell   The cell object which is being examined
     * 
     * @return  string          String value of the cell
     * 
     * @access  private
     * @since   3.11.1
     */

    private function get_compressed_value( object $cell ): string {
        if ( isset( $cell->attributes->t ) && $cell->attributes->t == 's' ) {
            if ( gettype( $this->parsed_strings->si[$cell->v]->t ) == 'object' ) {
                // If the value is an object, assume the cell is blank
                return '';
            }
            return $this->parsed_strings->si[$cell->v]->t;
        } else {
            return $cell->v;
        }
    }


    /**
     * Get the data value from the cell object when working on an uncompressed file
     * 
     * @param   object  $cell   The cell object which is being examined
     * 
     * @return  string          String value of the cell
     * 
     * @access  private
     * @since   3.11.1
     */

    private function get_uncompressed_value( object $cell ): string {
        $value = isset( $cell->is->t ) ? $cell->is->t : $cell->v;
        if ( is_object( $value ) ) {
            $value = '';
        }
        return $value;
    }


    /**
     * Extract and read xml data and encode it in a more readable format
     * 
     * @param   string  $path   The path to the the xml file to be read
     * 
     * @return  object          Encoded data
     * 
     * @access  private
     * @since   3.11.1
     */

    private function extract_data( string $path ): object {
        $container = new SimpleXMLElement( file_get_contents( $path ) );
        /**
         * Would've preferred to do it as one, but larger spreadsheets cause a fatal error, memory limit to be thrown.
         * On even larger sheets, we may have to increase the memory limit, but in the mean time:
         * Drop each variable after they're used to free up memory
         * 
         * @since   3.12.0
         */
        $data = json_encode( $container );
        unset( $container );
        $data1 = str_replace( '@', '', $data );
        unset( $data );
        return json_decode( $data1 );
    }


    /**
     * Recursively delete files from the server, including folders. Used mostly in the context of uploading XLSX files and converting them
     * 
     * @param   string  $target     The path to the file being deleted
     * 
     * Credit:
     * @link    https://paulund.co.uk/php-delete-directory-and-files-in-directory
     * 
     * @access  private
     * @since   3.1.0
     * @since   3.11.1  Moved from App\Admin\ImportHandler to LBF\Tools\Excel\ReadExcel
     */

    private function delete_files( string $target ): void {
        // This bit for deleting the stupid hidden file that the next bit won't
        $files = glob( $target . '_rels/{,.}*', GLOB_BRACE );
        foreach ( $files as $file ) {
            if ( is_file( $file ) ) {
                unlink( $file );
            }
        }
            
        if ( is_dir( $target ) ) {
            $files = glob( $target . '*', GLOB_MARK ); // GLOB_MARK adds a slash to directories returned
            foreach( $files as $f ) {
                $this->delete_files( $f );
            }
            rmdir( $target );
        } else if( is_file( $target ) ) {
            unlink( $target );
        }
    }


    /**
     * Clean up all the files if desired. This process is called in $this->__destruct() also.
     * You may wish to call this method if you are going to read and interpret a new .xlsx file without creating a new instance of the class
     * 
     * @access  public
     * @since   3.11.1
     */

    public function clean_up_files(): void {
        unlink( $this->folder_path . '.zip' );
        $this->delete_files( $this->folder_path . '/' );
        if ( !$this->keep_xlsx ) {
            unlink( $this->excel_file_path );
        }
    }


    /**
     * Destructor method, things to do when the class is closed.
     * Delete the .zip, the unzipped folder and if desired, the .xlsx
     * 
     * Closes the open database connection
     * 
     * @access  public
     * @since   3.11.0
     */

    public function __destruct() {
        $this->clean_up_files();
    } //__destruct

}