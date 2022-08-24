<?php

namespace Debugger\Tools;

use DateTime;

/**
 * Class for providing a number of timing handling tools.
 * 
 * @author  Gareth Palmer   @evangeltheology
 * 
 * @since   1.0.0
 */

class Timing {

    /**
     * The time which the code execution begins
     * 
     * @var string  $start_time
     * 
     * @access  private
     * @since   1.0.0
     */

    private string $start_time;

    /**
     * The time which the code execution ends
     * 
     * @var string  $end_time
     * 
     * @access  private
     * @since   1.0.0
     */

    private string $end_time;

    /**
     * Time marks within the running of the app.
     * 
     * @var array   $mark
     * 
     * @access  private
     * @since   1.0.0
     */

    private array $mark = [];

    /**
     * Counter for each entry;
     * 
     * @var integer $i
     * 
     * @access  private
     * @since   1.0.0
     */

    private int $i = 1;


    /**
     * Add the code element to start timing the code
     * 
     * @access  public
     * @since   1.0.0
     */

    public function start(): void {
        $this->start_time = microtime( true );
    }


    /**
     * Add a time report mark.
     * 
     * @param   string|null $label  A custom label
     * 
     * @access  public
     * @since   1.0.0
     */

    public function timestamp( ?string $label = null ): void {
        $this->mark[$this->i]['time'] = microtime( true );
        if ( is_null( $label ) ) {
            $this->mark[$this->i]['label'] = '';
        } else {
            $this->mark[$this->i]['label'] = $label;
        }
        $this->i++;
    }


    /**
     * Add the code elements to end timing the code and display results on the screen
     * 
     * @param   boolean $show_marks Whether to show all the timestamps Default: false
     * 
     * @access  public
     * @since   1.0.0
     */

    public function end( bool $show_marks = false ): void {
        $this->end_time = microtime( true );
        if ( $show_marks ) {
            $this->show_timestamps();
        } else {
            echo "The code took " . ( $this->end_time - $this->start_time ) . " seconds to complete.";
        }
    }


    /**
     * Show table of all the timestamps
     * 
     * @access  private
     * @since   1.0.0
     */

    private function show_timestamps(): void {
        echo "<style>
    .debug_table_timer {
        width: 800px;
        border-collapse: collapse;
        border: 1px solid black;
    }

    .debug_table_timer tr td,
    .debug_table_timer tr th {
        border: 1px solid black;
        padding: 3px;
    }
    .debug_table_timer tr:nth-child(even) {
        background-color: lightgrey;
    }
    .debug_table_timer tr:hover {
        background-color: #4CAF50;
        color: white;
    }
</style>";
        echo "<table class='debug_table_timer'>";
        echo "<tr>
        <th>Index</th>
        <th>Label</th>
        <th>Timestamp</th>        
        </tr>";
        $st = DateTime::createFromFormat( 'U.u', $this->start_time );
        echo "<tr>
        <td>0</td>
        <td>Start Recording</td>
        <td>{$st->format( "Y-m-d H:i:s.u" )}</td>
        </tr>";

        foreach ( $this->mark as $i => $entry ) {
            echo "<tr>";
            echo "<td>{$i}</td>";
            echo "<td>" . $entry['label'] . "</td>";
            echo "<td>" . $entry['time'] - $this->start_time  . "</td>";
            echo "</tr>";
        }

        echo "<tr>
        <td>" . ++$this->i . "</td>
        <td>End Recording</td>
        <td>" . $this->end_time - $this->start_time . "</td>
        </tr>";
        echo "</table>";
    }

}