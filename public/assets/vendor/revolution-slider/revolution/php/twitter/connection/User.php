<?php
namespace TwitterPhp\Connection;

class User extends Base
{
    /**
     * @param string $_consumerKey
     * @param string $_consumerSecret
     * @param string $_accessToken
     * @param string $_accessTokenSecret
     */
    public function __construct(private $_consumerKey, private $_consumerSecret, private $_accessToken, private $_accessTokenSecret)
    {
    }

    /**
     * @param string $url
     * @param $method
     */
    protected function _buildHeaders($url,array $parameters = null,$method): array
    {
        $headers = [];
        $oauthHeaders = array(
            'oauth_version' => '1.0',
            'oauth_consumer_key' => $this->_consumerKey,
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $this->_accessToken,
            'oauth_timestamp' => time()
        );

        $data = $oauthHeaders;
        if ($method == self::METHOD_GET) {
            $data = array_merge($oauthHeaders,$parameters);
        }
        $oauthHeaders['oauth_signature'] = $this->_buildOauthSignature($url,$data,$method);
        ksort($oauthHeaders);
        $oauthHeader = array();

        foreach($oauthHeaders as $key => $value) {
            $oauthHeader[] = $key . '="' . rawurlencode($value) . '"';
        }

        $headers[] = 'Authorization: OAuth ' . implode(', ', $oauthHeader);
        return $headers;
    }

    /**
     * @param $url
     * @param $method
     */
    private function _buildOauthSignature($url,array $params,$method): string
    {
        ksort($params);
        $sortedParams = array();

        foreach($params as $key=>$value) {
            $sortedParams[] = $key . '=' . $value;
        }

        $signatureBaseString =  $method . "&" . rawurlencode($url) . '&' . rawurlencode(implode('&', $sortedParams));
        $compositeKey = rawurlencode($this->_consumerSecret) . '&' . rawurlencode($this->_accessTokenSecret);
        return base64_encode(hash_hmac('sha1', $signatureBaseString, $compositeKey, true));
    }
}