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
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     *
     * @param string $token
     * @return mixed
     */
    public function captchaverify(string $token)
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
        $data = json_decode($response);

        return (boolean)$data->success;
    }
}