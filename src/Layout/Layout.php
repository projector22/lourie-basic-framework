<?php

namespace LBF\Layout;

use LBF\HTML\HTML;

class Layout {
    private string $html_header = '';
    private string $body = '';
    private string $footer = '';

    public function init_header( string $title, string $description, string $language = 'en', $block_robots = false ): static {
        $this->html_header = <<<HTML
    <!DOCTYPE html>
    <html lang='{$language}'>
    <head>
        <title>{$title}</title>
        <meta name='description' content='{$description}' />
        <meta charset='UTF-8' />
        <meta name='viewport' content='width=device-width, initial-scale=1'>
    HTML;

    if ( $block_robots ) {
        $this->html_header .= <<<HTML
        <meta name='robots' content='noindex, nofollow'>
        <meta name='googlebot' content='noindex, nofollow'>
        HTML;
    }

    $this->html_header .= <<<HTML
    <meta http-equiv='X-Clacks-Overhead' content='GNU Terry Pratchett' />
    <meta http-equiv='commune' content='Soli Deo Gloria' />
    HTML;
        return $this;
    }

    public function set_favicon( string $favicon ): static {
        $this->html_header .= <<<HTML
        <link rel='shortcut icon' href='{$favicon}' />
        <link rel='apple-touch-icon' href='{$favicon}' />
        HTML;
        return $this;
    }


    public function load_header_css( string $files ): static {
        $this->html_header .= $files;
        return $this;
    }

    public function load_header_js( string $files ): static {
        $this->html_header .= $files;
        return $this;
    }

    public function load_header_meta( array|string $meta ) {
        if ( is_array( $meta ) ) {
            $meta = implode( "\n", $meta );
        }
        $this->html_header .= $meta;
    }










    public function render_header() {
        $this->html_header .= '</head>';
        echo $this->html_header;
    }
    public function render_body() {

        echo $this->body;
    }
    public function render_footer() {

        echo $this->footer;
    }
}