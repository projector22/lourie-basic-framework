<?php

/**
 * Actions to perform Post Update
 * 
 * @author  Gareth Palmer  @evangeltheology
 * 
 * @since   0.1 Pre-alpha
 */

class PostUpdate {

    /**
     * The database structure variable
     * 
     * @var     $db_structure   The variable which will be assigned to the class DatabaseStructure
     * 
     * @since   0.1 Pre-alpha
     */

    private $db_structure;

    /**
     * Consructor method, things to do when the class is loaded
     * 
     * @since   0.1 Pre-alpha
     */

    public function __construct(){
        $this->db_structure = new DatabaseStructure;
    }//__construct


    /**
     * Runs the post update commands
     * 
     * @since   0.1 Pre-alpha
     */
    public function run_post_update(){
        $this->post_update_always_run();
        $this->post_update_specific_changes();
    }

    /**
     * Runs instructions that will always be called post an update, including database checks
     * 
     * @since   0.1 Pre-alpha
     */
    private function post_update_always_run(){
        $this->db_structure->create_tables();
        $this->db_structure->fix_missing_column();
    }

    /**
     * Runs post update instructions which may change from update to update, according to what is being updated
     * 
     * @since   0.1 Pre-alpha
     */
    private function post_update_specific_changes(){
        //In this function - any specific changes that need to be performed post this specific update should be placed
    }

}
