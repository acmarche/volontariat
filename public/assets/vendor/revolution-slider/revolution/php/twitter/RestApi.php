<?php
/**
 * @author   Albert Kozlowski <vojant@gmail.com>
 * @license  MIT License
 * @link     https://github.com/vojant/Twitter-php
 */

namespace TwitterPhp;

use Exception;
use \TwitterPhp\Connection\Application;
use \TwitterPhp\Connection\User;

require_once __DIR__ . '/connection/ConnectionAbstract.php';
require_once __DIR__ . '/connection/Application.php';
require_once __DIR__ . '/connection/User.php';

/**
 * Class TwitterRestApiException
 */
class RestApiException extends Exception {};

/**
 * Class RestApi
 * @package TwitterPhp
 */
class RestApi
{
    /**
     * @param string $_consumerKey
     * @param string $_consumerSecret
     * @param null|string $_accessToken
     * @param null|string $_accessTokenSecret
     * @throws TwitterRestApiException
     */
    public function __construct(private $_consumerKey,private $_consumerSecret,private $_accessToken = null,private $_accessTokenSecret = null)
    {
        if (!function_exists('curl_init')) {
            throw new TwitterRestApiException('You must have the cURL extension enabled to use this library');
        }
    }

    /**
     * Connect to Twitter API as application.
     * @link https://dev.twitter.com/docs/auth/application-only-auth
     */
    public function connectAsApplication(): Application
    {
        return new Application($this->_consumerKey,$this->_consumerSecret);
    }

    /**
     * Connect to Twitter API as user.
     * @link https://dev.twitter.com/docs/auth/oauth/single-user-with-examples
     *
     * @throws TwitterRestApiException
     */
    public function connectAsUser(): User
    {
        if (!$this->_accessToken || !$this->_accessTokenSecret) {
            throw new TwitterRestApiException('Missing ACCESS_TOKEN OR ACCESS_TOKEN_SECRET');
        }
        return new User($this->_consumerKey,$this->_consumerSecret,$this->_accessToken,$this->_accessTokenSecret);
    }

}