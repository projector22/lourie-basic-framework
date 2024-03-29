<?php

use LBF\App\Config;
use LBF\HTML\HTML;

/**
 * Load general functions which are used all over the script
 * 
 * @todo    Find a more elegant way of autoloading the functions, ideally converting most of them to static methods.
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.1.0
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                      Namespace changed from `Framework` to `LBF`.
 */


/**
 * Convert backslashes, used in Windows paths to forward slashes, used in UNIX type systems.
 * 
 * This normalizes paths for the app. Particularly useful in class autoloading.
 * 
 * @since   LRS 3.15.4
 */

function normalize_path_string(string $path): string {
    return str_replace('\\', '/', $path);
}


/**
 * Protect $_POST or $_GET data from various potential attacks, such as sql injection and XSS
 * 
 * @param   mixed  $data   Some kind of input data and many various kinds
 * 
 * @return  mixed  $data   The cleaned up data
 * 
 * @since   LRS 3.1.0
 * @since   LRS 3.17.0  Added array support
 */

function protect(mixed $data): mixed {
    if (is_null($data)) {
        return $data;
    }
    if (is_array($data)) {
        foreach ($data as $index => $entry) {
            $data[$index] = protect($entry);
        }
    } else {

        /**
         * This removes whitespace and other predefined characters from both sides of a string
         * 
         * @link    https://www.w3schools.com/php/func_string_trim.asp
         */

        $data = trim($data);

        /**
         * This removes backslashes 
         * 
         * @link    https://www.w3schools.com/php/func_string_stripslashes.asp
         */

        $data = stripslashes($data);

        /**
         * The htmlspecialchars() function converts some predefined characters to HTML entities.
         * 
         * The predefined characters are:
         * - & (ampersand) becomes &amp;
         * - " (double quote) becomes &quot;
         * - ' (single quote) becomes &#039;
         * - < (less than) becomes &lt;
         * - > (greater than) becomes &gt;
         * 
         * @link    https://www.w3schools.com/php/func_string_htmlspecialchars.asp
         */

        $data = htmlspecialchars($data, ENT_QUOTES);
    }

    return $data;
}


/**
 * Checks if there is $_POST or $GET named 'token' and if so, return it's content
 * 
 * Order:
 * 1. $_POST
 * 2. $_GET
 * 3. null if no result.
 * 
 * @return  string|null  Token string
 * 
 * @since   LRS 3.1.0
 * @since   LRS 3.15.0  Renamed from setToken() to get_token()
 */

function get_token(): ?string {
    return $_POST['token'] ?? $_GET['token'] ?? null;
}


/**
 * Shortcut for setting a hidden input element named 'token'
 * 
 * Generally interpreted by get_token()
 * 
 * @since   LRS 3.1.0
 * @since   LRS 3.15.0  Renamed from token() to set_token()
 */

function set_token(string $token): void {
    echo "<input type='hidden' name='token' value='{$token}'>";
}


/**
 * Shorcut for adding a ?p= hidden input. Usually used within a form button
 * 
 * @param   string  $page       The page to load
 * @param   boolean $load_pdf   Whether or not to insert the input required for loading a pdf
 * @param   boolean $return     Whether to echo or return
 *                              Default: false
 * 
 * @return  string
 * 
 * @since   LRS 3.8.0
 * @since   LRS 3.14.1  Added param $load_pdf
 * @since   LRS 3.15.4  Added param $return
 */

function page(string $page, bool $load_pdf = false, bool $return = false): string {
    $text = "<input type='hidden' name='p' value='{$page}'>";
    if ($load_pdf) {
        $text .= "<input type='hidden' name='task' value='pdf'>";
    }
    if (!$return) {
        echo $text;
        return '';
    }
    return $text;
}


/**
 * Shortcut for adding a ?t= hidden input. Usually used within a form button
 * 
 * @param   string  $tab    The tab to load
 * @param   boolean $return Whether to echo or return
 *                          Default: false
 * 
 * @return  string
 * 
 * @since   LRS 3.15.4
 */

function tab(string $tab, bool $return = false): string {
    $text = "<input type='hidden' name='t' value='{$tab}'>";
    if ($return) {
        return $text;
    } else {
        echo $text;
    }
}


/**
 * Set the correct page called or the default page as defined
 * 
 * @param   string  $default    The default page to load
 * 
 * @return  string  Page id string
 * 
 * @since   LRS 3.8.5
 */

function load_page(string $default): string {
    if (isset($_GET['p'])) {
        return protect($_GET['p']);
    } else {
        return $default;
    }
}


/**
 * Add a leading number of zeros to a number.
 * 
 * @example add_leading_zero( 5 );        -> returns '05'
 * @example add_leading_zero( 5, 2 );     -> returns '005'
 * @example add_leading_zero( 0, 1 );     -> returns '00'
 * @example add_leading_zero( 12 );       -> returns '12'
 * @example add_leading_zero( '12' );     -> returns '12'
 * @example add_leading_zero( 'cheese' ); -> returns 'cheese'
 * 
 * @param   integer|string  $input          A number or numeric string to have a leading zero attached.
 * @param   integer         $num_of_zeros   The number of leading zeros to add.
 * 
 * @return  string
 * 
 * @since   LRS 3.4.1
 * @since   LRS 3.16.1  Entirely reworked, added param $num_of_zeros.
 */

function add_leading_zero(mixed $input, int $num_of_zeros = 1): string {
    $input = "{$input}";
    if ($num_of_zeros < 1) {
        throw new Exception("You cannot set the parameter '\$num_of_zeros' to less than 1, you set it to {$num_of_zeros}");
    }
    if (!is_numeric($input)) {
        return $input;
    }

    $input_len = strlen($input);
    $zero_string = '0';
    for ($i = 0; $i < $num_of_zeros; $i++) {
        $zero_string .= '0';
    }

    return substr_replace($zero_string, $input, intval(gmp_neg($input_len)), $input_len);
}


/**
 * Remove all of the trailing characters from the end of a string
 * 
 * @param   string  $data   The string to be cleaned up
 * @param   string  $test   The character to be removed
 * 
 * @return  string  $data   Cleaned up string
 * 
 * @since   LRS 3.0.0
 */

function remove_trailing_chars(string $data, string $test): string {
    while (substr($data, -1) == $test) {
        $data = rtrim($data, $test);
    }
    return $data;
}


/**
 * Generate the <option> tags for a <select> droplist
 * 
 * @param   array|object    $list               An array of values to be placed in the droplist
 * @param   array           $value              The desired values, if they are different from the displayed list
 *                                              Default: null
 * @param   string          $selected           Which item is to be preselected as the default value
 *                                              Default: ''
 * @param   array           $hidden_entries     Any items that should be set to hidden. Note, the values of this array should be the 
 *                                              value of the option rather than the innerText  
 *                                              Default: []
 * 
 * @return  string  $option     The complete HTML of <option> elements which can be placed in a <select> or similar
 * 
 * @since   LRS 3.0.0
 * @since   LRS 3.14.3  Added param $hidden, streamlined the logic.
 * @since   LRS 3.17.3  Added the ability to create the based on object properties.
 */

function build_item_droplist(array|object $list, ?array $value = null, string|int|null $selected_option = '', array $hidden_entries = []): string {
    $option = '';
    if (is_object($list)) {
        foreach ($list as $key => $item) {
            $selected = (string)$key == $selected_option ? ' selected' : '';
            $hidden   = in_array($key, $hidden_entries) ? ' hidden' : '';
            $option .= "<option value='{$key}'{$selected}{$hidden}>{$item}</option>";
        }
    } else {
        if (!is_null($value)) {
            $i = 0;
            foreach ($list as $item) {
                $selected = (string)$value[$i] == $selected_option ? ' selected' : '';
                $hidden   = in_array($value[$i], $hidden_entries) ? ' hidden' : '';
                $option .= "<option value='{$value[$i]}'{$selected}{$hidden}>{$item}</option>";
                $i++;
            }
        } else {
            foreach ($list as $i => $item) {
                $selected = (string)$item == $selected_option ? ' selected' : '';
                $hidden   = in_array($item, $hidden_entries) ? ' hidden' : '';
                $option .= "<option value='{$item}'{$selected}{$hidden}>{$item}</option>";
            }
        }
    }
    return $option;
}


/**
 * Get the string in between two strings
 * 
 * @param   string  $string     The full string to be examined
 * @param   string  $start      The starting point within $string
 * @param   string  $end        The end point within $string
 * 
 * @return  string              The string results of the above workings
 * 
 * Credit:
 * @link    https://stackoverflow.com/questions/5696412/how-to-get-a-substring-between-two-strings-in-php
 * 
 * @since   LRS 3.1.0
 */

function get_string_between(string $string, string $start, string $end): string {
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) {
        return '';
    }
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}


/**
 * Get the weeks of the year options for a html select
 * 
 * @param   string  $selected_week  Which week is selected, Default: null.
 *                                  If no week is selected, the current week will be selected.
 * @param   integer $year           Which year is being looked at, Default: null.
 * @param   boolean $add_all        Add an entry for all to the list. Default: false.
 * 
 * @return  string  $options        The html <options> containing the weeks of the year
 * 
 * @since   LRS 3.3.2
 * @since   LRS 3.23.0  Added param $add_all
 */

function week_select(?string $selected_week = null, ?int $year = null, bool $add_all = false): string {
    if (is_null($year)) {
        $year  = date('Y');
    }

    $t = $w = [];

    if ($add_all) {
        $t[] = 'All';
        $w[] = 'all';
    }

    for ($i = 1; $i <= 52; $i++) {
        $date = new \DateTime();
        $date->setISODate($year, $i);
        $t[] = "Week {$i} : {$date->format('jS F')} - {$date->modify('+6 days')->format('jS F')}";
        $w[] = $i;
    }
    if (is_null($selected_week)) {
        $selected_week = date('W');
    }

    return build_item_droplist($t, $w, $selected_week);
}


/**
 * Get the months of the year options for a html select
 * 
 * @param   string  $selected_month Which month is selected, Default: null
 *                                  If no week is selected, the current week will be selected
 * @param   integer $year           Which year is being looked at, Default: null
 * 
 * @return  string  $options        The html <options> containing the weeks of the year
 * 
 * @since   LRS 3.3.2
 */

function month_select(?string $selected_month = null, ?int $year = null): string {
    $months = [
        1  => 'January',
        2  => 'February',
        3  => 'March',
        4  => 'April',
        5  => 'May',
        6  => 'June',
        7  => 'July',
        8  => 'August',
        9  => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    ];
    if (is_null($year)) {
        $year  = date('Y');
    }

    if (is_null($selected_month)) {
        $selected_month = date('n');
    }

    return build_item_droplist($months, array_keys($months), $selected_month);
}


/**
 * Get the path to the PHP executable. The bulk of the logic pertains to a Windows envirnoment as 
 * in a UNIX type environment, php can be invoked simply by calling 'php'
 * 
 * Based on, with the Windows section modified a bit:
 * @link    https://stackoverflow.com/questions/2372624/get-current-php-executable-from-within-script
 * 
 * @return  string  Path to php executable. Returns false if no path detected
 * 
 * @since   LRS 3.4.0
 */

function php_executable_path(): string|bool {
    if (PHP_OS !== 'WINNT') {
        return 'php';
    }
    if (defined('PHP_BINARY') && PHP_BINARY && in_array(PHP_SAPI, array('cli', 'cli-server')) && is_file(PHP_BINARY)) {
        return PHP_BINARY;
    } else if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $paths = explode(';', $_SERVER['PATH']);
        foreach ($paths as $path) {
            if (str_contains($path, 'php')) {
                if (is_file($path . DIRECTORY_SEPARATOR . 'php.exe')) {
                    return $path . DIRECTORY_SEPARATOR . 'php.exe';
                }
            }
        }
    } else {
        $paths = explode(PATH_SEPARATOR, getenv('PATH'));
        foreach ($paths as $path) {
            if (substr($path, strlen($path) - 1) == DIRECTORY_SEPARATOR) {
                $path = substr($path, strlen($path) - 1);
            }
            if (substr($path, strlen($path) - strlen('php')) == 'php') {
                if (is_file($path)) {
                    return $path;
                }
                $response = $path . DIRECTORY_SEPARATOR . 'php';
                if (is_file($response)) {
                    return $response;
                }
            }
        }
    }
    return false;
}


/**
 * Correct slashes when working in the Windows environment
 * 
 * @param   string  $path   The path to be tested
 * 
 * @return  string          The fixed string with slashes correctly oriented
 * 
 * @since   LRS 3.4.0
 */

function correct_win_slashes(string $path): string {
    return str_replace('/', '\\', trim($path));
}


/**
 * Correct slashes if
 * 1. Server is a Windows Server
 * 2. The string parsed is a correctly formatted path, eg. \\192.168.1.1 or C:\path\file.txt
 * 
 * @param   string  $path   Path to be examined.
 * 
 * @return  string
 * 
 * @since   LRS 3.17.0
 */

function correct_path_for_windows(string $path): string {
    if ($path == '') {
        return $path;
    }
    if (PHP_OS === 'WINNT') {
        /**
         * If the path is something like C:\
         */
        if ($path[1] == ':') {
            $path = correct_win_slashes($path);
        }
        /**
         * If the path begins with \\, eg. \\192.168.1.1
         */
        if ($path[0] == '/' && $path[1] == '/') {
            $path = correct_win_slashes($path);
        }
    }
    return $path;
}


/**
 * Execute a script shell or cmd in an asynchronous manor
 * 
 * @param   string  $command    A command to be passed to the shell, default: Null
 * 
 * @link    https://ourcodeworld.com/articles/read/207/how-to-execute-a-shell-command-using-php-without-await-for-the-result-asynchronous-in-linux-and-windows-environments
 * 
 * @since   LRS 3.4.0
 */

function async_execute(?string $command = null): void {
    if (!$command) {
        echo "<pre>";
        throw new Exception("No command given");
        echo "</pre>";
    }
    if (PHP_OS === 'WINNT') {
        system($command . " > NUL");
    } else {
        shell_exec("/usr/bin/nohup " . $command . " >/dev/null 2>&1 &");
    }
}

/**
 * Performs a custom subarray sort. Called without assignment -> order_by( $mydata, 'myfield' );
 * 
 * This does not preserve the origonal index
 * 
 * @param   array   $data       The data to be sorted
 * @param   string  $field      The field to sort by
 * @param   boolean $is_object  If the data to be sorted is an object, if false, assume the data is an array
 *                              Default: false
 * 
 * @return  array   The sorted data
 * 
 * @link    https://www.the-art-of-web.com/php/sortarray/
 * 
 * @since   LRS 3.4.9
 * @since   LRS 3.6.0   Added param $is_object
 */

function order_by(array &$data, string $field, bool $is_object = false): array|object {
    if ($is_object) {
        usort($data, function ($a, $b) use ($field) {
            return strnatcmp(strtoupper($a->$field ?? ''), strtoupper($b->$field ?? ''));
        });
    } else {
        usort($data, function ($a, $b) use ($field) {
            return strnatcmp(strtoupper($a[$field] ?? ''), strtoupper($b[$field] ?? ''));
        });
    }
    return $data;
}


/**
 * Return the SITE_LOGO with the appropriate file extension
 * 
 * @return  string|boolean
 * 
 * @since   LRS 3.6.0
 * 
 * @deprecated  LBF 0.6.0-beta
 */

function site_logo(): string|bool {
    if (!defined("SITE_LOGO")) {
        define("SITE_LOGO", "");
        return false;
    }
    if (file_exists(SITE_LOGO . '.png')) {
        return SITE_LOGO . '.png';
    } else if (file_exists(SITE_LOGO . '.jpg')) {
        return SITE_LOGO . '.jpg';
    } else if (file_exists(SITE_LOGO)) {
        return SITE_LOGO;
    } else {
        return false;
    }
}


/**
 * Returns the relative path from an absolute one. For example /var/www/html/bin/file.jpg returns /bin/file.jpg
 * 
 * @link    https://www.php.net/manual/en/function.realpath.php
 * 
 * @param   string  $relative_path  The path to be converted
 * 
 * @return  string   The relative path as calculated
 * 
 * @since   LRS 3.6.0
 * @since   LRS 3.9.1   Added consideration for windows type paths
 */

function html_path(string $relative_path): string {
    if (PHP_OS === 'WINNT') {
        $realpath = str_replace($_SERVER['DOCUMENT_ROOT'], '', normalize_path_string($relative_path));
        return $realpath;
    } else {
        $realpath = realpath($relative_path);
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $realpath);
    }
}


/**
 * Detect if the device is an iPad or iPhone
 * 
 * @return  boolean     Whether the device is an Apple Mobile device
 * 
 * @since   LRS 3.7.3
 */

function is_apple_mobile(): bool {
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        return (str_contains($_SERVER['HTTP_USER_AGENT'], 'iPhone') ||
            str_contains($_SERVER['HTTP_USER_AGENT'], 'iPad'));
    }
    return false;
}


/**
 * Convert a time value in seconds into minutes and seconds
 * 
 * @param   integer $time   A time value in seconds
 * 
 * @return  array   data with the split time, indexed by 'hour' and 'minute'
 * 
 * @since   LRS 3.9.0
 */

function minute_to_hour_minute(int $time_in_minutes): array {
    $time['hour']    = floor($time_in_minutes / 60);
    $time['minute']  = $time_in_minutes % 60;
    return $time;
}


/**
 * Return a string in the following format '1 hour', '2 hours'
 * 
 * @param   integer $length     The duration on which the string will be based
 * @param   string  $format     The text to follow the time number. Should always be in the singular
 *                              Default: 'hour'
 * 
 * @return  string  The formatted string
 * 
 * @since   LRS 3.9.0
 */

function hour_string(int $length, string $format = 'hour'): string {
    if ($length == 1) {
        return "1 $format";
    } else {
        return "$length {$format}s";
    }
}


/**
 * Return a string in the following format '1 minute', '2 minutes'
 * 
 * @param   integer $length     The duration on which the string will be based
 * @param   string  $format     The text to follow the time number. Should always be in the singular
 *                              Default: 'minute'
 * 
 * @return  string  The formatted string
 * 
 * @since   LRS 3.9.0
 */

function minutes_string(int $length, string $format = 'minute'): string {
    if ($length == 1) {
        return "1 $format";
    } else {
        return "$length {$format}s";
    }
}


/**
 * Validate a date input with the defined date format
 * 
 * @param   string  $date   The date to be checked
 * @param   string  $date   The date format to check $date against
 * 
 * @see     This function comes from the link below. Thanks to the author, (glavic at gmail dot com).
 * @link    https://www.php.net/manual/en/function.checkdate.php
 * 
 * @return  boolean Whether or not the date is valid
 * 
 * @since   LRS 3.9.0
 */

function validate_date(string $date, string $format = 'Y-m-d H:i:s'): bool {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}


/**
 * Attach and ordinal indicator to a set number
 * 
 * @param   integer $number The number which is being acted upon
 * 
 * @return  string  The formulated string
 * 
 * @since   LRS 3.9.0
 */

function add_number_suffix(int $number): string {
    if ($number < 1) {
        return $number;
    }
    switch ($number) {
        case 1:
            return $number . 'st';
        case 2:
            return $number . 'nd';
        case 3:
            return $number . 'rd';
        default:
            return $number . 'th';
    }
}


/**
 * Check if a file exists from a url
 * 
 * @link    https://stackoverflow.com/questions/7684771/how-to-check-if-a-file-exists-from-a-url
 * 
 * @param   string  $url    The link to check
 * 
 * @return  boolean
 * 
 * @since   LRS 3.12.1
 */

function url_file_exists(string $url): bool {
    $headers = get_headers($url);
    return stripos($headers[0], "200 OK") ? true : false;
}


/**
 * Get the tab set
 * 
 * @param   string  $default    The default tab if none is otherwise set
 * 
 * @return  string  The tab
 * 
 * @since   LRS 3.14.3
 */

function get_tab(string $default = ''): string {
    return Config::current_page('tab') ?? $default;
}


/**
 * Prepare a name to be suitable for calling as a method name
 * 
 * @param   string  $method_name    An unformatted method name
 * 
 * @return  string
 * 
 * @since   LRS 3.15.0
 */

function prepare_method_name(string $method_name): string {
    return strtolower(str_replace('-', '_', $method_name));
}


/**
 * Prepare the name of a JS file from a set text, usually from $_GET['p'].
 * Removes any '-' & '_', captitalizes the first letter of each word and remerges.
 * 
 * @param   string  $name   Name to be worked on.
 * 
 * @return  string
 * 
 * @since   LRS 3.22.1
 * @since   LRS 3.24.0  Revamped to function in one line.
 */

function prepare_routed_filename(string $name): string {
    $name = str_replace('_', '-', $name);
    return implode('', array_map('ucfirst', explode('-', $name)));
}


/**
 * Get the entry offences text.
 * 
 * @param   object  $entry          The object being worked on.
 * @param   object  $config         The object of offence names.
 * @param   integer $line_breaks    Number of line breaks to insert between entries.
 *                                  Default: 1
 * 
 * @return  string
 * 
 * @since   LRS 3.17.2
 */

function get_offences_text(object $entry, object $config, int $line_breaks = 1): string {
    $offences_text = [];
    $offence_list = explode(';', $entry->offences);
    foreach ($offence_list as $offence) {
        if (isset($config->data[$offence]->config_value)) {
            $offences_text[] = $config->data[$offence]->config_value;
        }
    }
    $br = '';
    for ($i = 0; $i < $line_breaks; $i++) {
        $br .= '<br>';
    }
    return implode(", {$br}", $offences_text);
}


/**
 * Get the entry sanctions text.
 * 
 * @param   object  $entry          The object being worked on.
 * @param   object  $config         The object of offence names.
 * @param   integer $line_breaks    Number of line breaks to insert between entries.
 *                                  Default: 1
 * 
 * @return  string
 * 
 * @since   LRS 3.17.2
 */

function get_sanctions_text(object $entry, object $config, int $line_breaks = 1): string {
    $sanctions_text = [];
    $sanction_list = explode(';', $entry->sanctions);
    foreach ($sanction_list as $sanction) {
        if (isset($config->data[$sanction]->config_value)) {
            $sanctions_text[] = $config->data[$sanction]->config_value;
        }
    }
    $br = '';
    for ($i = 0; $i < $line_breaks; $i++) {
        $br .= '<br>';
    }
    return implode(", {$br}", $sanctions_text);
}


/**
 * Validate if a year (YYYY) is valid.
 * 
 * @param   string|integer  $year   The year string / int to test
 * 
 * @return  boolean
 * 
 * @since   LRS 3.19.3
 */

function validate_year(string|int $year): bool {
    $date = DateTime::createFromFormat('Y', $year);
    return $date && $date->format('Y') == $year;
}


/**
 * Return the number + the english ordinal suffix to a number
 * 
 * @example 2 => "2nd"
 * @example 33 => "33rd"
 * 
 * @param   integer $num    The number to check.
 * 
 * @return  string
 * 
 * @since   LRS 3.20.0
 */

function number_ordinal_suffix(int $num): string {
    $converted_num = substr((string)$num, -1);
    if ($num == 0) {
        return "0";
    } else if ($num > 10 && $num < 20) {
        return "{$num}th";
    } else if ($converted_num == 1) {
        return "{$num}st";
    } else if ($converted_num == 2) {
        return "{$num}nd";
    } else if ($converted_num == 3) {
        return "{$num}rd";
    } else {
        return "{$num}th";
    }
}

/**
 * Perform an abreviation task on a long description.
 * 
 * @param   string  $outcome            The string to be tested.
 * @param   integer $aprox_max_length   The aprox total length of the text to return.
 *                                      Aprox because the function looks for the nearest space.
 * 
 * @todo    Work on how line breaks should be handled, in all situations.
 * 
 * @return  string
 * 
 * @access  private
 * @since   LRS 3.20.0
 */

function abreviate_outcome(string $outcome, int $aprox_max_length = 100): string {
    if ($outcome == '') {
        return '';
    }
    if (strlen($outcome) < $aprox_max_length) {
        return $outcome;
    }
    $pos = strpos($outcome, ' ', $aprox_max_length);
    if ($pos == false) {
        $pos = $aprox_max_length;
    }
    $abreviated = substr($outcome, 0, $pos);
    $link = HTML::a([
        'href'  => 'javascript::void(0)',
        'text'  => 'View all',
        'title' => $outcome,
        'echo'  => false,
    ]);
    return str_replace("\n", '<br>', $abreviated) . '... ' . $link;
}


/**
 * Compose a URI from an array of key => value params.
 * 
 * @param   array   $fields     The fields to compose into a URI
 * 
 * @return  string
 * 
 * @since   LRS 3.27.1
 */

function uri_compose(array $fields): string {
    $i = 0;
    $uri = '';
    foreach ($fields as $key => $value) {
        if ($i == 0) {
            $uri .= '?';
            $i++;
        } else {
            $uri .= '&';
        }
        $uri .= "{$key}={$value}";
    }
    return $uri;
}


/**
 * Returns the timestamp of the date last modified of the parsed file or
 * an md5 of the file + current time. The purpose of this is for cache validation
 * as any time a file is changed, the timestamp is changed, thus forcing a fresh load.
 * 
 * Would be used something like this:
 * 
 * ```php
 * echo "<iframe src='myFile.jpg?v=" . timestamp_cache_validation( 'myFile.jpg' ) . "'>";
 * ```
 * 
 * @param   string  $file   The full path to the file being parsed.
 * 
 * @return  string  Timestamp or hash
 * 
 * @since   LRS 3.12.1
 * @since   LBF 0.1.3-beta  Removed as a method and generalized here.
 */

function timestamp_cache_validation(string $file): string {
    $timestamp = @filemtime($file);
    if ($timestamp == '') {
        return md5($file . time());
    }
    return $timestamp;
}


/**
 * Function for detecting the luminance of a colour, whether is it light
 * or dark. If true, a dark text colour should be used, if false, a light
 * text colour should be used.
 * 
 * @param   string  $colour     The hex value of a colour, with the # up front.
 * 
 * @return  bool
 * 
 * @since   LBF 0.7.1-beta
 */

function is_light(string $colour): bool {
    $r = hexdec(substr($colour, 1, 2));
    $g = hexdec(substr($colour, 3, 2));
    $b = hexdec(substr($colour, 5, 2));

    // calculate the relative luminance using the formula from the WCAG 2.0 guidelines
    $luminance = (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) / 255;

    return $luminance > 0.5;
}