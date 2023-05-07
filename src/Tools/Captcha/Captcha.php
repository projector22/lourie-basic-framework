<?php

namespace LBF\Tools\Captcha;

use LBF\HTML\Button;

/**
 * Impliment various tools for use in performing a reCAPTCHA.
 * 
 * use LBF\Tools\Captcha;
 * 
 * @author  Gareth Palmer   [Github & Gitlab /projector22]
 * @since   LBF 0.3.2-beta
 */

final class Captcha {

    /**
     * Class constructor, injects the script elements.
     * 
     * @param   string  $form_id    The id of the form.
     * 
     * @access  public
     * @since   LBF 0.7.2-beta
     */

    public function __construct(

        /**
         * The id of the form which is being captcha'd
         * 
         * @var string  $form_id
         * 
         * @readonly
         * @access  public
         * @since   LBF 0.7.2-beta
         */

        private readonly string $form_id
    ) {
        echo '<script src="https://www.google.com/recaptcha/api.js"></script>';
        echo "<script>";
        echo <<<JS
        function onSubmit(token) {
            document.getElementById('{$this->form_id}').submit();
        }
        JS;
        echo "</script>";
    }


    /**
     * Static class constructor, sets the form id.
     * 
     * @param   string  $id The id of the form being captcha'd
     * 
     * @return  Captcha
     * 
     * @static
     * @access  public
     * @since   LBF 0.7.2-beta
     */

    public static function form_id(string $id): Captcha {
        return new Captcha($id);
    }


    /**
     * Returns the required params for the submit button, used in captchaing.
     * If not present, the captcha will fail.
     * 
     * @return  array
     * 
     * @access  public
     * @since   LBF 0.7.2-beta
     */

    public function params(): array {
        return [
            'data-sitekey' => getenv('CAPTCHA_SITE_KEY'),
            'data-callback' => 'onSubmit',
            'data-action' => 'submit',
            'class' => 'g-recaptcha',
        ];
    }


    /**
     * Merges the button's params with the required params.
     * 
     * @param   array   $params The button's params.
     * 
     * @return  array
     * 
     * @access  public
     * @since   LBF 0.7.2-beta
     */

    public function merge_params(array $params): array {
        $captcha_params = $this->params();
        if (isset($params['class'])) {
            $params['class'] .= ' ' . $captcha_params['class'];
            unset($captcha_params['class']);
        }
        return array_merge($params, $captcha_params);
    }


    /**
     * Draw a submit button with the relevant CAPTCHA properties attached.
     * 
     * @param   array   $params     The params to be parsed to the button.
     * 
     * @access  public
     * @since   LBF 0.3.2-beta
     */

    public function submit(array $params): void {
        Button::general($this->merge_params($params));
    }
}
