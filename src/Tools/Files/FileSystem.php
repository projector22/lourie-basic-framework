<?php

namespace LBF\Tools\Files;

use Exception;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Various methods for performing and executing LDAP queries
 * 
 * use LBF\Tools\Files\FileSystem;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @see     https://stackoverflow.com/questions/927564/how-to-find-a-reason-when-mkdir-fails-from-php
 * 
 * @since   LRS 3.4.0
 * @since   LRS 3.11.0  Moved to `Framework\Tools\FileSystem`.
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                      Namespace changed from `Framework` to `LBF`.
 */

class FileSystem {

    /**
     * Contains the last error that occured.
     * 
     * @var string  $last_error
     * 
     * @static
     * @access  public
     * @since   LRS 3.15.4
     */

    public static string $last_error;


    /**
     * Contains an array of all the errors.
     * 
     * @var array   $all_errors     Default: []
     * 
     * @static
     * @access  public
     * @since   LRS 3.15.4
     */

    public static array $all_errors = [];

    /**
     * Get the file permission octal. Eg. 0775
     * 
     * @param   string  $file   Path of file to be examined
     * 
     * @return  string  Octal file permissions, eg. 0775
     * 
     * @static
     * @access  public
     * @since   LRS 3.7.0
     */

    public static function get_permissions(string $file): string {
        return substr(sprintf("%o", fileperms($file)), -4);
    }


    /**
     * Fix the slashes used in a file path. Windows convert '\' to '/' and on Unix type systems '/' to '\'
     * 
     * @param   string  $file   Path of file to be examined
     * 
     * @return  string  string with slashes corrected
     * 
     * @static
     * @access  public
     * @since   LRS 3.7.0
     */

    public static function correct_slashes(string $file): string {
        if (PHP_OS === 'WINNT') {
            return str_replace('/', '\\', $file);
        } else {
            return str_replace('\\', '/', $file);
        }
    }


    /**
     * Create a folder, set appropriate permission and owner if desired.
     * 
     * @param   string      $path           The full path of the folder to create
     * @param   string      $permissions    Must be an octal.
     *                                      Default: '0755'. Should be parsed as a string.
     * @param   string      $owner          The owner of the file. Leave a null to skip.
     *                                      Should be sent as user:group. You can send just user,
     *                                      or just :group if desired.
     *                                      Default: null
     * 
     * @return  boolean     Success or failure.
     * 
     * @static
     * @access  public
     * @since   LRS 3.15.4
     */

    public static function create_folder(string $path, string $permissions = '0755', ?string $owner = null): bool {
        $success = false;
        try {
            $success = @mkdir($path, $permissions, true);
            if (!is_dir($path)) {
                throw new Exception("{$path} cannot be created, you do not have appropriate permissions.<br>");
            }
        } catch (Exception $e) {
            self::$last_error   = $e->getMessage();
            self::$all_errors[] = $e->getMessage();
            echo $e->getMessage();
            return false;
        }
        /**
         * If not in the Windows environment, i.e. in a Linux like environment
         * Set permissions and, if desired, the owner.
         * 
         * @since   LRS 3.15.4
         */
        if (PHP_OS !== 'WINNT') {
            exec("find {$path} -type d -exec chmod {$permissions} {} +");
            try {
                if (!is_null($owner)) {
                    $permits = explode(':', $owner);
                    if (isset($permits[0])) {
                        chown($path, $permits[0]);
                    }
                    if (isset($permits[1])) {
                        chgrp($path, $permits[1]);
                    }
                }
            } catch (Exception $e) {
                self::$last_error   = $e->getMessage();
                self::$all_errors[] = $e->getMessage();
                $success = false;
            }
        }
        return $success;
    }


    /**
     * A simple method for writing to / creating files.
     * 
     * @param   string  $file_path  The full path to the file.
     *                  For example C:\path\to\file.txt
     *                  or          /home/my/file.txt
     * @param   string  $contents   The contents to write or append to a file.
     * @param   string  $method     Default: 'w'
     * 
     * @see URL_TO_FOPEN for details of the method.
     * 
     * ### Method Options
     * | mode | Description |
     * | ---- | ----------- |
     * | 'r' | Open for reading only; place the file pointer at the beginning of the file. |
     * | 'r+' | Open for reading and writing; place the file pointer at the beginning of the file. |
     * | 'w' | Open for writing only; place the file pointer at the beginning of the file and truncate the file to zero length. If the file does not exist, attempt to create it. |
     * | 'w+' | Open for reading and writing; place the file pointer at the beginning of the file and truncate the file to zero length. If the file does not exist, attempt to create it. |
     * | 'a' | Open for writing only; place the file pointer at the end of the file. If the file does not exist, attempt to create it. |
     * | 'a+' | Open for reading and writing; place the file pointer at the end of the file. If the file does not exist, attempt to create it. |
     * | 'x' | Create and open for writing only; place the file pointer at the beginning of the file. If the file already exists, the fopen call will fail by returning false and generating an error of level E_WARNING. If the file does not exist, attempt to create it. This is equivalent to specifying O_EXCL |
     * | 'x+' | Create and open for reading and writing; place the file pointer at the beginning of the file. If the file already exists, the fopen call will fail by returning false and generating an error of level E_WARNING. If the file does not exist, attempt to create it. This is equivalent to specifying O_EXCL |
     * 
     * @return  boolean
     * 
     * @static
     * @access  public
     * @since   LRS 3.17.3
     */

    public static function write_file(
        string $file_path,
        string $contents,
        string $method = 'w'
    ): bool {
        try {
            $file = fopen($file_path, $method);
            fwrite($file, $contents);
            fclose($file);
            return true;
        } catch (Exception $e) {
            self::$last_error   = $e->getMessage();
            self::$all_errors[] = $e->getMessage();
            return false;
        }
    }


    /**
     * Create a blank file
     * 
     * @param   string  $file_path  The full path to the file.
     *                  For example C:\path\to\file.txt
     *                  or          /home/my/file.txt
     * 
     * @return  boolean
     * 
     * @static
     * @access  public
     * @since   LRS 3.17.3
     */

    public static function create_blank_file(string $file_path): bool {
        if (!file_exists($file_path)) {
            return touch($file_path);
        }
        return false;
    }


    /**
     * Create a blank file
     * 
     * @param   string  $file_path  The full path to the file.
     *                  For example C:\path\to\file.txt
     *                  or          /home/my/file.txt
     * @param   string  $contents   The contents to write or append to a file.
     * 
     * @return  boolean
     * 
     * @static
     * @access  public
     * @since   LRS 3.17.3
     */

    public static function append_to_file(string $file_path, string $contents): bool {
        return self::write_file($file_path, $contents, 'a');
    }


    /**
     * Deletes a folder and it's contents.
     * 
     * @param   string  $folder_path    Full path to folder that should be deleted.
     * 
     * @return  boolean
     * 
     * @static
     * @access  public
     * @since   LRS 3.19.6
     */

    public static function delete_folder(string $folder_path): bool {
        try {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($folder_path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($folder_path);
            return true;
        } catch (Exception $e) {
            self::$last_error   = $e->getMessage();
            self::$all_errors[] = $e->getMessage();
            return false;
        }
    }


    /**
     * Delete single or multiple listed files.
     * 
     * @param   string|array    ...$files   File or files to delete.
     * 
     * @return  boolean
     * 
     * @static
     * @access  public
     * @since   LRS 3.26.4
     */

    public static function delete_files(string|array ...$files): bool {
        if ($files == 'string') {
            return unlink($files);
        } else {
            $status = true;
            foreach ($files as $file) {
                $status = unlink($file);
                if (!$status) {
                    break;
                }
            }
            return $status;
        }
    }


    /**
     * Returns all the files in a folder. Automatically skips the '.' & '..' files.
     * 
     * @param   string  $path   Path to folder to be scanned.
     * 
     * @return  array
     * 
     * @static
     * @access  public
     * @since   LRS 3.21.1
     */

    public static function get_all_files_in_folder(string $path): array {
        if (!is_dir($path)) {
            throw new Exception("{$path} is not a folder.");
        }
        $files = scandir($path);
        foreach ($files as $i => $file) {
            if ($file == '.' || $file == '..') {
                unset($files[$i]);
            }
        }
        sort($files);
        return $files;
    }


    /**
     * Returns if the file exists.
     * 
     * @param   string  $path   Path to the file to be tested.
     * 
     * @return  boolean
     * 
     * @static
     * @access  public
     * @since   LBF 0.1.6-beta
     */

    public static function file_exists(string $path): bool {
        return file_exists($path);
    }


    /**
     * Copy a file from a source to a destination.
     * 
     * @param   string  $source         The source file to be copied. Must be the full file path including the file name.
     *                                  Example: `/var/my/source/myFile.txt`.
     * @param   string  $destination    The copy destination file. Must be the full file path including the file name.
     *                                  Example: `/var/my/destination/myFile.txt`.
     * 
     * @return  bool
     * 
     * @static
     * @access  public
     * @since   LBF 0.2.3-beta
     */

    public static function copy_file(string $source, string $destination): bool {
        try {
            return copy($source, $destination);
        } catch (\Throwable $th) {
            return false;
        }
    }


    /**
     * Move a file from a source to a destination.
     * 
     * @param   string  $source         The source file to be moved. Must be the full file path including the file name.
     *                                  Example: `/var/my/source/myFile.txt`.
     * @param   string  $destination    The copy destination file. Must be the full file path including the file name.
     *                                  Example: `/var/my/destination/myFile.txt`.
     * 
     * @return  bool
     * 
     * @static
     * @access  public
     * @since   LBF 0.2.3-beta
     */

    public static function move_file(string $source, string $destination): bool {
        try {
            return rename($source, $destination);
        } catch (\Throwable $th) {
            return false;
        }
    }
}
