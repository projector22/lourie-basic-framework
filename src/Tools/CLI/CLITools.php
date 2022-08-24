<?php

namespace LBS\Tools\CLI;

use Exception;

/**
 * Handle various Command Line Interface tools.
 * 
 * use LBS\Tools\CLI\CLITools;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   3.17.0
 */

class CLITools {

    /**
     * For debugging. Get the tool to print the command before attempting to execute it.
     * 
     * @var boolean $print_command
     * 
     * @access  public
     * @since   3.17.0
     */

    public bool $print_command = false;

    /**
     * If data should be returned as a string, set $this->return to this constant
     * 
     * @var integer STRING
     * 
     * @access  public
     * @since   3.17.0
     */

    const STRING = 0;

    /**
     * If data should be returned as an array, set $this->return to this constant.
     * This is the default choice
     * 
     * @var integer ARRAY
     * 
     * @access  public
     * @since   3.17.0
     */

    const ARRAY = 1;

    /**
     * How the instruction should store data resulting from the execution of the command.
     * 
     * ### OPTIONS
     * - self::STRING
     * - self::ARRAY
     * 
     * @var integer $return     Should be set by calling one of the above defined constants.
     * 
     * @access  public
     * @since   3.17.0
     */

    public int $return = self::ARRAY;

    /**
     * The bit of command to add to the end of instruction to hide all output
     * 
     * @var string  HIDE_OUTPUT
     * 
     * @access  public
     * @since   3.17.0
     */
    const HIDE_OUTPUT = PHP_OS === 'WINNT' ? '  > NUL' : ' /dev/null 2>&1 &';

    /**
     * Will contain the last result of an instruction executed. If nothing has been executed yet, it will be null.
     * 
     * @var string|array|null   $last_result    Default: null
     * 
     * @access  public
     * @since   3.17.0
     */

    public string|array|null $last_result = null;

    /**
     * If able, will contain the result or exit code of the instruction.
     * 
     * @var integer|null    $result_code    Default: null
     * 
     * @access  public
     * @since   3.17.0
     */

    public ?int $result_code = null;

    /**
     * Whether or not to store all previous results. Useful if looping through and executing multiple commands
     * 
     * @var boolean $keep_all_results   Default: false
     * 
     * @access  public
     * @since   3.17.0
     */

    public bool $keep_all_results = false;

    /**
     * If $this->keep_all_results is true, this stores each of those results.
     * 
     * @var array   $all_results    Default: []
     * 
     * @access  public
     * @since   3.17.0
     */

    public array $all_results = [];

    /**
     * Whether or not execute is arbitary PHP code. Will add the PHP executable and
     * a few other required bits of string onto the instruction.
     * 
     * @example if you wish to pass the php instruction `echo 'Cheese';` to the CLI, the instruction will
     *          transform into something like `php --run "echo 'Cheese';"` which should execute fine.
     * 
     * @var boolean $php_command    Default: false
     * 
     * @access  public
     * @since   3.17.0
     */

    public bool $php_command = false;


    /**
     * A standard CLI execution.
     * 
     * @param   string  $command    A line of code or a script you wish to execute.
     * 
     * @access  public
     * @since   3.17.0
     */

    public function execute( string $command ): void {
        $command = $this->check_php_command( $command );

        if ( $this->print_command ) {
            echo $command;
        }

        switch ( $this->return ) {
            case self::STRING:
                $this->last_result = shell_exec( $command );
                break;
            case self::ARRAY:
                exec( $command, $this->last_result, $this->result_code );
                break;
            default:
                throw new Exception( 'Invalid value for property \'$this->return\'' );
        }

        if ( $this->keep_all_results ) {
            $this->all_results[] = $this->last_result;
        }
    }


    /**
     * Execute a CLI command without expecting return.
     * This was an attempt to allow instructions run asynchronously, but
     * unfortunately, this doesn't work, at least most every circumstances.
     * 
     * @param   string  $command    A line of code or a script you wish to execute.
     * 
     * @access  public
     * @since   3.17.0
     */

    public function silent_execute( string $command ): void {
        $command = $this->check_php_command( $command );
        $command .= self::HIDE_OUTPUT;

        if ( $this->print_command ) {
            echo $command;
        }

        shell_exec( $command );
    }


    /**
     * Check if code requires the PHP executable moderation and add as needed.
     * 
     * @param   string  $command    A line of code or a script you wish to execute.
     * 
     * @return  string  The modified command.
     * 
     * @access  public
     * @since   3.17.0
     */

    private function check_php_command( string $command ): string {
        if ( $this->php_command ) {
            $command = "\"{$command}\"";
            $command = php_executable_path() . ' --run ' . $command;
        }
        return $command;
    }

}