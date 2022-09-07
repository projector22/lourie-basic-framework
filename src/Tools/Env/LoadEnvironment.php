<?php

namespace LBF\Tools\Env;

use Throwable;
use LBF\Errors\FileNotFoundError;
use LBF\Errors\ConstantAlreadyDefinedError;

/**
 * Load environmental data to the system.
 * 
 * use LBF\Tools\Env\LoadEnvironment;
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   0.1.4-beta
 */

class LoadEnvironment {

    /**
     * Load lines from the env file into an array
     * 
     * @var string  $lines  All the lines from the .env file.
     * 
     * @access  private
     * @since   0.1.4-beta
     */

    private readonly array $lines;


    /**
     * Class constructor. Loads the environmental file (usually .env) file.
     * 
     * @param   string $path_to_env
     * 
     * @access  public
     * @since   LBF 0.1.4-beta
     */

    public function __construct(

        /**
         * The full path to the environment variable.
         * 
         * @var string  $path_to_env
         * 
         * @throws  FileNotFoundError   If the environmental variable doesn't exist.
         * 
         * @readonly
         * @access  private
         * @since   LBF 0.1.4-beta
         */

        private readonly string $path_to_env
    ) {
        if ( !file_exists( $this->path_to_env ) ) {
            echo "<pre>";
            throw new FileNotFoundError( "Environment variable file not found." );
        }
        $lines = explode( "\n", file_get_contents( $this->path_to_env ) );
        foreach( $lines as $index => $line ) {
            if ( $line == '' ) {
                unset( $lines[$index] );
            }
            if ( trim( $line)[0] == '#') {
                // skip comments
                unset( $lines[$index] );
            }
        }
        $this->lines = $lines;
    }


    /**
     * Load environmental variables to `$_ENV` and `getenv()`.
     * 
     * @return  boolean
     * 
     * @access  public
     * @since   0.1.4-beta
     */

    public function load_to_env(): bool {
        foreach ( $this->lines as $line ) {
            try {
                $parts = explode( '=', $line );
                $_ENV[$parts[0]] = $parts[1];
                putenv( $line );
            } catch ( Throwable $th ) {
                return false;
            }
        }
        return true;
    }


    /**
     * Load environmental variables to a DEFINED .
     * 
     * @return  boolean
     * 
     * @throws  ConstantAlreadyDefinedError If the constant is already defined in the app.
     * 
     * @throws  
     * @access  public
     * @since   0.1.4-beta
     */

    public function load_to_const(): bool {
        foreach ( $this->lines as $line ) {
            try {
                $parts = explode( '=', $line );
                if ( defined( $parts[0] ) ) {
                    echo "<pre>";
                    throw new ConstantAlreadyDefinedError( "Constant {$parts[0]} already defined in the app." );
                }
                define( $parts[0], $parts[1] );
            } catch ( ConstantAlreadyDefinedError $e ) {
                die( $e->getMessage() );
            } catch ( Throwable $th ) {
                return false;
            }
        }
        return false;
    }

}