<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 5/11/18
 * Time: 14:37
 */

namespace AcMarche\Volontariat\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CaptchaService
{
    public function __construct(private ParameterBagInterface $parameterBag)
    {

    }

    /**
     *
     * @return mixed
     */
    public function captchaverify(string $token): bool
    {
        $secret = $this->parameterBag->get('acmarche_volontariat_captcha_secret_key');

        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            array(
                "secret" => $secret,
                "response" => $token,
                //     "remoteip" => $_SERVER['remote'],
            )
        );
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, null, 512, JSON_THROW_ON_ERROR);

        return (boolean)$data->success;
    }
}