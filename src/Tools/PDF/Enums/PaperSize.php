<?php

namespace LBF\Tools\PDF\Enums;

enum PaperSize {

    case A4;



    public function value(): string {
        return match ( $this ) {
            self::A4 => 'A4',
        };
    }


}