<?php

namespace LBF\Layout;

class Layout {
    private static string $html_header = '';
    private static string $body = '';
    private static string $footer = '';

    private static array $header_meta = [];

    public function init_header( string $title, string $description, string $language = 'en', $block_robots = false ): static {
        self::$html_header = <<<HTML
    <!DOCTYPE html>
    <html lang='{$language}'>
    <head>
        <title>{$title}</title>
        <meta name='description' content='{$description}' />
        <meta charset='UTF-8' />
        <meta name='viewport' content='width=device-width, initial-scale=1'>
    HTML;

        if ( $block_robots ) {
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

    public function set_favicon( string $favicon ): static {
        self::$html_header .= <<<HTML
        <link rel='shortcut icon' href='{$favicon}' />
        <link rel='apple-touch-icon' href='{$favicon}' />
        HTML;
        return $this;
    }


    public function append_to_header( string $files ): static {
        self::$html_header .= $files;
        return $this;
    }


    public static function load_header_meta( array|string $meta ): void {
        if ( is_array( $meta ) ) {
            $meta = implode( "\n", $meta );
        }
        self::$header_meta[] = $meta;
    }



    public static function append_to_body( string $body, bool $before = false ): void {
        if ( $before ) {
            self::$body = $body . self::$body;
        } else {
            self::$body .= $body;
        }
    }




    public static function set_footer_payload( string $footer ): void {
        self::$footer .= "<footer>{$footer}</footer>";
    }

    public function append_to_footer( string $data ): static {
        self::$footer .= $data;
        return $this;
    }


    public function render_header() {
        self::$html_header .= implode( "\n", self::$header_meta ) . '</head>';
        echo self::$html_header;
    }
    public function render_body() {
        echo "<body>" . self::$body;
    }
    public function render_footer() {
        self::$footer .= '</body></html>';
        echo self::$footer;
    }
}