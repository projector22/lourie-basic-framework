<?php

namespace App\Db;

/**
 * Database template for DB Data classes
 * 
 * use App\Db\DbTemplate;
 * 
 * @author  &AUTHOR&
 * 
 * @since   &VERSION&
 */

interface DbTemplate {

    /**
     * Constructor method, things to do when the class is loaded
     * 
     * @param   boolean $select_all     If true, instatiating the class will populate $this->data with all rows from the database,
     *                                  Default: true
     * @param   array   $search_params  Current search parameters
     *                                  ## Options
     *                                  - include_hidden
     *                                  - include_archived
     *                                  - include_deleted
     * 
     *                                  Default: []
     * 
     * @access  public
     * @since   &VERSION&
     */

    public function __construct(bool $select_all = false, array $search_params = []);

    /**
     * Destructor method, things to do when the class is closed
     * 
     * Closes the open database connection
     * 
     * @since   &VERSION&
     */

    public function __destruct();
}
