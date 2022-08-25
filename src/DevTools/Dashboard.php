<?php

namespace LBF\DevTools;

use LBF\Db\ConnectMySQL;
use LBF\HTML\Draw;
use LBF\HTML\Form;
use LBF\HTML\HTML;
use LBF\HTML\Button;
use LBF\Img\SVGImages;

/**
 * This class handles the dev tools mode dashboard.
 * 
 * use LBF\DevTools\Dashboard;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.19.0
 */

class Dashboard extends ConnectMySQL {

    /**
     * Class constructor, connect the database
     * 
     * @access  public
     * @since   3.19.0
     */

    public function __construct() {
        $this->conn = $this->connect_db();
    }


    /**
     * Draw various maintenance mode pages
     * 
     * @access  public
     * @since   3.19.0
     */

    public function construct_page(): void {
        HTML::div( ['class' => 'maintenance_mode_content'] );
        HTML::heading( 1, "Development Mode - Tools", ['class' => 'text_align_center'] );

        /**
         * @todo
         * 
         * Figure out a generic way of determining if the site is in DEV mode, and therefore this is enabled.
         */
        $is_super_admin = true; // Session::value( Sessions::SESSION_USER_PERMISSIONS, 'super_admin' );
        if ( $is_super_admin ) {
            $this->draw_dashboard();
        } else {
            $this->page_closed();
        }
        HTML::close_div();
    }


    /**
     * Draw out the dashboard
     * 
     * @access  private
     * @since   3.19.0
     */

    private function draw_dashboard(): void {
        HTML::div( ['class' => 'maintenance_mode_dashboard_container'] );
        switch ( $_GET['dev-page'] ?? '' ) {
            case 'table-tool-creator':
                $this->table_tool_creator();
                break;
            default:
                $this->dashboad_entries();
        }
        Button::general( [
            'content' => 'Exit',
            'href'    => 'home',
        ] );
        HTML::close_div();
    }

        
    /**
     * Draw out the standard page given to users
     * 
     * @access  private
     * @since   3.19.0
     */

    private function page_closed(): void {
        HTML::img( [
            'src'   => html_path( SVGImages::maintenance->path() ),
            'class' => 'error_img',
        ] );
        HTML::div_container( [
            'class' => 'text_align_center',
        ], "Unfortunately you cannot do anything at this time. Please try again later" );
    }


    /**
     * Draw out the various dashboard entries
     * 
     * @access  private
     * @since   3.19.0
     */

    private function dashboad_entries(): void {
        Draw::set_input_multi_line();
        Button::general( [
            'content' => 'Create Table Tools from Table',
            'href'    => '?dev-page=table-tool-creator',
        ] );
        Draw::close_multi_line_input();
    }


    /**
     * The database tables creation tool.
     * 
     * @access  private
     * @since   3.19.0
     */

    private function table_tool_creator(): void {
        echo "This tool allows you to create the PHP classes required to interact with the database.";
        Draw::lines( 1 );
        Draw::line_separator();

        $tables = $this->get_tables();
        Form::select_box( [
            'lebel'    => 'Select Table',
            'id'       => 'dev--table_name_box',
            'data'     => build_item_droplist( $tables ),
            'hint'     => 'Select a table from the database for which to create PHP classes.',
            'required' => true,
            'validate' => [
                'nil_value' => 'nil',
            ],
        ] );

        /**
         * @see src\js\pages\devtools\Scripts.js
         * -> overwrite_name.onchange
         */

        Form::toggle( [
            'label' => 'Overwrite Table Name',
            'id'    => 'dev--overwrite_name',
            'value' => false,
            'hint'  => 'Ordinarily this tool uses the table\'s name (without the prefix) to create class names. If you want to set your own, switch this on.',
        ] );

        Form::text( [
            'label'    => 'New name',
            'id'       => 'dev--overwrite_name__new_name',
            'hint'     => 'Set a different name to use for your class',
            'size'     => 50,
            'disabled' => true,
        ] );

        /**
         * @see src\js\pages\devtools\Scripts.js
         * -> prepare_files.onclick
         */
        Button::general( [
            'content' => 'Prepare Tables',
            'id'      => 'dev--prepare_files',
            'colour'  => 'blue',
        ] );
        /**
         * @see src\js\pages\devtools\Scripts.js
         * -> execute_creation.onclick
         */
        Button::general( [
            'content' => 'Execute',
            'id'      => 'dev--execute_creation',
            'colour'  => 'green',
            'hidden'  => true,
        ] );

        Draw::code_feedback_box( 'dev--feedback', '500px' );

        Draw::lines( 2 );
        Draw::line_separator();
        Button::back( ['href' => '@dev-tools'] );
    }

}