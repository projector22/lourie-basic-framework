<?php

namespace LBF\Db;

use Exception;
use LBF\Db\FieldAssets\PrimaryKey;
use LBF\Db\FieldAssets\SQLFunctions;
use LBF\HTML\Draw;
use stdClass;

/**
 * General methods of interacting with the database
 * 
 * use LBF\Db\Field;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.7.0-beta
 */

class Field {

    /**
     * A means of assigning the default value as an empty string.
     * 
     * @var string  EMPTY_STRING
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public const EMPTY_STRING = '""';

    /**
     * The field name of the field.
     * 
     * @var string  $Field
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public string $Field;

    /**
     * The field type of the field.
     * 
     * @var string  $Type
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public string $Type;

    /**
     * Whether or not the field can be null.
     * 
     * @var string  $Null
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public string $Null;

    /**
     * If a key, what type of key (primary, unique).
     * 
     * @var string  $Key
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public string $Key;

    /**
     * The default value of the field.
     * 
     * @var string  $Default
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public string $Default;

    /**
     * Any extra data of the field.
     * 
     * @var string  $Extra
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public string $Extra;

    /**
     * A way of positioning the field in the table.
     * 
     * @var string  $After
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public string $After;

    /**
     * The stdClass which contains the test values.
     * 
     * @var stdClass    $test
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public stdClass $test;


    /**
     * Class constructor, sets all the defaults and the test data.
     * 
     * @param   string  $field_name Assigned to `$this->Field`.
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

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


    /**
     * Static class costructor - sets up the entire object.
     * 
     * @param   string  $field_name Assigned to `$this->Field`.
     * 
     * @return  Field
     * 
     * @static
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public static function name(string $field_name): Field {
        return new Field($field_name);
    }


    /**
     * Generates the actual SQL for creating the field on the table.
     * 
     * Use something like this: `ALTER TABLE {$table} ADD {$column->generate()}`
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

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


    /**
     * Validates if a stdObject from the database is the same as what has been constructed by this object.
     * 
     * @param   stdClass    $object This is generated from running `"SHOW COLUMNS FROM {$table};"`.
     * @param   bool        $debug  Default: false. If true it'll give more feedback if there is a mismatch.
     * 
     * @return  bool
     * 
     * @access  public
     * @since   LBF 0.4.0-beta
     */

    public function validate(stdClass $object, bool $debug = false): bool {
        if ($debug) {
            if ($object != $this->test) {
                Draw::line_separator();
                Draw::print_red("ON DATABASE");
                print_r($object);
                Draw::print_red("ON STRUCTURE");
                print_r($this->test);
                Draw::line_separator();
            }
        }
        return $object == $this->test;
    }


    /**
     * Set the primary key of the table.
     * 
     * @param   PrimaryKey  $type       Default: PrimaryKey::AUTO_INT (auto incriment int)
     * @param   int         $txt_length Default: 10, When creating a random txt id, set the length.
     * 
     * @return  static
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public function primary_key(PrimaryKey $type = PrimaryKey::AUTO_INT, int $txt_length = 10): static {
        switch ($type) {
            case PrimaryKey::AUTO_INT:
                $this->Type = 'INT';
                $this->Extra = 'auto_increment';
                $this->test->Extra = 'auto_increment';
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


    /**
     * Set the type of the field (`VARCHAR`, `INT`, `DATETIME` etc.).
     * 
     * @param   string      $type       The actual type of the field.
     * @param   int|null    $limit      The limit of the field (as in `VARCHAR(50)`). Default: null
     * @param   int|null    $decimal    When dealing with decimal numbers, set the number of decimal places.
     * 
     * @return  static
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

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


    /**
     * Prevent the field from being null be default.
     * 
     * @return  static
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public function not_null(): static {
        $this->Null = 'NOT NULL';
        $this->test->Null = 'NO';
        return $this;
    }


    /**
     * Set the default value to null.
     * 
     * @return  static
     * 
     * @throws  Exception
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public function null(): static {
        if ($this->Null === 'NOT NULL') {
            throw new Exception("Cannot set the default value as Null, when Not Null is set.");
        }
        $this->test->Null = 'YES';
        return $this;
    }


    /**
     * Indicate that the field should be unique.
     * 
     * @return  static
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public function unique(): static {
        $this->Extra = 'UNIQUE';
        $this->test->Key = 'UNI';
        return $this;
    }


    /**
     * Set the default value as desired.
     * 
     * @param   int|string|SQLFunctions $default    The default value. Either a text value or and SQLFunctions
     *                                              enum to set a function as the default value (like `NOW()`
     *                                              or `UNIX_TIMESTAMP()`).
     * @param   string|null             $fn_value   If using a SQLFunctions, you can set a value as a param of
     *                                              that function.
     * 
     * @return  static
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

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
            if ($default == self::EMPTY_STRING) {
                $this->test->Default = '';
            }
        }
        return $this;
    }


    /**
     * Add the `AFTER` clause for positioning the adding of a column to the table.
     * 
     * @param   string  $after  The field to place the column after
     * 
     * @return  static
     * 
     * @access  public
     * @since   LBF 0.7.0-beta
     */

    public function after(string $after): static {
        $this->After = "AFTER {$after}";
        return $this;
    }
}
