<?php

namespace LBF\HTML\Injector;

/**
 * The trait has common methods used by the CSS & JS injectors.
 * 
 * LBF\HTML\Injector\InjectorMeta;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

trait InjectorMeta {


    /**
     * Set the default values to the the JS or CSS array.
     * 
     * @static
     * @return  array
     * @since   LBF 0.6.0-beta
     */

    protected static function set_default_data(): array {
        return [
            PagePositions::IN_HEAD->id() => [
                'raw' => [],
                'cdn' => [],
            ],
            PagePositions::TOP_OF_PAGE->id() => [
                'raw' => [],
                'cdn' => [],
            ],
            PagePositions::BOTTOM_OF_PAGE->id() => [
                'raw' => [],
                'cdn' => [],
            ],
        ];
    }


    /**
     * Remove any duplicates in the array of data parsed.
     * 
     * @param   array   $data   List of styles / js.
     * 
     * @return  array
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function remove_duplicates( array $data ): array {
        return array_unique( $data );
    }


    /**
     * Merge any entries into a single string.
     * 
     * @param   array   $data   List of styles / js.
     * 
     * @return  string
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function merge( array $data ): string {
        return implode( "\n", $data );
    }


    /**
     * Task to combine both Javascript and CSS into the desired part of the page.
     * 
     * @param   PagePositions   $position   The position to insert the Javascript & styles.
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function insert_js_and_css( PagePositions $position ): void {
        $this->insert_js( $position );
        $this->insert_css( $position );
    }

}