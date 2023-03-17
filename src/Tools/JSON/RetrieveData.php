<?php

namespace LBF\Tools\JSON;

interface RetrieveData {

    public function data_as_array(): array {
        return JSONTools::read_json_file_to_array( $this->path() );
    }

    public function data_as_object(): array {
        return JSONTools::read_json_file_to_object( $this->path() );
    }

    
}