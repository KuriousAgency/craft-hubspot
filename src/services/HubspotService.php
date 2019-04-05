<?php
/**
 * Hubspot plugin for Craft CMS 3.x
 *
 * Hubspot plugin for Craft CMS
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

// http://docs.guzzlephp.org/en/stable/quickstart.html#making-a-request

namespace kuriousagency\hubspot\services;

use kuriousagency\hubspot\Hubspot;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

use Craft;
use craft\base\Component;

/**
 * @author    Kurious Agency
 * @package   Hubspot
 * @since     1.0.0
 */
class HubspotService extends Component
{
    // Public Methods
    // =========================================================================

    private $portalId;
    private $apiKey;

    public function init()
    {
        $this->apiKey = Hubspot::$plugin->getSettings()->apiKey;
    }

    public function saveUser(UserModel $user)
    {

        $data = [
            'firstname' => $user->firstName,
            'lastname' => $user->lastName,
            'email' => $user->email,
            'registration_date' => $user->dateCreated,
        ];  
        
        $this->createOrUpdateByEmail($data,$user->email);
      
    }
    
    public function createOrUpdateByEmail($data,$email)
    {
        $url = "https://api.hubapi.com/contacts/v1/contact/createOrUpdate/email/".$email."?hapikey=".$this->apiKey;
        
        // create new guzzle client
		// $guzzleClient = Craft::createGuzzleClient(['timeout' => 120, 'connect_timeout' => 120]);
		$client = new Client(['base_uri'=>$url]);
        // Submit to Hubspot
        try {
            $client->post($url, [ 'json' => $data ]);
        } catch (\Exception $e) {
            Craft::error(
                'Hubspot Error updating email '.$e->getMessage(),
                __METHOD__
            );
        }
	}
	
	public function updateByEmail($data,$email)
    {

        $url = "https://api.hubapi.com/contacts/v1/contact/email/".$email."/profile?hapikey=".$this->apiKey;
        
        // create new guzzle client
		$client = new Client(['base_uri'=>$url]);
		
        // Submit to Hubspot
        try {
			$response = $client->post($url, [ 'json' => $data ]);
        } catch (\Exception $e) {
            Craft::error(
                'Hubspot Error updating contact by email '.$e->getMessage(),
                __METHOD__
            );
        }
	}


    public function unsubscribeByEmail($email)
    {

        $url = "https://api.hubapi.com/email/public/v1/subscriptions/".$email."?hapikey=".$this->apiKey;

		$client = new Client(['base_uri'=>$url]);

        $data['unsubscribeFromAll'] = true;

        try {
            $response = $client->put($url, [ 'json' => $data ]);
        } catch (\Exception $e) {
            Craft::error(
                'Hubspot Error updating email '.$e->getMessage(),
                __METHOD__
            );
        }

    }
    
    public function getBlogPosts()
    {
        "https://api.hubapi.com/content/api/v2/blog-posts?hapikey=".$this->apiKey;

    }

}
