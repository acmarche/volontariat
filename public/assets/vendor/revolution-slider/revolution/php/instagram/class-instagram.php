<?php 

/**
 * Instagram
 *
 * with help of the API this class delivers all kind of Images from instagram
 *
 * @package    socialstreams
 * @subpackage socialstreams/instagram
 * @author     ThemePunch <info@themepunch.com>
 */

class TP_instagram {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $api_key	Instagram API key.
	 */
	public function __construct(private $api_key)
	{
	}

	/**
	 * Get Instagram Pictures
	 *
	 * @since    1.0.0
	 * @param    string    $user_id 	Instagram User id (not name)
	 */
	public function get_public_photos($search_user_id){
		//call the API and decode the response
		$url = "https://api.instagram.com/v1/users/".$search_user_id."/media/recent?access_token=".$this->api_key."&client_id=".$search_user_id;
		$rsp = json_decode(file_get_contents($url), null, 512, JSON_THROW_ON_ERROR);
		return $rsp->data;
	}

}
?>