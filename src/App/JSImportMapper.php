<?php

namespace LBF\App;

/**
 * Tools for generating a Javascript `<script type='importmap'>` tags. Includes default tags for LBF
 * 
 * Should be used as follows:
 * 
 * ```php
 * JSImportMapper::import([
 *  'example' => '../../../example-path.js'
 *  ...
  * ])->render();
 * ```
 * 
 * use LBF\App\JSImportMapper;
 * 
 * @author  Gareth Palmer  [Github & Gitlab /projector22]
 * 
 * @since   LBF 0.6.0-beta
 */

class JSImportMapper {

    /**
     * List of mappings used by LBF JS libraries.
     * 
     * @var array   LBF_MAP
     * 
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private const LBF_MAP = [
        'lrs-ajax' => 'vendor/projector22/lourie-basic-framework/src/js/lib/ajax.js',

        'lrs-keyboard-shortcuts' => 'vendor/projector22/lourie-basic-framework/src/js/lib/keyboard_shortcuts.js',
        'lrs-table-filters'      => 'vendor/projector22/lourie-basic-framework/src/js/lib/table_filters.js',
        'lrs-uploader-element'   => 'vendor/projector22/lourie-basic-framework/src/js/lib/uploader_element.js',
        'lrs-forms'              => 'vendor/projector22/lourie-basic-framework/src/js/lib/forms.js',
        'lrs-input-validation'   => 'vendor/projector22/lourie-basic-framework/src/js/lib/input_validation.js',
        'lrs-ui'                 => 'vendor/projector22/lourie-basic-framework/src/js/lib/ui.js',

    ];

    /**
     * The complete map in the form of an array. Will be JSON encoded.
     * 
     * @var array   $map
     * 
     * @access  private
     * @since   LBF 0.6.0-beta
     */

    private array $map;

    private readonly array $lbf_map;

    private readonly string $buffer;


    public function __construct() {
        if ( Config::current_page( 'tab' ) !== null ) {
            $this->buffer = '../../../../../';
        } else {
            $this->buffer = '../../../../';
        }
        $map = [];
        foreach ( self::LBF_MAP as $shortcut => $path ) {
            $map[$shortcut] = $this->buffer . $path;
        }
        $this->lbf_map = $map;        
    }


    /**
     * Method for class instantiation and setting of mapping data.
     * 
     * @param   array   $map    User's custom mapping data.
     * 
     * @return  JSImportMapper
     * 
     * @static
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public static function import( array $map ): JSImportMapper {
        $class = __CLASS__;
        $this_obj = new $class;
        $this_obj->map = array_merge(
            $this_obj->lbf_map,
            $map,
        );
        return $this_obj;
    }


    /**
     * Returns the the JSON encoded complete mapping data.
     * 
     * @access  public
     * @since   LBF 0.6.0-beta
     */

    public function render(): string {
        return json_encode( ['imports' => $this->map] );
    }
}