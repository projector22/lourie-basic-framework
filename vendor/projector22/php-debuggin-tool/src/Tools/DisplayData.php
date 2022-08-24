<?php

namespace Debugger\Tools;

/**
 * A set of tools that can be called to help in the development and debugging of this app.
 * 
 * @author  Gareth Palmer  @evangeltheology
 * 
 * @since   1.0.0
 */

class DisplayData {

    /**
     * Display data out, formatted nicely if an object or array
     * 
     * @param   mixed   ...$data    Parse as many bits of data to display as you like
     * 
     * @see     https://www.php.net/manual/en/function.var-dump.php
     * 
     * @access  public
     * @since   1.0.0
     */

    public function data( mixed ...$data ): void {
        foreach ( $data as $entry ) {
            if ( is_object ( $entry ) ) {
                echo "<pre>";
                var_dump( $entry );
                echo "</pre>";
                return;
            }
            if ( is_array( $entry ) ) {
                echo "<pre>";
                print_r( $entry );
                echo "</pre>";
                return;
            }
            var_dump( $entry );
        }
    }


    /**
     * Table out data that may be sent, ideally in the form of an array
     * 
     * @param   array   $data   Any array, string or object
     * 
     * @access  public
     * @since   1.0.0
     */

    public static function table( array|object $data ): void {
        if ( count( $data ) == 0 ) {
            echo "No results<br>";
            return;
        }
        echo "<style>
            .debug_table {
                width: 100%;
                border-collapse: collapse;
                border: 1px solid black;
            }

            .debug_table tr td,
            .debug_table tr th {
                border: 1px solid black;
            }
            .debug_table tr:nth-child(even) {
                background-color: lightgrey;
            }
            .debug_table tr:hover {
                background-color: #4CAF50;
                color: white;
            }
        </style>";
        echo "<table class='debug_table'>";
        echo "<tr>";
        if ( gettype( $data[array_key_first( $data )] ) !== 'string' ) {
            foreach ( $data[array_key_first($data)] as $index => $entry ) {
                echo "<th>{$index}</th>";
            }
        }
        echo "</tr>";

        foreach ( $data as $index => $entry ) {
            echo "<tr>";
            if ( is_array( $entry ) || is_object( $entry ) ) {
                foreach ( $entry as $item ) {
                    echo "<td>";
                    if ( is_array( $item ) || is_object( $item ) ) {
                        self::data( $item );
                    } else {
                        echo $item;
                    }
                    echo "</td>";
                }
            } else {
                echo "<td>{$entry}</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        echo "<br>";
    }


    /**
     * Display out data from $_GET, $_POST, $_SERVER & $_SESSION.
     * 
     * @access  public
     * @since   1.0.0
     */

    public function page_data(): void {
        @session_start();
        echo "<style>
    .debug_table_page_data {
        min-width: 600px;
        border-collapse: collapse;
        border: 1px solid black;
    }

    .debug_table_page_data tr td,
    .debug_table_page_data tr th {
        border: 1px solid black;
    }
    .debug_table_page_data tr:nth-child(even) {
        background-color: lightgrey;
    }
    .debug_table_page_data tr:hover {
        background-color: #4CAF50;
        color: white;
    }
</style>";
        $entries = [
            'GET'     => $_GET,
            'POST'    => $_POST,
            'SERVER'  => $_SERVER,
            'SESSION' => $_SESSION,
        ];
        foreach ( $entries as $index => $entry ) {
            echo "<h1>{$index}</h1>";
            if ( count( $entry ) == 0 ) {
                continue;
            }
            echo "<table class='debug_table_page_data'>";
            echo "<tr>
            <th>Key</th>
            <th>Value</th>
            </tr>";
            foreach ( $entry as $key => $value ) {
                echo "<tr>";
                echo "<td>{$key}</td>";
                echo "<td>{$value}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }

}