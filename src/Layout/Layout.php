<?php

namespace LBF\Layout;

/**
 * Class to handle layout tasks, putting the head, body and footer onto the page.
 * 
 * use LBF\Layout\Layout;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

class Layout {

    /**
     * The contents of the HTML Header, between the `<header></header>` tags.
     * 
     * @var string $html_header
     * 
     * @static
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private static string $html_header = '';

    /**
     * The contents of the page body, between the `<body></body>` tags.
     * 
     * @var string $body
     * 
     * @static
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private static string $body = '';

    /**
     * The contents of the `<footer></footer>` tags.
     * 
     * @var string $footer
     * 
     * @static
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private static string $footer = '';

    /**
     * Any extra `<meta>` tags to be inserted into the HTML header.
     * 
     * @var array $header_meta
     * 
     * @static
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private static array $header_meta = [];


    /**********
     * HEADER *
     **********/


    /**
     * Generate the boilerplate header markup.
     * 
     * @param   string  $title          The title of the page.
     * @param   string  $description    The description of the page.
     * @param   string  $language       The language of the page. Default: 'en'.
     * @param   boolean $block_robots   Whether to require robots be blocked. Default: false
     * 
     * @return  static
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function init_header(string $title, string $description, string $language = 'en', bool $block_robots = false): static {
        self::$html_header = <<<HTML
    <!DOCTYPE html>
    <html lang='{$language}'>
    <head>
        <title>{$title}</title>
        <meta name='description' content='{$description}' />
        <meta charset='UTF-8' />
        <meta name='viewport' content='width=device-width, initial-scale=1'>
    HTML;

        if ($block_robots) {
            self::$html_header .= <<<HTML
            <meta name='robots' content='noindex, nofollow'>
            <meta name='googlebot' content='noindex, nofollow'>
            HTML;
        }

        self::$html_header .= <<<HTML
        <meta http-equiv='X-Clacks-Overhead' content='GNU Terry Pratchett' />
        <meta http-equiv='commune' content='Soli Deo Gloria' />
        HTML;
        return $this;
    }


    /**
     * Set the page favicon in the HTML Header.
     * 
     * @param   string  $favicon    The path to the favicon icon file.
     * 
     * @return  static
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function set_favicon(string $favicon): static {
        self::$html_header .= <<<HTML
        <link rel='shortcut icon' href='{$favicon}' />
        <link rel='apple-touch-icon' href='{$favicon}' />
        HTML;
        return $this;
    }


    /**
     * Append any other tags to the the HTML header, including JS & CSS files and markup.
     * 
     * @param   string  $files  The files and markup to insert.
     * 
     * @return  static
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function append_to_header(string $files): static {
        self::$html_header .= $files;
        return $this;
    }


    /**
     * Load any meta header tags into the HTML header.
     * 
     * @param   array|string    $meta   The tag or tags to insert. Insert more than one at
     *                                  a time by parsing any array.
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function load_header_meta(array|string $meta): void {
        if (is_array($meta)) {
            $meta = implode("\n", $meta);
        }
        self::$header_meta[] = $meta;
    }


    /**********
     *  BODY  *
     **********/


    /**
     * Append markup to the body element of the page.
     * 
     * @param   string  $body   The markup to insert.
     * @param   boolean $before Whether or not to insert before the body or after. Default: false (after).
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function append_to_body(string $body, bool $before = false): void {
        if ($before) {
            self::$body = $body . self::$body;
        } else {
            self::$body .= $body;
        }
    }


    /**********
     * FOOTER *
     **********/


    /**
     * Set the footer payload between the `<footer></footer>` tags.
     * 
     * @param   string  $footer The markup to insert into the footer.
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function set_footer_payload(string $footer): void {
        self::$footer .= "<footer>{$footer}</footer>";
    }


    /**
     * Append data into the footer tags.
     * 
     * @param   string  $data   The markup to insert into the footer.
     * 
     * @return  static
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function append_to_footer(string $data): static {
        self::$footer .= $data;
        return $this;
    }


    /**********
     * RENDER *
     **********/


    /**
     * Render the HTML header.
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function render_header(): void {
        self::$html_header .= implode("\n", self::$header_meta) . '</head>';
        echo self::$html_header;
    }


    /**
     * Render the HTML body.
     * 
     * @param   bool    $login  If the page being called is the login page. Default: false
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function render_body(bool $login = false): void {
        if ($login) {
            echo "<body class='login-body'>";
        } else {
            echo "<body>";
        }
        echo self::$body;
    }


    /**
     * Remder the HTML footer.
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function render_footer(): void {
        self::$footer .= '</body></html>';
        echo self::$footer;
    }
}
