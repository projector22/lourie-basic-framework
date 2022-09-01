<?php

namespace LBF\Tools\Downloads;

use \Exception;
use LBF\HTML\Draw;

/**
 * Class for controlling downloads through the app, rejecting non logged in downloads.
 * 
 * use LBF\Tools\Downloads\DownloadHandler;
 * 
 * @see     https://wordpress.stackexchange.com/questions/281500/protecting-direct-access-to-pdf-and-zip-unless-user-logged-in-without-plugin
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.6.3
 * @since   LRS 3.11.0  Moved to `Framework\Tools\Downloads` and class renamed `DownloadHandler` from `Downloads`.
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                  Namespace changed from `Framework` to `LBF`.
 */

class DownloadHandler {

    /**
     * The full file path of the file that should be downloaded
     * 
     * @var string  $file
     * 
     * @access  public
     * @since   LRS 3.6.3
     */
    
    public string $file;

    /**
     * Name of the file
     * 
     * @var string  $file_name
     * 
     * @access  public
     * @since   LRS 3.6.3
     */

    public string $file_name;

    /**
     * Allow the file to download without restriction
     * 
     * @var boolean $require_login  Default: true
     * 
     * @access  public
     * @since   LRS 3.6.3
     */

    public bool $require_login = true;

    /**
     * Which token to send to src/downloads.php
     * 
     * @var string  $token
     * 
     * @access  public
     * @since   LRS 3.6.3
     */

    public string $token;

    /**
     * Set mimetype for downloading
     * 
     * @var string  $mime_type
     * 
     * @access  public
     * @since   LRS 3.6.3
     */

    public string $mime_type;

    /**
     * Constructor method, things to do when the class is loaded.
     * 
     * @param   string  $file   The file to be downloaded, can be specified later
     *                          Default: null
     * 
     * @access  public
     * @since   LRS 3.6.3
     */

    public function __construct( ?string $file = null ) {
        if ( !is_null ( $file ) ) {
            $this->file = $file;
        }
    } //__construct


    /**
     * Create url link for downloading the file
     * 
     * @param   string  $file   The file to be downloaded, can be specified later
     *                          Default: null
     * 
     * @return  string  url string
     * 
     * @access  public
     * @since   LRS 3.6.3
     */

    public function create_download_url( ?string $file = null ): string {
        if ( !is_null ( $file ) ) {
            $this->file = $file;
        }
        $this->file =urlencode( $this->file );

        if ( !isset ( $this->token ) ) {
            echo "<pre>";
            throw new Exception ( 'No token set' );
            echo "</pre>";
        }

        if ( !isset( $_SERVER['REDIRECT_URL'] ) ) {
            $split = explode( $_SERVER['HTTP_ORIGIN'], $_SERVER['HTTP_REFERER'] )[1];
            $path = explode( '?', $split )[0];
            if ( $path[0] == '/' ) {
                $path = substr( $path, 1 );
            }
        }

        return ( $_SERVER['REDIRECT_URL'] ?? $path ) . "?task=download&token={$this->token}&payload={$this->file}&require-login={$this->require_login}";
    }


    /**
     * Execute the download
     * 
     * Note - you cannot start a download directly from an AJAX for security reasons.
     * It is better to open a new window and execute the download
     * 
     * @access  public
     * @since   LRS 3.6.3
     */

    public function execute_download(): void {
        if ( !isset ( $this->file ) ) {
            echo "<pre>";
            throw new Exception ( 'No file payload set' );
            echo "</pre>";
        }

        $mime_type = isset( $this->mime_type ) ? $this->mime_type : mime_content_type( $this->file );
        $file_name = isset( $this->file_name ) ? $this->file_name : basename( $this->file );

        header( 'Content-Description: File Transfer' );
        header( 'Content-type: ' . $mime_type );
        header( 'Content-Disposition: inline; filename="' . $file_name . '"' );
        header( 'Expires: 0' );
        header( 'Cache-Control: must-revalidate' );
        header( 'Pragma: public' );
        readfile( $this->file );
    }


    /**
     * Generate a file name from the url file url
     * 
     * @access  public
     * @since   LRS 3.6.3
     */

    public function set_filename_from_payload(): void {
        $array = explode( "/", str_replace( "\\", "/", $this->file ) );
        // Get last element in the array, generally the file name
        $this->file_name = $array[array_key_last( $array )];
    }


    /**
     * Display various errors on the screen
     * 
     * @param   integer|null $error  The error code. Default: null
     * 
     * @access  public
     * @since   LRS 3.6.3
     */

    public function download_error( ?int $error = null ): void { // return never when PHP 8.1
        switch ( $error ) {
            case 1:
                echo "File not available for download";
                break;
            case 2:
                echo "Not all required info is available";
                break;
            case 3:
                echo "You are not permitted to view this page";
                break;
            default:
                Draw::action_error();
        }
        die;
    }

}