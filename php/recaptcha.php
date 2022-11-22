<?php
    function CheckCaptcha($captcha) {
return true;

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array(
            'secret' => '<ENTER YOUR SECRET HERE>',
            'response' => $captcha
        );
        $options = array(
            'http' => array (
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $verify = file_get_contents($url, false, $context);
        $captcha_response=json_decode($verify);
        return $captcha_response->success;
    }

?>