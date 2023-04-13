<?php

namespace LBF\Tools;

use DateTime as GlobalDateTime;

/**
 * Methods of extracting various date time formats on the fly.
 * 
 * use LBF\Tools\DateTime;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

class DateTime extends GlobalDateTime {

    /**
     * A general timestamp ('Y-m-d G:i:s')
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function timestamp(): string {
        return $this->format('Y-m-d G:i:s');
    }


    /**
     * The date, formatted as Y-m-d.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function date(): string {
        return $this->format('Y-m-d');
    }


    /**
     * The current time formatted as G:i
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function time(): string {
        return $this->format('G:i');
    }


    /**
     * The current year.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function year(): string {
        return $this->format('Y');
    }
}
