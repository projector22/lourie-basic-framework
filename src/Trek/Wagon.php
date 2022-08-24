<?php

namespace LBS\Trek;

/**
 * A template class for any treks that are created
 * 
 * use LBS\Trek\Wagon;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.14.0
 */

interface Wagon {

    /**
     * Any post update changes to be made to the database and codebase
     * These are usually database changes, but might also be used for cleaning up the bin folder, for example
     * 
     * @since   3.14.0
     */

    public function out();

    /**
     * As far as possible roll back any changes made in out.
     * Any changes that cannot be rolled back should have a comment indicating this.
     * 
     * @since   3.14.0
     */

    public function back();

}