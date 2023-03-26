<?php

namespace LBF\Error;

use LBF\Assets\HTTPStatusCode;

/**
 * Tool for handling and generating HTTP error pages.
 * 
 * use LBF\Error\ErrorPage;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

class ErrorPage {

    /**
     * The defined error code thrown by the app.
     * 
     * @var integer $code
     * 
     * @static
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private static int $code;

    /**
     * Whether or not to skip the router check. You would set this to true
     * if an error page has already been thrown and generated.
     * 
     * @var boolean $skip_check
     * 
     * @static
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private static bool $skip_check = false;


    /**
     * Draw up the error page as defined by `self::$code`.
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function construct_page(): void {
        echo match (self::$code) {
            401     => $this->error_401(),
            403     => $this->error_403(),
            404     => $this->error_404(),
            500     => $this->error_500(),
            default => $this->error_500(),
        };
    }


    /**
     * Set the error message code and skip check properties. Used to "throw" an error code.
     * 
     * @param   integer $code       The code number to register.
     * @param   boolean $skip_check Whether or not to skip the check in the router. Default: false.
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function set_error(int $code, $skip_check = false): void {
        self::$code = $code;
        self::$skip_check = $skip_check;
    }


    /**
     * Return the current error code, or null if none has been set.
     * 
     * @return  integer|null
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function get_error_code(): ?int {
        return self::$code ?? null;
    }


    /**
     * Returns the current skip_check status.
     * 
     * @return  boolean
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function skip_check(): bool {
        return self::$skip_check;
    }


    /**
     * Draw out the page that will display when there is a 401 error.
     * 
     * @access  private
     * @since   LRS 3.9.1   Reworked and remade
     * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
     *                      Namespace changed from `Framework` to `LBF`.
     * @since   LBF 0.6.0   Merged into ErrorPage Class.
     */

    private function error_401(): void {
        $image = html_path(HTTPStatusCode::UNAUTHORIZED->image()->path());
        echo "<div class='container__401'>";
        echo "<h3>Can't find your creds here mate...</h3>";
        echo "<img class='error_img' src='{$image}'>";
        echo "<h1>401 - Unauthorized</h1>";
        echo "</div>";
    }


    /**
     * Draw out the page that will display when there is a 403 error.
     * 
     * @access  private
     * @since   LRS 3.9.1   Reworked and remade
     * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
     *                      Namespace changed from `Framework` to `LBF`.
     * @since   LBF 0.6.0   Merged into ErrorPage Class.
     */

    private function error_403(): void {
        $image = html_path(HTTPStatusCode::FORBIDDEN->image()->path());
        echo "<div class='container__403'>";
        echo "<h3>You shall not pass!</h3>";
        echo "<img class='error_img' src='{$image}'>";
        echo "<h1>403 - Forbidden</h1>";
        echo "</div>";
    }


    /**
     * Draw out the page that will display when there is a 404 error.
     * 
     * @access  private
     * @since   LRS 3.1.0
     * @since   LRS 3.9.1   Reworked and remade, new image & text
     * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
     *                      Namespace changed from `Framework` to `LBF`.
     * @since   LBF 0.6.0   Merged into ErrorPage Class.
     */

    private function error_404(): void {

        $image = html_path(HTTPStatusCode::NOT_FOUND->image()->path());
        echo "<div class='container__404'>";
        echo "<h3>Um... yeah</h3>";
        echo "<img class='error_img' src='{$image}'>";
        echo "<h1>404 - Page Not Found</h1>";
        echo "</div>";
    }


    /**
     * Draw out the 500 error page.
     * 
     * @access  private
     * @since   LBF 0.6.0
     */

    private function error_500(): void {
        echo "<h2>Error 500</h2>";
        echo "<h3>Internal Server Error</h3>";
        echo "The server has encountered a situation it does not know how to handle.";
    }
}
