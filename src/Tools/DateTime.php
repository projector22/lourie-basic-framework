<?php

namespace LBF\Tools;

use DateTime as GlobalDateTime;

class DateTime extends GlobalDateTime {
    public function timestamp(): string {
        return $this->format( 'Y-m-d G:i:s' );
    }

    public function date(): string {
        return $this->format( 'Y-m-d' );
    }
    public function time(): string {
        return $this->format( 'G:i' );
    }
    public function year(): string {
        return $this->format( 'Y' );
    }
}