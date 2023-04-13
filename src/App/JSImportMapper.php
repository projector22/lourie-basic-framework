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
        'lrs-ajax'             => 'vendor/projector22/lourie-basic-framework/src/js/lib/ajax.js',
        'lrs-datetime'         => 'vendor/projector22/lourie-basic-framework/src/js/lib/datetime.js',
        'lrs-filter'           => 'vendor/projector22/lourie-basic-framework/src/js/lib/filter.js',
        'lrs-forms'            => 'vendor/projector22/lourie-basic-framework/src/js/lib/forms.js',
        'lrs-hash'             => 'vendor/projector22/lourie-basic-framework/src/js/lib/hash.js',
        'lrs-input-validation' => 'vendor/projector22/lourie-basic-framework/src/js/lib/input_validation.js',
        'lrs-loading'          => 'vendor/projector22/lourie-basic-framework/src/js/lib/loading.js',
        'lrs-modal'            => 'vendor/projector22/lourie-basic-framework/src/js/lib/modal.js',
        'lrs-mutations'        => 'vendor/projector22/lourie-basic-framework/src/js/lib/mutations.js',
        'lrs-print'            => 'vendor/projector22/lourie-basic-framework/src/js/lib/print.js',
        'lrs-responses'        => 'vendor/projector22/lourie-basic-framework/src/js/lib/responses.js',
        'lrs-spreadsheetTool'  => 'vendor/projector22/lourie-basic-framework/src/js/lib/spreadsheetTool.js',
        'lrs-SVGTool'          => 'vendor/projector22/lourie-basic-framework/src/js/lib/SVGTool.js',
        'lrs-table-filters'    => 'vendor/projector22/lourie-basic-framework/src/js/lib/table_filters.js',
        'lrs-tools'            => 'vendor/projector22/lourie-basic-framework/src/js/lib/tools.js',
        'lrs-ui'               => 'vendor/projector22/lourie-basic-framework/src/js/lib/ui.js',
        'lrs-uploader-element' => 'vendor/projector22/lourie-basic-framework/src/js/lib/uploader_element.js',
        'lrs-uri'              => 'vendor/projector22/lourie-basic-framework/src/js/lib/uri.js',
        'lrs-validation'       => 'vendor/projector22/lourie-basic-framework/src/js/lib/validation.js',

        /**
         * This one needs to be moved to LRS
         */
        'lrs-keyboard-shortcuts' => 'vendor/projector22/lourie-basic-framework/src/js/lib/keyboard_shortcuts.js',

        /**
         * 3rd Party
         */

        'lrs-sortablejs' => 'vendor/projector22/lourie-basic-framework/node_modules/sortablejs/modular/sortable.esm.js',

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
        if (Config::current_page('tab') !== null) {
            $this->buffer = '../../../../../';
        } else {
            $this->buffer = '../../../../';
        }
        $map = [];
        foreach (self::LBF_MAP as $shortcut => $path) {
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

    public static function import(array $map): JSImportMapper {
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
        return json_encode(['imports' => $this->map]);
    }
}
