<?php

/**
 * Functions around performing an update
 * 
 * @author  Gareth Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class Update {

    /**
     * Variables for the performing of the update
     * 
     * @var     $remote_file    The file on the server to be downloaded. Can be custom defined
     * @var     $local_file     The local file which the remote file will be save as, ready for processing
     * 
     * @since   0.1 Pre-alpha
     */

    private $remote_file;
    private $local_file = 'tmp.zip';


    /**
     * Consructor method, things to do when the class is loaded
     * 
     * @since   0.1 Pre-alpha
     */

    public function __construct( $remote_file=null ){
        if ( !is_null( $remote_file ) ){
            $this->remote_file = $remote_file;
        } else{
            $this->remote_file = UPDATE_FILE;
        }
    }//__construct


    /**
     * Goes to the online non git repository and checks for the most up to date version update available
     * 
     * @return  string  Error message
     * @return  boolean Returns true if a new update is found
     * 
     * @since   0.1 Pre-alpha
     */

    public function check_online_update(){
        $update_version = @file_get_contents( UPDATE_URL, true );
        if ( !$update_version ){
            return 'Unable to check for update';
        }
        $new_version = false;
        if ( $update_version == PROJ_VERSION . PROJ_STATUS ){
            return "Program up to date ";
        } else if ( strpos( $update_version, '<body>', 0 ) != false ){
            //check if you're getting back the plain text of the version number or a whole site
            return 'update repository not correctly configured';
        } else {
            //check for beta status
            $update_version_status = '';    
            if ( stripos( $update_version, 'alpha', 0 ) != false ) {
                $update_version_status = 'alpha';
                $update_version = str_ireplace( 'alpha', '', $update_version );
                $update_version = remove_trailing_chars( $update_version, ' ' );  
            } else if ( stripos( $update_version, 'beta', 0 ) != false ){
                $update_version_status = 'beta';
                $update_version = str_ireplace( 'beta', '', $update_version );
                $update_version = remove_trailing_chars( $update_version, ' ' );  
            }//if check for alpha or beta
            
            $current = explode( '.', PROJ_VERSION );
            $server = explode( '.', $update_version );
            
            foreach( $server as $i => $s ){
                if ( isset( $s ) && !isset( $current[$i] ) ){
                    $new_version = true;
                    break;
                } else if ( $s > $current[$i] ){
                    $new_version = true;
                    break;
                }//if
            }//foreach
            
            if ( $update_version_status != PROJ_STATUS ){
                $new_version = true;
            }//if
            
            if ( $new_version ){
                return true;
                // echo "New version <b>$update_version $update_version_status</b> is available ";
                // echo "<input type='submit' class='admin_submit_bttn' name='full_update' value='Perform Update'> ";
            } else {
                return "Program is up to date";
            }//if new version is available
        }
    }


    /**
     * Should be called if an online update is invoked. Downloads and extracts the update then calls for the post update instructions
     * 
     * @since   0.1 Pre-alpha
     */

    public function perform_online_update(){
        $this->download_update();
        $this->extract_update();
        $post_update = new PostUpdate;
        $post_update->run_post_update();
    }


    /**
     * Extracts the recently downloaded tempory zip file and places the files in their correct place, thus performing the physical update. 
     * Finally removes the tempory zip file.
     * 
     * @since   0.1 Pre-alpha
     */

    private function extract_update(){
        $zip = new ZipArchive;
        if( $zip->open( $this->local_file ) != "true" ){
            die( "Error :- Unable to open the Zip File" );
        } else {
            echo "extracting update...<br>";
        }
        
        //Extract Zip File
        if ( $zip->extractTo( HOME_PATH ) ){
            echo "Update extracted, now applying...<br>";
        }
        $zip->close();
        unlink( $this->local_file );
    }


    /**
     * Performs a CURL download from the remote update URL
     * 
     * @since   0.1 Pre-alpha
     */
    
    private function download_update(){
        $zipResource = fopen( $this->local_file, "w" );
        // Get The Zip File From Server
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $this->remote_file );
        curl_setopt( $ch, CURLOPT_FAILONERROR, true );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_BINARYTRANSFER,true );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 ); 
        curl_setopt( $ch, CURLOPT_FILE, $zipResource );
        $page = curl_exec( $ch );
        if( !$page ) {
            die( "Error :- " . curl_error( $ch ) );
        } else {
            echo "Downloading update...<br>";
        }
        curl_close( $ch );
    }

}
