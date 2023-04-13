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

class Captcha {

    /**
     * Inject the basic reCAPTCHA scripts to a form.
     * 
     * @param   string  $form_id    The id of the form being observed.
     * 
     * @static
     * @access  public
     * @since   LBF 0.3.2-beta
     */

    public static function scripts(string $form_id): void {
        echo '<script src="https://www.google.com/recaptcha/api.js"></script>';
        echo "<script>
    function onSubmit(token) {
        document.getElementById('{$form_id}').submit();
    }
</script>";
    }


    /**
     * Draw a submit button with the relevant CAPTCHA properties attached.
     * 
     * @param   array   $params     The params to be parsed to the button.
     * 
     * @static
     * @access  public
     * @since   LBF 0.3.2-beta
     */

    public static function submit(array $params): void {
        $params['data-sitekey']  = $_ENV['CAPTCHA_SITE_KEY'];
        $params['data-callback'] = 'onSubmit';
        $params['data-action']   = 'submit';

        if (isset($params['class'])) {
            $params['class'] .= ' g-recaptcha';
        } else {
            $params['class'] = 'g-recaptcha';
        }

        Button::general($params);
    }
}
