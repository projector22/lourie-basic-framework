<?php

namespace LBF\Db;

use Exception;
use LBF\Db\FieldAssets\PrimaryKey;
use LBF\Db\FieldAssets\SQLFunctions;
use stdClass;

class Field {
    public string $Field;
    public string $Type;
    public string $Null;
    public string $Key;
    public string $Default;
    public string $Extra;

    public stdClass $test;

    public function __construct(string $field_name) {
        $this->Field = $field_name;
        $this->test = new stdClass;
        $this->test->Field   = $field_name;
        $this->test->Type    = '';
        $this->test->Null    = 'YES';
        $this->test->Key     = '';
        $this->test->Default = '';
        $this->test->Extra   = '';
    }

    public static function name(string $field_name): Field {
        return new Field($field_name);
    }

    public function generate(): string {
        $field = [$this->Field];
        if (isset($this->Type)) {
            $field[] = $this->Type;
        }
        if (isset($this->Null)) {
            $field[] = $this->Null;
        }
        if (isset($this->Key)) {
            $field[] = $this->Key;
        }
        if (isset($this->Default)) {
            $field[] = $this->Default;
        }
        if (isset($this->Extra)) {
            $field[] = $this->Extra;
        }
        return implode(' ', $field);
    }

    public function validate($object): bool {
        return $object == $this->test;
    }

    public function primary_key(PrimaryKey $type, bool $auto_increment = true): static {
        switch ($type) {
            case PrimaryKey::AUTO_INT:
                $this->Type = 'INT';
                if ($auto_increment) {
                    $this->Extra = 'auto_increment';
                    $this->test->Extra = 'auto_increment';
                }
                break;
            case PrimaryKey::UUID:
                $this->Type = 'CHAR(36)';
                $this->default(SQLFunctions::UUID);
                break;
            case PrimaryKey::MD5:
                $this->Type = 'CHAR(32)';
                break;
        }
        $this->test->Type = strtolower($this->Type);
        $this->Null = 'NOT NULL';
        $this->test->Null = 'NO';
        $this->Key = 'PRIMARY KEY';
        $this->test->Key = 'PRI';
        return $this;
    }

    public function type(string $type, ?int $limit = null): static {
        $this->Type = $type;
        if (!is_null($limit)) {
            $this->Type .= "({$limit})";
        }
        $this->test->Type = strtolower($this->Type);
        return $this;
    }

    public function not_null(): static {
        $this->Null = 'NOT NULL';
        $this->test->Null = 'NO';
        return $this;
    }

    public function null(): static {
        if ($this->Null === 'NOT NULL') {
            throw new Exception("Cannot set the default value as Null, when Not Null is set.");
        }
        $this->test->Null = 'YES';
        return $this;
    }

    public function unique(): static {
        $this->Extra = 'UNIQUE';
        $this->test->Key = 'UNI';
        return $this;
    }

    public function default(int|string|SQLFunctions $default, ?string $fn_value = null): static {
        $this->Default = "DEFAULT ";
        if ($default instanceof SQLFunctions) {
            if (!is_null($fn_value)) {
                $this->Default .= "({$default->fn($fn_value)})";
            } else {
                $this->Default .= "({$default->fn()})";
            }
            $this->test->Default = strtolower(!is_null($fn_value) ? $default->fn($fn_value) : $default->fn());
            $this->test->Extra = 'DEFAULT_GENERATED';
        } else {
            $this->Default = $default;
            $this->test->Default = strtolower($default);
            if (preg_match('/\(.+?\)/', $default) || $default = 'CURRENT_TIMESTAMP' || $default = 'CURRENT_DATE' || $default = 'CURRENT_TIME' || $default = 'CURRENT_USER') {
                $this->Default = "({$default})";
                $this->test->Extra = 'DEFAULT_GENERATED';
            }
        }
        return $this;
    }
}
