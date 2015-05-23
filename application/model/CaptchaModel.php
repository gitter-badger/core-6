<?php

/**
 * Class CaptchaModel
 *
 * This model class handles all the captcha stuff.
 * Currently this uses the excellent Captcha generator lib from https://github.com/Gregwar/Captcha
 * Have a look there for more options etc.
 */
class CaptchaModel {
    /**
     * Generates the captcha, "returns" a real image, this is why there is header('Content-type: image/jpeg')
     * Note: This is a very special method, as this is echoes out binary data.
     */
    public static function generateAndShowCaptcha() {
        // create a captcha with the CaptchaBuilder lib (loaded via Composer)
        $captcha = new Gregwar\Captcha\CaptchaBuilder;
        $captcha->build(Config::get('CAPTCHA_WIDTH'), Config::get('CAPTCHA_HEIGHT'));

        // write the captcha character into session
        Session::set('captcha', $captcha->getPhrase());

        // render an image showing the characters (=the captcha)
        header('Content-type: image/jpeg');
        $captcha->output();
    }

    /**
     * Checks if the entered captcha is the same like the one from the rendered image which has been saved in session
     * @param $captcha string The captcha characters
     * @return bool success of captcha check
     */
    public static function checkCaptcha($captcha) {
        if($captcha == Session::get('captcha')) {
            return true;
        }

        return false;
    }

    /**
     * Check Google ReCaptcha
     * @param $gReCaptchaResponse
     * @return bool
     */
    public static function checkRecaptcha($gReCaptchaResponse) {
        $recaptcha = new \ReCaptcha\ReCaptcha(Config::get('RECAPTCHA_SECRET'));
        $resp = $recaptcha->verify($gReCaptchaResponse, Request::server('REMOTE_ADDR'));
        if ($resp->isSuccess()) {
            return true;
        } else {
            return false;
        }
    }
}
