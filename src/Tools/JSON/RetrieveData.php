<?php

namespace LBF\Tools\JSON;

/**
 * Trait to be used with a AppJson enum. Allows for the use of returning data of the specified
 * enum as an array or object.
 * 
 * use LBF\Tools\JSON\RetrieveData;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

trait RetrieveData {

    /**
     * Return the contents of the specified JSON file enum as an array.
     * 
     * @return  array
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function as_array(): array {
        return JSONTools::read_json_file_to_array( $this->path() );
    }


    /**
     * Return the contents of the specified JSON file enum as an object.
     * 
     * @return  object
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function as_object(): object {
        return JSONTools::read_json_file_to_object( $this->path() );
    }
    
}
