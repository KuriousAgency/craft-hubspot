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
use craft\elements\User;
use craft\helpers\UrlHelper;
use craft\commerce\elements\Order;
use verbb\events\elements\Ticket;

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
    private $settings;

    public function init()
    {
        $this->settings = Hubspot::$plugin->getSettings();
        $this->apiKey = $this->settings->apiKey;
        
    }

    public function saveUser(User $user)
    {

        $dataArray = [
            'firstname' => $user->firstName,
            'lastname' => $user->lastName,
            'email' => $user->email,
            $this->settings->userDescriptionField => $this->_formatField($user->userDescription),
            $this->settings->jobTitleField => $user->jobTitle->label
        ];  
        // Craft::dd($data);
        // custom fields, can it be done dynamically
        foreach($dataArray as $key=>$value) {
            $data['properties'][] = ['property'=>$key,'value'=>$value];
        }
        $response = $this->createOrUpdateByEmail($data,$user->email);
        if ($response) {
            $user->hubspotVid = json_decode($response->getBody())->vid;
            Craft::$app->getElements()->saveElement($user,false);
        }
    }

    public function createDeals(Order $order)
    {
        $url = "https://api.hubapi.com/deals/v1/deal?";
        $params = UrlHelper::buildQuery([
            'hapikey' => $this->apiKey,
        ]);
        $url = $url . $params;
        $client = new Client(['base_uri'=>$url]);

        // Check if events is installed and check if event ticket
        $user = $order->user;
        foreach ($order->lineitems as $lineitem) {
            $data = [
                'associations' => [
                    'associatedVids' => [$user->hubspotVid]
                ],
                'properties' => [
                    [
                        'value' => $user->fullName . ' - ' . $lineitem->description,
                        'name' => 'dealname' 
                    ],
                    [
                        'value' => $this->settings->pipeline,
                        'name' => 'pipeline'
                    ],
                    [
                        'value' => $this->settings->dealStage,
                        'name' => 'dealstage'
                    ],
                    [
                        'value' => $order->reference,
                        'name' => $this->settings->orderReferenceField
                    ],
                    [
                        'value' => $lineitem->description,
                        'name' => $this->settings->productDescriptionField
                    ]
                ]
            ];

            try {
                $response = $client->post($url, [ 'json' => $data ]);
            } catch (\Exception $e) {
                Craft::error(
                    'Hubspot Error updating deal '.$e->getMessage(),
                    __METHOD__
                );
            }

        }
        


    }
    
    public function createOrUpdateByEmail($data,$email)
    {
        $url = "https://api.hubapi.com/contacts/v1/contact/createOrUpdate/email/".$email."?hapikey=".$this->apiKey;
        
        // create new guzzle client
		// $guzzleClient = Craft::createGuzzleClient(['timeout' => 120, 'connect_timeout' => 120]);
		$client = new Client(['base_uri'=>$url]);
        // Submit to Hubspot
        try {
            $response = $client->post($url, [ 'json' => $data ]);
            return $response;
        } catch (\Exception $e) {
            Craft::error(
                'Hubspot Error updating email '.$e->getMessage(),
                __METHOD__
            );
        };
        return null;
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

    // public function getUser($user)
    // {
    //     $url = "https://api.hubapi.com/contacts/v1/contact/email/".$user->email."/profile?";
    //     $params = UrlHelper::buildQuery([
    //         'hapikey' => $this->apiKey,
    //     ]);
    //     $url = $url . $params;
    //     // Craft::dd($url);
    //     $client = new Client(['base_uri'=>$url]);

    //     $response = $client->get($url);
    //     $user->hubspotVid = json_decode($response->getBody())->vid;
    //     Craft::$app->getElements()->saveElement($user, false);
    // }

    private function _formatField($field)
    {
        $array = (array) $field;
        $values = array_column($array,'value');
        return implode(';',$values);
    }
}
