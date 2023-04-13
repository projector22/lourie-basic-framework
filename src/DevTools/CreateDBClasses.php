<?php

namespace LBF\DevTools;

use Exception;
use LBF\Db\ConnectMySQL;
use LBF\Tools\Files\FileSystem;

/**
 * This class handles the creation of php class files.
 * 
 * use LBF\DevTools\CreateDBClasses;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.19.0
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                      Namespace changed from `Framework` to `LBF`.
 */

class CreateDBClasses extends ConnectMySQL {

    /**
     * The path to the template for the 'table' file.
     * 
     * @var string  TABLE_TEMPLATE
     * 
     * @access  public
     * @since   LRS 3.19.0
     */

    const TABLE_TEMPLATE = __DIR__ . '/TableTemplate.txt';

    /**
     * The path to the template for the 'table' file.
     * 
     * @var string  DATA_TEMPLATE
     * 
     * @access  public
     * @since   LRS 3.19.0
     */

    const DATA_TEMPLATE = __DIR__ . '/DataTemplate.txt';


    /**
     * The current version of the project
     * 
     * @var string  $version
     * 
     * @access  private
     * @since   LRS 3.19.0
     */

    private string $version = '';


    /**
     * Class constructor - sets $this->version.
     * 
     * @access  public
     * @since   LRS 3.19.0
     */

    public function __construct(
        /**
         * The path of the app. In LRS this is parsed as APP_PATH
         * 
         * @var string  $app_path
         * 
         * @readonly
         * @access  private
         * @since   LRS 3.28.0
         */
        private readonly string $app_path
    ) {
        if (defined('APP_VERSION')) {
            $this->version = APP_VERSION;
        }
        $this->conn = $this->connect_db();
    }


    /**
     * Prepare and generate the path names for files about to be created. Do some basic checking.
     * 
     * @param   boolean $return Whether or not to return the names.
     *                          Default: false
     * 
     * @return  bool|array
     * 
     * @access  public
     * @since   LRS 3.19.0
     */

    public function prepare_table_php_class_creation(bool $return = false): bool|array {
        /**
         * String cleaning of the table name as required.
         * 
         * @since   LRS 3.19.0
         */
        $clean_up_file_name = function ($text, $replace) {
            $parts = explode($replace, $text);
            foreach ($parts as $i => $part) {
                $parts[$i] = ucfirst($part);
            }
            return implode('', $parts);
        };

        $_POST['table'] = protect($_POST['table']);

        if ($_POST['overwrite_table_name']) {
            $table_name = ucfirst($_POST['new_name']);
            $table_name = $clean_up_file_name($table_name, ' ');
            $table_name = $clean_up_file_name($table_name, '_');
        } else {
            $table_name = str_replace(getenv('TABLE_PREFIX'), '', $_POST['table']);
            $table_name = $clean_up_file_name($table_name, '_');
        }

        $tables_file = normalize_path_string($this->app_path . 'db\\tables\\' . $table_name . '.php');
        $data_file   = normalize_path_string($this->app_path . 'db\\data\\'   . $table_name . 'Data.php');
        $namespace   = 'App\\Db\\Tables\\' . $table_name;
        if (file_exists($tables_file) || file_exists($data_file)) {
            echo "You cannot continue!<br>";
            if (file_exists($tables_file)) {
                echo "The file <b>{$tables_file}</b> already exists.<br>";
            }
            if (file_exists($data_file)) {
                echo "The file <b>{$data_file}</b> already exists.<br>";
            }
            return false;
        }

        $constants = get_defined_constants(true);
        foreach ($constants['user'] as $name => $value) {
            if ($value == $_POST['table']) {
                $table_constant = $name;
            }
        }
        if (!isset($table_constant)) {
            echo "You cannot continue!<br>";
            echo "You have not yet defined a table constant for this table in src\\includes\\tables.php";
            return false;
        }
        if ($return) {
            return [
                'table' => [
                    'path'  => $tables_file,
                    'class' => $table_name,
                ],
                'data' => [
                    'path'  => $data_file,
                    'class' => "{$table_name}Data",
                ],
                'table_name'      => $_POST['table'],
                'table_name_anon' => str_replace(getenv('TABLE_PREFIX'), 'TABLE_PREFIX', $_POST['table']),
                'constant'        => $table_constant,
                'namespace'       => $namespace,
            ];
        } else {
            echo "<h2>Preparations Successful</h2>";
            echo "The following files are about to be created:<br>";
            echo "<b>{$tables_file}</b><br>";
            echo "<b>{$data_file}</b><br>";
            return true;
        }
    }


    /**
     * Execute the creation of the desired files.
     * 
     * @access  public
     * @since   LRS 3.19.0
     */

    public function execute_table_php_class_creation() {
        $files = $this->prepare_table_php_class_creation(true);
        if (!$files) {
            return;
        }

        $f1 = FileSystem::write_file(
            file_path: $files['table']['path'],
            contents: $this->get_table_content($files),
        );

        $f2 = FileSystem::write_file(
            file_path: $files['data']['path'],
            contents: $this->get_data_content($files),
        );

        if ($f1 && $f2) {
            echo "<h2>File Creation Successful</h2>";
            echo "File: <b>{$files['table']['path']}</b> successfully created.<br><br>";
            echo "File: <b>{$files['data']['path']}</b> successfully created.<br><br>";
        } else {
            echo "<h2>File Creation Failed</h2>";
            if (!$f1) {
                echo "File: <b>{$files['table']['path']}</b> could not be created.<br><br>";
            }
            if (!$f2) {
                echo "File: <b>{$files['data']['path']}</b> could not be created.<br><br>";
            }
        }
    }


    /**
     * Generate the 'Table' file.
     * 
     * @param   array   $files  The files array containing the info needed to generate the file.
     * 
     * @return  string
     * 
     * @access  private
     * @since   LRS 3.19.0
     */

    private function get_table_content(array $files): string {
        $table_template = file_get_contents(self::TABLE_TEMPLATE);
        $table_template = str_replace('<<CLASS_TABLE_NAME>>', $files['table']['class'], $table_template);
        $table_template = str_replace('<<CURRENT_VERSION>>', $this->version, $table_template);
        $table_template = str_replace('<<ORIGONAL_TABLE_NAME>>', $files['table_name_anon'], $table_template);

        $table_schema = $this->get_table_columns_schemas($files['table_name']);
        $properties = "\n";
        foreach ($table_schema[$files['table_name']] as $column => $data) {
            $property = "    /**
     * @var\tstring";
            if ($data->IS_NULLABLE == 'YES') {
                $property .= '|null';
            }
            $property .= "\t\${$column}\t\tFrom table {$files['table_name_anon']}.
     * 
     * @type\t\t{$data->DATA_TYPE}\n";
            if (!is_null($data->CHARACTER_MAXIMUM_LENGTH)) {
                $property .= "     * @max-length\t{$data->CHARACTER_MAXIMUM_LENGTH}\n";
            }
            if (!is_null($data->COLUMN_DEFAULT)) {
                $property .= "     * @default\t\t{$data->COLUMN_DEFAULT}\n";
            }
            if ($data->COLUMN_KEY == 'PRI') {
                $property .= "     * @primary-key\n";
            }
            if ($data->COLUMN_KEY == 'UNI') {
                $property .= "     * @unique\n";
            }
            if ($data->EXTRA == 'auto_increment') {
                $property .= "     * @auto-increment\n";
            }
            $property .= "     * 
     * @access  public
     * @since   {$this->version}
     */

    public ";
            if ($data->IS_NULLABLE == 'YES') {
                $property .= '?';
            }
            $property .= "string \${$column};\n\n";
            $properties .= $property;
        }
        $table_template = str_replace('<<CLASS_PROPERTIES>>', $properties, $table_template);
        return $table_template;
    }


    /**
     * Generate the 'Data' file.
     * 
     * @param   array   $files  The files array containing the info needed to generate the file.
     * 
     * @return  string
     * 
     * @access  private
     * @since   LRS 3.19.0
     */

    private function get_data_content(array $files): string {
        $data_template = file_get_contents(self::DATA_TEMPLATE);
        $data_template = str_replace('<<CLASS_TABLE_NAME>>', $files['data']['class'], $data_template);
        $data_template = str_replace('<<CURRENT_VERSION>>', $this->version, $data_template);
        $data_template = str_replace('<<ORIGONAL_TABLE_NAME>>', $files['table_name_anon'], $data_template);
        $data_template = str_replace('<<TABLE_TEMPLATE_CLASS>>', $files['table']['class'], $data_template);
        $data_template = str_replace('<<TABLE_NAME_SHORTCUT>>', $files['constant'], $data_template);
        $data_template = str_replace('<<TEMPLATE_CLASS_NAME>>', $files['namespace'], $data_template);

        $table_schema = $this->get_table_columns_schemas($files['table_name']);

        $keys = [];
        $can_be_hidden   = 'false';
        $can_be_archived = 'false';
        $can_be_deleted  = 'false';
        foreach ($table_schema[$files['table_name']] as $column => $data) {
            if ($data->COLUMN_KEY == 'PRI') {
                $primary_key = $column;
                $keys[] = "'{$column}'";
            } else if ($data->COLUMN_KEY == 'UNI') {
                $keys[] = "'{$column}'";
            }
            if ($column == 'is_hidden') {
                $can_be_hidden = 'true';
            }
            if ($column == 'is_archived') {
                $can_be_archived = 'true';
            }
            if ($column == 'is_deleted') {
                $can_be_deleted = 'true';
            }
        }
        $keys = implode(', ', $keys);
        $data_template = str_replace('<<PRIMARY_KEY>>', $primary_key, $data_template);
        $data_template = str_replace('<<UNIQUE_VALUES>>', $keys, $data_template);
        $data_template = str_replace('<<CAN_BE_HIDDEN>>', $can_be_hidden, $data_template);
        $data_template = str_replace('<<CAN_BE_ARCHIVED>>', $can_be_archived, $data_template);
        $data_template = str_replace('<<CAN_BE_DELETED>>', $can_be_deleted, $data_template);
        return $data_template;
    }
}
