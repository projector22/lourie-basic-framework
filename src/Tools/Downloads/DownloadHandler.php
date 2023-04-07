<?php

namespace LBF\Tools\Downloads;

use LBF\App\Config;
use LBF\Config\AppMode;
use LBF\Errors\Files\FileNotFound;
use LBF\HTML\Draw;
use LBF\Router\Nav;

/**
 * Class for controlling downloads through the app, rejecting non logged in downloads.
 * 
 * use LBF\Tools\Downloads\DownloadHandler;
 * 
 * @see     https://wordpress.stackexchange.com/questions/281500/protecting-direct-access-to-pdf-and-zip-unless-user-logged-in-without-plugin
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.6.3
 * @since   LRS 3.11.0  Moved to `Framework\Tools\Downloads` and class renamed `DownloadHandler` from `Downloads`.
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                      Namespace changed from `Framework` to `LBF`.
 * @since   LBF 0.6.0-beta  Intirely rewritten to be a compact single line command.
 */

final class DownloadHandler {

    /**
     * The mimetype of the file to download
     * 
     * @var string  $mime_type
     * 
     * @readonly
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private readonly string $mime_type;


    /**
     * Constructor method, things to do when the class is loaded.
     * 
     * @param   string  $file   The file to be downloaded, can be specified later
     *                          Default: null
     * 
     * @access  public
     * @since   LRS 3.6.3
     */

    public function __construct(

        /**
         * The full file path of the file that should be downloaded
         * 
         * @var string  $file
         * 
         * @access  public
         * @since   LRS 3.6.3
         */

        public readonly string $file
    ) {
        if (is_null(Config::user()->account_name)) {
            Nav::redirect('/forbidden');
        }
    }


    /**
     * Statically instantiate the class and import the file desired to be downloaded.
     * 
     * @param   string  $file   Full path of the file to be downloaded.
     * 
     * @return  DownloadHandler
     * 
     * @throws  FileNotFound
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function file(string $file): DownloadHandler {
        if (!file_exists($file)) {
            if (Config::ENVIRONMENT() == AppMode::DEVELOPEMENT) {
                throw new FileNotFound("File {$file} does not exist.");
            } else {
                Nav::redirect('/not-found');
            }
        }
        return new DownloadHandler($file);
    }


    /**
     * Execute the download
     * 
     * Note - you cannot start a download directly from an AJAX for security reasons.
     * It is better to open a new window and execute the download
     * 
     * @return  never
     * 
     * @access  public
     * @since   LRS 3.6.3
     * @since   LBF 0.6.0-beta  Renamed from `execute_download` to `download`.
     */

    public function download(): never {
        $this->mime_type ??= mime_content_type($this->file);
        $file_name = basename($this->file);

        header('Content-Description: File Transfer');
        header('Content-type: ' . $this->mime_type);
        header('Content-Disposition: inline; filename="' . $file_name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        readfile($this->file);
        die;
    }


    /**
     * If a mimetype is not being detected correctly, you can set it deliberately here.
     * 
     * @param   string  $mime_type  The mimetype of the file to be downloaded. For example `text/csv`.
     * 
     * @return  static
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function set_mime_type(string $mime_type): static {
        $this->mime_type = $mime_type;
        return $this;
    }


    /**
     * Create url link for downloading the file
     * 
     * @param   string      $token  The download token to talk to the download handler.
     * @param   string|null $file   The file to be downloaded, can be specified later
     *                          Default: null
     * 
     * @return  string  url string
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.3
     */

    public static function create_download_url(string $token, ?string $file = null): string {
        $url = "/download?token={$token}";
        if (!is_null($file)) {
            $url .= "&file={$file}";
        }
        return $url;
    }


    /**
     * Display various errors on the screen
     * 
     * @param   integer|null $error  The error code. Default: null
     * 
     * @return  never
     * 
     * @static
     * @access  public
     * @since   LRS 3.6.3
     */

    public static function download_error(?int $error = null): never {
        Draw::temporary_change_echo(false);
        echo match ($error) {
            1 => "File not available for download",
            2 => "Not all required info is available",
            3 => "You are not permitted to view this page",
            default => Draw::action_error(),
        };
        die;
    }
}
