<?php

namespace LBF\Tools\Cron;

use App\Db\Data\CronEntriesData;
use LBF\HTML\Draw;
use LBF\Tools\Files\FileSystem;

/**
 * Handle various cron instructions.
 * 
 * use LBF\Tools\Cron\CronHandler;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.17.4
 * @since   3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                  Namespace changed from `Framework` to `LBF`.
 * 
 * @todo    REVAMP, Needs a complete overhall, but not just at the moment. Do not use outside of LRS for the moment.
 */

class CronHandler {

    /**
     * ENUM record to indicate WINDOWS
     * 
     * @var integer WINDOWS
     * 
     * @access  public
     * @since   3.4.0
     * @since   3.17.4  Revamped and seperated out the App aspects.
     */

    const WINDOWS = 0;

    /**
     * ENUM record to indicate UNIX
     * 
     * @var integer UNIX
     * 
     * @access  public
     * @since   3.17.4
     */

    const UNIX = 1;

    /**
     * Which environment is being operated in, Windows or a UNIX type environment.
     * 
     * @var int $environment    Choice of self::WINDOWS or self::UNIX
     * 
     * @access  protected
     * @since   3.17.4
     */

    protected int $environment;

    /**
     * Days of the week in the correct format.
     * 
     * @var array   WEEK_DAYS
     * 
     * @access  public
     * @since   3.17.4
     */

    const WEEK_DAYS = [
        1 => 'MON',
        2 => 'TUE',
        3 => 'WED',
        4 => 'THU',
        5 => 'FRI',
        6 => 'SAT',
        7 => 'SUN',
    ];

    /**
     * Days of the month in the correct format.
     * 
     * @var array   MONTH_DAYS
     * 
     * @access  public
     * @since   3.17.4
     */

    const MONTH_DAYS = [
        1  => '1st',
        2  => '2nd',
        3  => '3rd',
        4  => '4th',
        5  => '5th',
        6  => '6th',
        7  => '7th',
        8  => '8th',
        9  => '9th',
        10 => '10th',
        11 => '11th',
        12 => '12th',
        13 => '13th',
        14 => '14th',
        15 => '15th',
        16 => '16th',
        17 => '17th',
        18 => '18th',
        19 => '19th',
        20 => '20th',
        21 => '21st',
        22 => '22nd',
        23 => '23rd',
        24 => '24th',
        25 => '25th',
        26 => '26th',
        27 => '27th',
        28 => '28th',
        29 => '29th',
        30 => '30th',
        31 => '31st',
    ];

    /**
     * The Windows scheduled task template for shell execution
     * 
     * /f suppresses any duplication questions
     * 
     * @var string  WINDOWS_TASK_TEMPLATE
     * 
     * @access  public
     * @since   3.17.4
     */

    const WINDOWS_TASK_TEMPLATE = "schtasks /create /tn %event_name% /tr %bat_file% /f /sc %interval%";

    /**
     * Different Windows scheduled tasks template options for various time based tasks
     * 
     * @var array   WINDOWS_SCHEDULE_OPTIONS
     * 
     * @access  public
     * @since   3.17.4
     */

    const WINDOWS_SCHEDULE_OPTIONS = [
        'MINUTE'  => ' /mo %min%',
        'HOURLY'  => ' /mo %hour%',
        'DAILY'   => ' /mo %day%',
        'WEEKLY'  => ' /mo %week% /d %week_day%',
        'MONTHLY' => ' /mo %month% /d %month_day%',
    ];

    /**
     * The Unix scheduled task template for shell execution
     * 
     * @access  public
     * @since   3.17.4
     */

    const UNIX_TASK_TEMPLATE = '(crontab -l ; echo "%interval% bash %sh_file%") | sort - | uniq - | crontab - 2>&1';

    /**
     * Different Unix Cron type template options for various time based tasks
     * 
     * @var array   UNIX_SCHEDULE_OPTIONS
     * 
     * @access  public
     * @since   3.17.4
     */

    const UNIX_SCHEDULE_OPTIONS = [
        'MINUTE'  => '%min% * * * *',
        'HOURLY'  => '0 %hour% * * *',
        'DAILY'   => '%min% %hour% */%day% * *',
        'WEEKLY'  => '%min% %hour% * * %week_day% expr `date +\%s` / 604800 \% %week% >/dev/null || ',
        'MONTHLY' => '%min% %hour% %month_day% */ %month% *',
    ];

    /**
     * The defined name of the cron / scheduled task
     * 
     * @var string  $cron_name
     * 
     * @access  public
     * @since   3.17.4
     */
    
    public string $cron_name;

    /**
     * The template token to apply to the cron.
     * 
     * @var string  $template
     * 
     * @access  public
     * @since   3.17.4
     */

    public string $template;

    /**
     * The email address to send it to.
     * 
     * @var string  $send_to_email
     * 
     * @access  public
     * @since   3.17.4
     */

    public string $send_to_email;

    /**
     * The start time for the cron.
     * 
     * @var string  $start_time
     * 
     * @access  public
     * @since   3.17.4
     */

    public string $start_time;

    /**
     * Content of the php file.
     * 
     * @var string  $php_file_content;
     * 
     * @access  public
     * @since   3.17.4
     */

    protected string $php_file_content;

    /**
     * A string to use when a scheduled task or cron has successfully been created
     * 
     * @var string  $scheduled_success
     * 
     * @access  protected
     * @since   3.4.0
     */

    protected string $scheduled_success = "The scheduled task has successfully been created";

    /**
     * A string to use when a scheduled task or cron has failed to be created
     * 
     * @var string  $scheduled_failed
     * 
     * @access  protected
     * @since   3.4.0
     */

    protected string $scheduled_failed = "Scheduled task creation failed, please run the following into CMD on <b>THE SERVER</b>";

    /**
     * A string to use when a scheduled task or cron has failed to be deleted
     * 
     * @var string  $scheduled_delete_failed
     * 
     * @access  protected
     * @since   3.4.0
     */

    protected string $scheduled_delete_failed = "Schedule deletion failed, please run the following into CMD on <b>THE SERVER</b>";

    /**
     * Send an email in response to the cron's execution.
     * Set to false, if emailing is built into the action function, for example, rather than the cron.
     * 
     * @var boolean $send_email Default: false
     * 
     * @access  public
     * @since   3.17.4
     */

    public bool $send_email = true;


    /**
     * Constructor method, things to do when the class is loaded
     * 
     * - Sets $this->environment.
     * - Tests the PHP CLI interface.
     * 
     * @access  public
     * @since   3.17.4
     */

    public function __construct() {
        $this->environment = PHP_OS === 'WINNT' ? self::WINDOWS : self::UNIX;
        $this->test_php_cli();
    }


    /**
     * Construct the rest of the template.
     * 
     * @param   string  $interval   The interval between cron executions.
     * @param   string  $schedule   The schedule type index.
     * 
     * @return  string
     * 
     * @todo    TEST ON LINUX!!
     * 
     * @access  private
     * @since   3.17.4
     */

    private function fill_in_template( string $interval, string $schedule ): string {
        $template = $this->environment == self::WINDOWS ? self::WINDOWS_TASK_TEMPLATE : self::UNIX_TASK_TEMPLATE;
        if ( $this->environment == self::WINDOWS ) {
            $template = str_replace( '%interval%', $schedule, $template ) . $interval;
        }
        $template = str_replace( '%bat_file%', correct_win_slashes( CRON_BAT_PATH . "{$this->cron_name}.bat" ), $template );
        $template = str_replace( '%event_name%', $this->cron_name, $template );
        return $template;
    }


    /**
     * Construct the cmdlet string for executing every x minutes.
     * 
     * @param   string  $schedule   The schedule type index.
     * @param   string  $minute     How many minutes between executions.
     * 
     * @return  string
     * 
     * @access  protected
     * @since   3.17.4
     */

    protected function contruct_minutely(
        string $schedule,
        string $minute
    ): string {
        $text = $this->environment == self::WINDOWS ? self::WINDOWS_SCHEDULE_OPTIONS[$schedule] : self::UNIX_SCHEDULE_OPTIONS[$schedule];
        $text = str_replace( '%min%', $minute, $text );
        return $this->fill_in_template( $text, $schedule );
    }


    /**
     * Construct the cmdlet string for executing every x hours.
     * 
     * @param   string  $schedule   The schedule type index.
     * @param   string  $hour       How many hours between executions.
     * 
     * @return  string
     * 
     * @access  protected
     * @since   3.17.4
     */

    protected function contruct_hourly(
        string $schedule,
        string $hour
    ): string {
        $text = $this->environment == self::WINDOWS ? self::WINDOWS_SCHEDULE_OPTIONS[$schedule] : self::UNIX_SCHEDULE_OPTIONS[$schedule];
        $text = str_replace( '%hour%', $hour, $text );
        return $this->fill_in_template( $text, $schedule );
    }


    /**
     * Construct the cmdlet string for executing every x days.
     * 
     * @param   string      $schedule   The schedule type index.
     * @param   string      $day        How many days between executions.
     * @param   int|null    $min        In the UNIX context, how many minutes between executions.
     * @param   int|null    $hour       In the UNIX context, how many hours between executions.
     * 
     * @return  string
     * 
     * @access  protected
     * @since   3.17.4
     */

    protected function construct_daily(
        string $schedule,
        string $day,
        ?int $min  = null,
        ?int $hour = null
    ): string {
        $text = $this->environment == self::WINDOWS ? self::WINDOWS_SCHEDULE_OPTIONS[$schedule] : self::UNIX_SCHEDULE_OPTIONS[$schedule];
        $string = str_replace( '%day%', $day, $text );
        if ( !is_null( $min ) ) {
            $string = str_replace( '%min%', $min, $string );
        }
        if ( !is_null( $hour ) ) {
            $string = str_replace( '%hour%', $hour, $string );
        }
        return $this->fill_in_template( $string, $schedule );
    }


    /**
     * Construct the cmdlet string for executing every x weeks
     * 
     * @param   string      $schedule   The schedule type index.
     * @param   string      $week       How many weeks between executions.
     * @param   string      $week_day   On which day of the week to execute.
     * @param   int|null    $min        In the UNIX context, how many minutes between executions.
     * @param   int|null    $hour       In the UNIX context, how many hours between executions.
     * 
     * @return  string
     * 
     * @access  protected
     * @since   3.17.4
     */

    protected function construct_weekly(
        string $schedule,
        string $week,
        string $week_day,
        ?int $min  = null,
        ?int $hour = null
    ): string {
        $text = $this->environment == self::WINDOWS ? self::WINDOWS_SCHEDULE_OPTIONS[$schedule] : self::UNIX_SCHEDULE_OPTIONS[$schedule];
        $string = str_replace( '%week%', $week, $text );
        $string = str_replace( '%week_day%', $week_day, $string );
        if ( !is_null( $min ) ) {
            $string = str_replace( '%min%', $min, $string );
        }
        if ( !is_null( $hour ) ) {
            $string = str_replace( '%hour%', $hour, $string );
        }
        return $this->fill_in_template( $string, $schedule );
    }


    /**
     * Construct the cmdlet string for executing every x months.
     * 
     * @param   string      $schedule   The schedule type index.
     * @param   string      $month      How many months between executions.
     * @param   int         $month_day  Which day of the month to execute.
     * @param   int|null    $min        In the UNIX context, how many minutes between executions.
     * @param   int|null    $hour       In the UNIX context, how many hours between executions.
     * 
     * @return  string
     * 
     * @access  protected
     * @since   3.17.4
     */

    protected function construct_monthly(
        string $schedule,
        string $month,
        int $month_day,
        ?int $min  = null,
        ?int $hour = null
    ): string {
        $text = $this->environment == self::WINDOWS ? self::WINDOWS_SCHEDULE_OPTIONS[$schedule] : self::UNIX_SCHEDULE_OPTIONS[$schedule];
        $string = str_replace( '%month%', $month, $text );
        $string = str_replace( '%month_day%', $month_day, $string );
        if ( !is_null( $min ) ) {
            $string = str_replace( '%min%', $min, $string );
        }
        if ( !is_null( $hour ) ) {
            $string = str_replace( '%hour%', $hour, $string );
        }
        return $this->fill_in_template( $string, $schedule );
    }


    /**
     * Create the text of the cron php file.
     * 
     * @access  protected
     * @since   3.17.4
     */

    protected function create_php_template(): void {
        $this->php_file_content = "<?php";
        $this->php_file_content .= "\n\$ts = date( 'Y-m-d G:i:s' );";
        $this->php_file_content .= "\necho \"\\n[{\$ts}] - CRON STARTED:: {$this->cron_name}\\n\";";
        $this->php_file_content .= "\n\$start_time = microtime( true );";
        $this->php_file_content .= "\nif ( isset( \$_SERVER['REMOTE_ADDR'] ) ) {";
        $this->php_file_content .= "\n    die( 'Permission denied. You may not run this file directly' );";
        $this->php_file_content .= "\n}";
        $this->php_file_content .= "\nrequire '" . INCLUDES_PATH . "general-loader.php';";
        $this->php_file_content .= "\n\$cron = new App\Actions\CronActions;";
        $this->php_file_content .= "\n\$cron->token = '{$this->template}';";
        if ( isset( $this->send_to_email ) ) {
            $this->php_file_content .= "\n\$cron->email = '{$this->send_to_email}';";
        }
        $this->php_file_content .= "\n\$cron->execute();";
        if ( $this->send_email && isset( $this->send_to_email ) ) {
            $this->php_file_content .= "\n\$mail = new LBF\Tools\Mail\Mail;";
            $this->php_file_content .= "\n\$mail->send_mail( ";
            $this->php_file_content .= "\n    '{$this->send_to_email}',";
            $this->php_file_content .= "\n    \$cron->subject,";
            $this->php_file_content .= "\n    \$cron->body,";
            $this->php_file_content .= "\n    \$cron->attachment ?? null,";
            $this->php_file_content .= "\n    APP_NAME,";
            $this->php_file_content .= "\n    true";
            $this->php_file_content .= "\n);";
            $this->php_file_content .= "\nif ( isset( \$cron->attachment[0] ) && is_file( \$cron->attachment[0] ) ) {";
            $this->php_file_content .= "\n    unlink( \$cron->attachment[0] );";
            $this->php_file_content .= "\n}";
        }
        $this->php_file_content .= "\n\$end_time = microtime( true );";
        $this->php_file_content .= "\n\$ts = date( 'Y-m-d G:i:s' );";
        $this->php_file_content .= "\necho \"\\n[{\$ts}] - CRON COMPLETED:: The code took \" . (\$end_time - \$start_time) . \" seconds to complete. It completed successfully.\\n\\n\";";
    }


    /**
     * Add the cron entry to the database
     * 
     * @param   string      $schedule   The schedule that will be applied.
     *                                  Options are:
     *                                  - 'MINUTE'
     *                                  - 'HOURLY'
     *                                  - 'DAILY'
     *                                  - 'WEEKLY'
     *                                  - 'MONTHLY'
     * @param   string|int  $condition  The basic modifier, every x days or every x hours etc.
     * 
     * @access  public
     * @since   3.17.4
     */

    public function add_to_database( string $schedule, string $condition ): void {
        $cron_id = "{$this->template} . {$schedule} . " . date( 'Y-M-d' );
        $cron_data = new CronEntriesData;
        $cron_data->select_all( "cron_id LIKE '%{$cron_id}%'" );

        if ( $cron_data->number_of_records > 0 ) {
            $cron_id .= ' - ' . $cron_data->number_of_records;
        }

        $cron_data->insert( [
            'cron_id'      => $cron_id,
            'date_set'     => date( 'Y-m-d G:i:s' ),
            'event_name'   => $this->cron_name,
            'template'     => $this->template,
            'send_to_addr' => $this->send_to_email ?? null,
            'rec_schedule' => $schedule,
            'conditions'   => $condition,
            'start_time'   => $this->start_time ?? null,
        ] );
    }


    /**
     * Delete a Cron job or scheduled task.
     * 
     * @param   string  $cron_name  The name of the cron to be deleted
     * 
     * @return  boolean
     * 
     * @access  public
     * @since   3.4.0
     */

    public function delete_cron( string $cron_name ): bool {
        $cron_name = $this->event_name_convention( $cron_name );
        $success = true;
        switch ( $this->environment ) {
            case self::WINDOWS:
                $bat_file = correct_win_slashes( CRON_BAT_PATH . "{$cron_name}.bat" );
                $php_file = correct_win_slashes( CRON_PHP_PATH . "{$cron_name}.php" );
                if ( file_exists( $bat_file ) && !unlink( $bat_file ) ) {
                    $success = false;
                }
                if ( file_exists( $php_file ) && !unlink( $php_file ) ) {
                    $success = false;
                }

                $cmd = "schtasks /delete /tn {$cron_name} /f";
                $execute = shell_exec( $cmd . ' 2>&1' );

                echo "<span style='text-align:left'>";
                if ( !str_contains( $execute, 'SUCCESS' ) ) {
                    /**
                     * @todo    Check on 'run as' user and whether it should run without someone logged on, NB
                     * 
                     * @since   3.10.1
                     */
                    echo $this->scheduled_delete_failed . "<br>";
                    Draw::copy_text_textbox( $cmd, 'sc_d' );
                    $success = false;
                }
                echo "</span>";
                break;
            case self::UNIX:
                $sh_file  = CRON_SH_PATH  . "{$cron_name}.sh";
                $php_file = CRON_PHP_PATH . "{$cron_name}.php";
                if ( file_exists( $php_file ) && !unlink( $php_file ) ) {
                    $success = false;
                }
                if ( file_exists( $sh_file ) && !unlink( $sh_file ) ) {
                    $success = false;
                }

                $cmd = 'crontab -l | grep -v "bash /var/www/html/bin/crons/sh/' . $cron_name . '" | crontab -';
                $execute = exec( $cmd . ' 2>&1' );
                echo "<span style='text-align:left'>";
                if ( $execute == ' ' ) {
                    echo $this->scheduled_delete_failed . "<br>";
                    Draw::copy_text_textbox( $cmd, 'sc_d' );
                    $success = false;
                }
                echo "</span>";
                break;
        }
        if ( $success ) {
            FileSystem::append_to_file( CRON_LOG_FILE, '[' . date( 'Y-m-d G:i:s' ) . "] - CRON Deleted:: SUCCESS - {$cron_name} - {$execute}\n" );
        } else {
            FileSystem::append_to_file( CRON_LOG_FILE, '[' . date( 'Y-m-d G:i:s' ) . "] - CRON Deleted:: FAILED - {$cron_name} - {$execute}\n" );
        }
        return $success;                
    }


    /**
     * Test php CLI capabilities
     * 
     * @access  private
     * @since   3.4.0
     */

    protected function test_php_cli() {
        switch ( $this->environment ) {
            case self::WINDOWS:
                if ( !php_executable_path() ) {
                    Draw::action_error( 'The system cannot file where to find the php.exe file, please add it to PATH' );
                    die;
                }
                break;
            case self::UNIX:
                $test = shell_exec( 'php -v 2>&1' );
                if ( str_contains( $test, "Command 'php' not found" ) ) {
                    Draw::action_error( '<i>php-cli</i> not installed' );
                    die;
                }
                break;
        }
    }

    
    /**
     * Ensure the event name meets task naming conventions replacing spaces with underscores
     * 
     * @param   string  $name   The name to be tested
     * 
     * @return  string          The fixed string with spaces replaced with underscores
     * 
     * @access  private
     * @since   3.4.0
     */

    protected function event_name_convention( $name ) {
        return str_replace( ' ', '-', trim( $name ) );
    }
}