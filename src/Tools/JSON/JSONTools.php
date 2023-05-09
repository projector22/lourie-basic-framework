<?php

namespace LBF\Tools\JSON;

use Exception;
use LBF\HTML\Draw;

/**
 * Hold and contain the various JSON data within the app.
 * 
 * use LBF\Tools\JSON\JSONTools;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.8.0
 * @since   LRS 3.11.0	Moved to `Framework\Tools\JSONData` from `Tools\JSONData`.
 * @since   LRS 3.14.4  Renamed `JSONTools`.
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                      Namespace changed from `Framework` to `LBF`.
 */

class JSONTools {

    /**
     * Define the correct slash seperator.
     * 
     * @var string DIR_SEP  Either `\` or `/`, depending on the server OS.
     * 
     * @access  public
     * @since   LRS 3.28.0
     */

    const DIR_SEP = PHP_OS === 'WINNT' ? '\\' : '/';

    /**
     * Write a JSON file
     * 
     * @param   string  $file   The file path of the JSON file to write
     * @param   array   $data   The data to be converted to be encoded and written to file
     * 
     * @return  boolean
     * 
     * @access  public
     * @since   LRS 3.8.0
     */

    public static function write_json_file(string $file, array $data): bool {
        try {
            $json = json_encode($data, JSON_PRETTY_PRINT);

            // Write data to file
            $new_config_write = fopen($file, 'w');
            fwrite($new_config_write, $json);
            fclose($new_config_write);
            try {
                @chgrp($file, 'www-data');
                return @chmod($file, 0664);
            } catch (Exception) {
                return false;
            }
        } catch (Exception) {
            return false;
        }
    }


    /**
     * Read a JSON file and return as an array
     * 
     * @param   string  $file   Life to be read.
     * 
     * @return  array   Array of the data read from the JSON
     * 
     * @access  public
     * @since   LRS 3.8.0
     */

    public static function read_json_file_to_array(string $file): array {
        return json_decode(file_get_contents($file), true);
    }


    /**
     * Read parsed JSON data from a $_POST entry.
     * 
     * @param   string  $file   Life to be read.
     * 
     * @return  array   Array of the data read from the JSON
     * 
     * @access  public
     * @since   LRS 3.15.2
     */

    public static function read_json_from_post_to_array(string $field): array {
        return json_decode($_POST[$field], true);
    }


    /**
     * Read a JSON file and return as an object
     * 
     * @param   string  $file   Life to be read.
     * 
     * @return  object   Object of the data read from the JSON
     * 
     * @access  public
     * @since   LRS 3.8.0
     */

    public static function read_json_file_to_object(string $file): object|array {
        return json_decode(file_get_contents($file));
    }


    /**
     * Read parsed JSON data from a $_POST entry.
     * 
     * @return  array   Array of the data read from the JSON
     * 
     * @access  public
     * @since   LRS 3.15.2
     */

    public static function read_json_from_post_to_object(string $field): object|array {
        return json_decode($_POST[$field]);
    }


    /**
     * Check if the parsed file exists and is not empty
     * 
     * @param   string  $file   JSON file to be checked
     * 
     * @return  boolean     Whether or not file exists and isn't empty
     * 
     * @access  public
     * @since   LRS 3.8.0
     */

    public static function check_json_file(string $file): bool {
        return file_exists($file) && file_get_contents($file) !== '';
    }


    /**
     * Handle the creation of any listed json array
     * 
     * @param   array   $json   An array in the following form:
     *                          |      Key       |    Value    |
     *                          | ============== | =========== |
     *                          | json file path | json string |
     * @param   boolean $draw_text_feedback Default: false
     * 
     * @access  public
     * @since   LRS 3.14.4
     */

    public static function create_listed_json_files(array $json, bool $draw_text_feedback = false): void {
        foreach ($json as $file => $data) {
            if (!file_exists($file)) {
                self::write_json_file($file, $data);
                if ($draw_text_feedback) {
                    $name = explode('/', $file)[array_key_last(explode('/', $file))];
                    echo "Creating {$name} ...";
                    Draw::lines(1);
                }
            }
        }
    }


    /**
     * Check through the listed JSON files and apply any missing values, 
     * without overwriting any already set values
     * 
     * @param   array   $json   An array in the following form:
     *                          |      Key       |    Value    |
     *                          | ============== | =========== |
     *                          | json file path | json string |
     * 
     * @access  public
     * @since   LRS 3.14.4
     */

    public static function check_listed_json_content(array $json): void {
        foreach ($json as $file => $data) {
            $set_data = self::read_json_file_to_array($file);
            $hold_data = self::check_values($data, $set_data);
            $file_name = explode(self::DIR_SEP, $file);
            echo "Checking {$file_name[array_key_last($file_name)]} ...";
            Draw::lines(1);
            self::write_json_file($file, $hold_data);
        }
    }


    /**
     * Recursively check the live data keys against the master data keys
     * and return the differences
     * 
     * @param   array   $master_data    The default data to be checked
     * @param   array   $live_data      The current data
     * 
     * @return  array
     * 
     * @access  public
     * @since   LRS 3.14.4
     */

    public static function check_values(array $master_data, array $live_data): array {
        $hold = $live_data;
        foreach ($master_data as $index => $value) {
            if (is_array($value)) {
                if (isset($live_data[$index])) {
                    $hold[$index] = self::check_values($master_data[$index], $live_data[$index]);
                } else {
                    $hold[$index] = $master_data[$index];
                }
            } else {
                $hold[$index] = $live_data[$index] ?? $master_data[$index];
            }
        }
        return $hold;
    }
}
