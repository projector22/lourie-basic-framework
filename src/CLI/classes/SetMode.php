<?php

/**
 * Execute the setting of different modes on the app.
 * 
 * Current options
 * - Switch set-mode prod
 * - Switch set-mode dev
 * - Switch set-mode maint
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.14.0
 * @since   3.17.4  Renamed from Dev to SetMode.
 */

class SetMode {

    /**
     * The chosen mode to set the app to
     * 
     * @var string|null $mode
     * 
     * @access  public
     * @since   3.17.4
     */

    public ?string $mode = null;

    /**
     * Possible options for setting the mode.
     * 
     * @var array   OPTIONS
     * 
     * @access  public
     * @since   3.17.4
     */

    const OPTIONS = [
        'prod'  => 'MODE_PRODUCTION',
        'dev'   => 'MODE_DEVELOPEMENT',
        'maint' => 'MODE_MAINTENANCE',
    ];

    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @access  public
     * @since   3.14.0
     */

    public function __construct() {
        if ( !file_exists( CONFIG_FILE ) ) {
            echo "App is not yet installed, please install first through the web interface first.";
            Draw::end_of_script();
            die;
        }
    }


    /**
     * Execute the change
     * 
     * @access  public
     * @since   3.14.0
     * @since   3.17.4  Revamped for mode switching
     */

    public function execute() {
        $this->validate_switcher();
        $config = file_get_contents( CONFIG_FILE );

        $prod  = '$environment = MODE_PRODUCTION;';
        $dev   = '$environment = MODE_DEVELOPEMENT;';
        $maint = '$environment = MODE_MAINTENANCE;';

        $hide_prod  = '// $environment = MODE_PRODUCTION;';
        $hide_dev   = '// $environment = MODE_DEVELOPEMENT;';
        $hide_maint = '// $environment = MODE_MAINTENANCE;';

        switch ( $this->mode ) {
            case 'prod':
                $config = str_replace( $hide_prod, $prod, $config );
                if ( !str_contains( $config, $hide_dev ) ) {
                    $config = str_replace( $dev, $hide_dev, $config );
                }
                if ( !str_contains( $config, $hide_maint ) ) {
                    $config = str_replace( $maint, $hide_maint, $config );
                }

                system( 'composer install --no-dev 2>&1' );
                break;
            case 'dev':
                $config = str_replace( $hide_dev, $dev, $config );
                if ( !str_contains( $config, $hide_prod ) ) {
                    $config = str_replace( $prod, $hide_prod, $config );
                }
                if ( !str_contains( $config, $hide_maint ) ) {
                    $config = str_replace( $maint, $hide_maint, $config );
                }

                system( 'composer install 2>&1' );
                break;
            case 'maint':
                $config = str_replace( $hide_maint, $maint, $config );
                if ( !str_contains( $config, $hide_prod ) ) {
                    $config = str_replace( $prod, $hide_prod, $config );
                }
                if ( !str_contains( $config, $hide_dev ) ) {
                    $config = str_replace( $dev, $hide_dev, $config );
                }
                break;
        }
        $file = fopen( CONFIG_FILE, 'w' ) or die( "Unable to write config.php file" );
        fwrite( $file, $config );            
        fclose( $file );
        echo "Success";
        Draw::lines( 1 );
    }


    /**
     * Validate the user's argument
     * 
     * @access  private
     * @since   3.14.0
     * @since   3.17.4  Converted to mode from dev.
     */

    private function validate_switcher(): void {
        if ( !in_array( $this->mode, array_keys( self::OPTIONS ) ) && !is_null( $this->mode ) ) {
            echo "Invalid argument set. Please set either 'prod', 'dev' or 'maint'";
            Draw::end_of_script();
            die;
        }
        if ( is_null( $this->mode ) ) {
            echo "No argument set";
            Draw::end_of_script();
            die;
        }
    }

}