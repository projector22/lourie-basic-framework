<?php

namespace LBF\HTML;

use LBF\HTML\HTML;

/**
 * Tool for drawing a terminal window onto the screen.
 * 
 * use LBF\HTML\Terminal;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LRS 3.27.0
 * @since   LRS 3.28.0  Seperated out of `Lourie Registration System` into `Lourie Basic Framework`.
 *                  Namespace changed from `Framework` to `LBF`.
 */

class Terminal {

    /**
     * The default heading to draw.
     * 
     * @var string  DEFAULT_HEADING
     * 
     * @access  public
     * @since   LRS 3.27.0
     */

    const DEFAULT_HEADING = "<h2 class='center_text'>LRS Feedback Console</h2>";

    /**
     * The content to draw in the terminal when it is first loaded
     * 
     * @var string  $content
     * 
     * @access  private
     * @since   LRS 3.27.0
     */

    private string $content = '';


    /**
     * Class constructor. Called when the class loads
     * 
     * @param   string  $console_id     Set the ID of the console.
     *                                  Default: `feedback_console`
     * 
     * @access  public
     * @since   LRS 3.27.0
     */

    public function __construct(

        /**
         * Set the id of the terminal.
         * 
         * @var string  $console_id     Default: `feedback_console`.
         * 
         * @access  private
         * @since   LRS 3.27.0
         */

        private string $console_id = 'feedback_console',
    ) {
        // Nothing else
    }


    /**
     * Draw out the console onto the screen.
     * 
     * @param   boolean $use_default_heading    Default: true
     * @param   boolean $open                   Default: true
     * 
     * @access  public
     * @since   LRS 3.27.0
     */

    public function draw( bool $use_default_heading = true, bool $open = true ): void {
        $class = 'feedback_console';

        if ( $open ) {
            $class .= ' feedback_console_height';
        }
        
        HTML::div( [
            'class'                => $class,
            'id'                   => $this->console_id,
            'data-default-heading' => htmlspecialchars ( self::DEFAULT_HEADING ),
        ] );

        if ( $use_default_heading ) {
            echo self::DEFAULT_HEADING;
        }

        echo $this->content;

        HTML::close_div(); // feedback_console
        // HTML::div(); // Options
        
        /**
         * @todo    Create a `src/Framework/HTML/Forms.php` Form for this type of input.
         * 
         * @todo    Impliment on the terminal.
         * 
         * @since   LRS 3.27.0
         */
        // echo "<input type='range' min='1', max='100' value='90' id='terminal_opacity.{$this->console_id}' name='terminal_opacity'>";
        
        // HTML::close_div();
    }


    /**
     * Set or append data to be drawn on the screen when the terminal first loads.
     * 
     * @param   string  $content    Content to add.
     * @param   boolean $append     Whether to append or replace the already set data.
     *                              Default: false
     * 
     * @access  public
     * @since   LRS 3.27.0
     */

    public function set_default_content( string $content, bool $append = false ): void {
        if ( $append ) {
            $this->content .= $content;
        } else {
            $this->content = $content;
        }
    }

}