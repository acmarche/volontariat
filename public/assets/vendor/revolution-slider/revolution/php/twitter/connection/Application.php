<?php
namespace TwitterPhp\Connection;

use TwitterPhp\RestApiException;

class Application extends Base
{
    private ?string $_bearersToken = null;

    /**
     * @param string $_consumerKey
     * @param string $_consumerSecret
     */
    public function __construct(private $_consumerKey, private $_consumerSecret)
    {
    }

    /**
     * @param string $url
     * @param $method
     */
    protected function _buildHeaders($url,array $parameters = null,$method): array
    {
        return $headers = array(
                    "Authorization: Bearer " . $this->_getBearerToken()
                );
    }

    /**
     * Get Bearer token
     *
     * @link https://dev.twitter.com/docs/auth/application-only-auth
     *
     * @throws RestApiException
     */
    private function _getBearerToken(): string {
        if ($this->_bearersToken === '' || $this->_bearersToken === '0') {
            $token = urlencode($this->_consumerKey) . ':' . urlencode($this->_consumerSecret);
            $token = base64_encode($token);

            $headers = array(
                "Authorization: Basic " . $token
            );

            $options = array (
                CURLOPT_URL => self::TWITTER_API_AUTH_URL,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => "grant_type=client_credentials"
            );

            $response = $this->_callApi($options);

            if (isset($response["token_type"]) && $response["token_type"] == 'bearer') {
                $this->_bearersToken = $response["access_token"];
            } else {
                throw new RestApiException('Error while getting access token');
            }
        }
        return $this->_bearersToken;
    }
}