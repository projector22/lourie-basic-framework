<?php

/**
 * Draw out the help section
 * 
 * Current options
 * - php lrs help
 * - php lrs help all
 * - php lrs help set-mode
 * - php lrs help dev-tools
 * - php lrs help init
 * - php lrs help trek
 * - php lrs help rollover
 * - php lrs help update
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.14.0
 */

class Help {

    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @param   array   $arguments  Arguments passed to the CLI.
     *                              If null, help will wait for you to call a method.
     *                              Default: null
     * 
     * @access  public
     * @since   3.14.0
     */

    public function __construct( ?array $arguments = null ) {
        echo "Welcome to Lourie Registration System CLI tool";
        Draw::lines( 1 );
        if ( $arguments !== null ) {
            $search = isset( $arguments[2] ) ? $arguments[2] : 'none';
        } else {
            $search = null;
        }
        Draw::lines( 2 );
        switch ( $search ) {
            case 'all':
                $this->all();
                break;
            case 'set-mode':
                $this->set_mode();
                break;
            case 'dev-tools':
                $this->dev_tools();
                break;
            case 'init':
                $this->init();
                break;
            case 'trek':
                $this->trek();
                break;
            case 'rollover':
                $this->rollover();
                break;
            case 'update':
                $this->update();
                break;
            case null:
                break;
            default:
                $this->meta();
        }
    }


    /**
     * The default message to show
     * 
     * @access  public
     * @since   3.14.0
     */

    public function meta(): void {
        echo "You can search for help using the following commands:";
        Draw::lines( 2 );
        echo "php lrs help all";
        Draw::lines( 1 );
        echo "php lrs help set-mode";
        Draw::lines( 1 );
        echo "php lrs help dev-tools";
        Draw::lines( 1 );
        echo "php lrs help init";
        Draw::lines( 1 );
        echo "php lrs help trek";
        Draw::lines( 1 );
        echo "php lrs help rollover";
        Draw::lines( 1 );
        echo "php lrs help update";
        Draw::lines( 1 );
    }


    /**
     * Draw the complete help system
     * 
     * @access  public
     * @since   3.14.0
     */

    public function all(): void {
        $this->set_mode();
        Draw::lines( 3 );
        $this->dev_tools();
        Draw::lines( 3 );
        $this->init();
        Draw::lines( 3 );
        $this->trek();
        Draw::lines( 3 );
        $this->rollover();
        Draw::lines( 3 );
        $this->update();
    }


    /**
     * Help for the dev section
     * 
     * @access  public
     * @since   3.14.0
     */

    public function set_mode(): void {
        echo "You can switch the mode of the app into the different modes. Your choices are 'production', 'maintenance' or 'development' with the following commands:";
        Draw::lines( 2 );
        $this->instruction( "php lrs set-mode prod" );
        Draw::lines( 1 );
        $this->instruction( "php lrs set-mode dev" );
        Draw::lines( 1 );
        $this->instruction( "php lrs set-mode maint" );
        Draw::lines( 2 );
        echo "Note - dev mode should only be used in developement, it should be set to off on production servers. Maintenance mode should be set for a short a time as possible.";
    }


    /**
     * Help for the dev tools section
     * 
     * @access  public
     * @since   3.14.0
     */

    public function dev_tools(): void {
        echo "You can uninstall the server installation by using the following command:";
        Draw::lines( 2 );
        $this->instruction( "php lrs dev-tools uninstall" );
        Draw::lines( 2 );
        echo "You can empty the server of all data, without changing any configurations by using the following command:";
        Draw::lines( 2 );
        $this->instruction( "php lrs dev-tools clear-data" );
        Draw::lines( 2 );
        echo "You can reset configuration to factory by using the following command:";
        Draw::lines( 2 );
        $this->instruction( "php lrs dev-tools config-reset" );
    }


    /**
     * Help for the init section
     * 
     * @access  public
     * @since   3.14.0
     */

    public function init(): void {
        echo "Use the following command to initialize the app and should be run when the server is first installed:";
        Draw::lines( 2 );
        $this->instruction( "php lrs init" );
    }


    /**
     * Help for the trek section
     * 
     * @access  public
     * @since   3.14.0
     */

    public function trek(): void {
        echo "You can create a new trek in dev mode by using the following command:";
        Draw::lines( 2 );
        $this->instruction( "php lrs trek create" );
        Draw::lines( 2 );
        echo "You can update your app to the latest version by running the following command:";
        Draw::lines( 2 );
        $this->instruction( "php lrs trek out" );
        Draw::lines( 2 );
        echo "You can roll back changes by running the following command:";
        Draw::lines( 2 );
        $this->instruction( "php lrs trek back n"     . Draw::tabs( 1 ) . "<- n is the number of step back." . Draw::tabs( 2 ) . "Eg. php lrs trek back 2" );
        Draw::lines( 1 );
        $this->instruction( "php lrs trek back x.y.z" . Draw::tabs( 1 ) . "<- x.y.z is the version to roll back to." . Draw::tabs( 1 ) . "Eg. php lrs trek back 3.14.2" );
        Draw::lines( 1 );
        $this->instruction( "php lrs trek back"       . Draw::tabs( 1 ) . "<- roll back as far as possible, essentially a factory reset" );
        Draw::lines( 1 );
    }


    /**
     * Help for the rollover section
     * 
     * @access  public
     * @since   3.14.0
     */

    public function rollover(): void {
        echo "You can roll over the app to the next academic year by using the following command";
        Draw::lines( 2 );
        $this->instruction( "php lrs rollover" );
        Draw::lines( 2 );
        echo "If you add the switch `-y` to the command, it will run without confirmation";
        Draw::lines( 2 );
        $this->instruction( "php lrs rollover -y" );
        Draw::lines( 1 );
    }


    /**
     * Help for the update section
     * 
     * @access  public
     * @since   3.14.0
     */

    public function update(): void {
        echo "THIS FEATURE IS VERY MUCH IN BETA. DO NOT USE IN PRODUCTION! USE AT OWN RISK!";
        Draw::lines( 1 );
        echo "You can check for an update by using the following command:";
        Draw::lines( 2 );
        $this->instruction( "php lrs update check" );
        Draw::lines( 2 );
        echo "You can perform an update by using the following command:";
        Draw::lines( 2 );
        $this->instruction( "php lrs update install" );
        Draw::lines( 1 );
    }


    /**
     * Draw a basic code onto the terminal
     * 
     * @param   string  $code   The code to draw
     * @param   integer $tabs   How many tabs to draw
     * 
     * @access  private
     * @since   3.14.0
     */

    private function instruction( string $code, int $tabs = 1 ): void {
        echo Draw::tabs( $tabs ) . $code;
    }

}