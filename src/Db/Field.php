<?php

namespace LBF\Db;

use Exception;
use LBF\Db\FieldAssets\PrimaryKey;
use LBF\Db\FieldAssets\SQLFunctions;
use LBF\HTML\Draw;
use stdClass;

class Field {
    public string $Field;
    public string $Type;
    public string $Null;
    public string $Key;
    public string $Default;
    public string $Extra;
    public string $After;

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
        if (isset($this->After)) {
            $field[] = $this->After;
        }
        return implode(' ', $field);
    }

    public function validate(stdClass $object): bool {
        // if ($object != $this->test) {
        //     Draw::line_separator();
        //     Draw::print_red("ON DATABASE");
        //     print_r($object);
        //     Draw::print_red("ON STRUCTURE");
        //     print_r($this->test);
        //     Draw::line_separator();
        // }
        return $object == $this->test;
    }

    public function primary_key(PrimaryKey $type = PrimaryKey::AUTO_INT, bool $auto_increment = true, int $txt_length = 10): static {
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
            case PrimaryKey::UUID_PLACEHOLDER:
                $this->Type = 'VARCHAR(36)';
                /** @todo Merge in with above */
                break;
            case PrimaryKey::UUID_PLACEHOLDER2:
                $this->Type = 'CHAR(36)';
                /** @todo Merge in with above */
                break;
            case PrimaryKey::MD5:
                $this->Type = 'VARCHAR(32)';
                break;
            case PrimaryKey::MD5_CHAR:
                $this->Type = 'CHAR(32)';
                /** @todo Merge in with above */
                break;
            case PrimaryKey::TXT:
                $this->Type = "VARCHAR({$txt_length})";
                break;
        }
        $this->test->Type = strtolower($this->Type);
        $this->Null = 'NOT NULL';
        $this->test->Null = 'NO';
        $this->Key = 'PRIMARY KEY';
        $this->test->Key = 'PRI';
        return $this;
    }

    public function type(string $type, ?int $limit = null, ?int $decimal = null): static {
        $this->Type = $type;
        if (!is_null($decimal)) {
            $this->Type .= "({$limit},{$decimal})";
        } else if (!is_null($limit)) {
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
            if ($default == SQLFunctions::CURRENT_TIMESTAMP) {
                $this->test->Default = 'now()';
            }
            if ($default == SQLFunctions::CURRENT_DATE) {
                $this->test->Default = 'curdate()';
            }
            if ($default == SQLFunctions::CURRENT_TIME) {
                $this->test->Default = 'curtime()';
            }
            $this->test->Extra = 'DEFAULT_GENERATED';
        } else {
            $this->Default .= $default;
            $this->test->Default = strtolower($default);
            if (preg_match('/\(.+?\)/', $default) || $default == 'CURRENT_TIMESTAMP' || $default == 'CURRENT_DATE' || $default == 'CURRENT_TIME') {
                $this->Default = "({$default})";
                if ($default == 'CURRENT_TIMESTAMP') {
                    $this->test->Default = 'now()';
                }
                if ($default == 'CURRENT_DATE') {
                    $this->test->Default = 'curdate()';
                }
                if ($default == 'CURRENT_TIME') {
                    $this->test->Default = 'curtime()';
                }
                $this->test->Extra = 'DEFAULT_GENERATED';
            }
        }
        return $this;
    }

    public function after(string $after) {
        $this->After = "AFTER {$after}";
        return $this;
    }
}
