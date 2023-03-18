<?php

namespace LBF\App;

class JSImportMapper {

    const LRS_MAP = [

    ];

    private array $map;

    public static function import( array $map ): JSImportMapper {
        $class = __CLASS__;
        $this_obj = new $class;
        $class->map = array_merge(
            self::LRS_MAP,
            $map,
        );
        return $this_obj;
    }

    public function render(): void {
        return json_encode( ['import' => $this->map] );
    }
}