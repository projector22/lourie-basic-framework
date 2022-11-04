<?php

namespace LBF\Traits;

use LBF\Errors\Classes\UndefinedProperty;

/**
 * Set `__get` and `__set` for any class that needs it.
 * 
 * use App\Traits\GetSet;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.4.4-beta
 */

trait GetSet {

    /**
     * Placeholder for dynamically set properties.
     * 
     * @var array   $properties Default: []
     * 
     * @access  private
     * @since   LBF 0.4.4-beta
     */

    private array $properties = [];

    /**
     * Setter for dynamic properties.
     * 
     * @param   string  $name   The key to the property.
     * @param   mixed   $value  The value of the property.
     * 
     * @access  public
     * @since   LBF 0.4.4-beta
     */

    public function __set( string $name, mixed $value): void {
        $this->properties[$name] = $value;
    }


    /**
     * Getter for dynamic properties.
     * 
     * @param   string  $name   The key to the property
     * 
     * @return  mixed
     * 
     * @throws  UndefinedProperty   If the key does not exist on the class.
     * 
     * @access  public
     * @since   LBF 0.4.4-beta
     */

    public function __get( string $name ): mixed {
        if ( !isset( $this->properties[$name] ) ) {
            throw new UndefinedProperty( "Property {$name} does not exist on " . __CLASS__ );
        }
        return $this->properties[$name];
    }

}