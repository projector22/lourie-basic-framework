<?php

namespace LBF\Actions;

/**
 * Template for Actions API class.
 * 
 * use LBF\Actions\ActionsTemplate;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

interface ActionsTemplate {

    /**
     * Class Constructor.
     * 
     * @since   LBF 0.6.0-beta
     */

    public function __construct();

    /**
     * Execute the action.
     * 
     * @since   LBF 0.6.0-beta
     */

    public function execute(): bool;
}
