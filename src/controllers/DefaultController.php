<?php
/**
 * Hubspot plugin for Craft CMS 3.x
 *
 * Hubspot plugin for Craft CMS
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\hubspot\controllers;

use kuriousagency\hubspot\Hubspot;

use Craft;
use craft\web\Controller;
use craft\commerce\Plugin as Commerce;
use craft\helpers\UrlHelper;

use craft\elements\User;

/**
 * @author    Kurious Agency
 * @package   Hubspot
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->renderTemplate('hubspot/index');
    }

    /**
     * @return mixed
     */
    public function actionSaveSettings()
    {
        $result = 'Welcome to the DefaultController actionDoSomething() method';

        return $result;
    }
    
    // public function actionTest()
    // {
    //     $users = User::find()->groupId(5)->all();
    //     foreach ($users as $user) {
    //         if (!$user->hubspotVid) {
    //             Hubspot::$plugin->hubspot->saveUser($user);
    //         }
    //     }
    // }
	
	// public function actionTest()
	// {		
	// 	$order = Commerce::getInstance()->getOrders()->getOrderById(1003);
		
	// 	// get review email
	// 	$reviewPath = "review/".$order->number;
	// 	$url = UrlHelper::siteUrl($reviewPath);

	// 	$data['properties'] = [
	// 		[
	// 			'order_date'
	// 		],
	// 	];

	// 	$dataArray['order_date'] = 1000 * strtotime($order->dateOrdered->format('Y-m-d'));
	// 	$dataArray['order_review_link'] = $url;
	// 	$dataArray['lifecyclestage'] = "customer";

	// 	$data = [];

	// 	foreach($dataArray as $key=>$value) {
	// 		$data['properties'][] = ['property'=>$key,'value'=>$value];
	//    }

	// 	Craft::dd($data);

	// 	Hubspot::$plugin->hubspot->updateByEmail($data,$order->email);

	// 	echo "end";

	// 	Craft::$app->end();

	// }

}
