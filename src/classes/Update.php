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
     * Git variables
     * 
     * @var string  $branch     Which branch to pull from
     * @var string  $user       Which user to use when pulling
     * @var string  $password   Which access key to use
     * @var string  $repo       Repo URL
     * 
     * @since   3.3
     */

    private $branch   = "prod-dist";
    private $user     = "prod_dist";
    private $password = "8U7f2oRBBLfmUeJ766mx";
    private $repo     = "https://gitlab.com/projector22/lourie-basic-framework.git";


    /**
     * Consructor method, things to do when the class is loaded
     * 
     * @since   0.1 Pre-alpha
     */

    public function __construct(){
        if ( file_exists ( UPDATE_CREDENTIALS ) ){
            $json = json_decode( file_get_contents( UPDATE_CREDENTIALS ), true );
            if ( isset( $json['username'] ) ){
                $this->user = $json['username'];
            }
            if ( isset( $json['password'] ) ){
                $this->password = $json['password'];
            }
        }
    }//__construct


    /**
     * Performs a check for update via git
     * 
     * @param   boolean $show_results   Whether or not to draw out a text response, Default: true
     * 
     * @return  boolean                 If update is available
     * 
     * @since   0.1 Pre-alpha
     */

    public function check_for_git_update( $show_results = true ){
        $dir = getcwd();
        $p = explode( '\\', $dir )[count( explode( '\\', $dir ) ) - 1];
        if ( $p == 'src' ){
            chdir( ".." );
        }
        $git_test = shell_exec( "git" );
        $git_installed = true;
        if ( PHP_OS === 'WINNT' ){
            if ( strpos( $git_test, "'git' is not recognized" ) !== false ) {
                $git_installed = false;
            }
        } else {
            if ( strpos( $git_test, "No such file or directory" ) !== false ) {
                $git_installed = false;
            }
        }//if detect Server OS

        if ( !$git_installed ){
            echo "Git not installed - please install git on the server before updates will work";
            die;
        }

        $remote = shell_exec( "git remote 2>&1" );
        if ( strpos( $remote, "fatal: not a git repository (or any of the parent directories): .git" ) ){
            shell_exec( "git init" );
            $remote = shell_exec( "git remote 2>&1" );
        }

        if ( strpos( $remote, 'live' ) === false ){
            $url_p = explode ( '://', $this->repo )[1];
            $url = "https://" . $this->user . ':' . $this->password . '@' . $url_p;
            shell_exec( "git remote add live $url" );
            shell_exec( "git checkout live/" . $this->branch );
        }
        
        $test_fetch = shell_exec( "git fetch live --dry-run 2>&1" );
        chdir( $dir );
        if ( $show_results ){
            if ( strpos( $test_fetch, "live/" . $this->branch ) ){
                echo "Update available ";
                echo "<input type='submit' name='git_full_update' onClick='perform_updates()' value='Perform Update'> ";
            } else {
                echo "Everything up to date ";
            }
        }
        
        return ( strpos( $test_fetch, "live/" . $this->branch ) !== false );
    }


    /**
     * Performs the git commands to update the app
     * 
     * @return  boolean     If update has succeeded or not
     * 
     * @since   0.1 Pre-alpha
     */
    
    public function perform_git_update() {
        if ( $this->check_for_git_update( false ) ){
            $dir = getcwd();
            $p = explode( '\\', $dir )[count( explode( '\\', $dir ) ) - 1];
            if ( $p == 'src' ){
                chdir( ".." );
            }
            shell_exec( "git fetch live 2>&1" );
            $pull = shell_exec( "git reset --hard live/" . $this->branch . " 2>&1" );
            chdir( $dir );
            if ( strpos( $pull, "HEAD is now at" ) !== false ){
                echo "App updated";
                return true;
            } else {
                echo "Update failed";
                return false;
            }            
            
        } else {
            echo "Update failed, Update unavailable";
            return false;
        }//check if update is available
    }

}
