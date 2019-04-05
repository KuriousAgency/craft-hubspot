<?php
/**
 * Hubspot plugin for Craft CMS 3.x
 *
 * Hubspot plugin for Craft CMS
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\hubspot;

use kuriousagency\hubspot\services\HubspotService;
use kuriousagency\hubspot\variables\HubspotVariable;
use kuriousagency\hubspot\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use craft\elements\User as UserElement;
use craft\events\UserEvent;
use craft\helpers\UrlHelper;


use craft\commerce\elements\Order;
use yii\base\Event;

/**
 * Class Hubspot
 *
 * @author    Kurious Agency
 * @package   Hubspot
 * @since     1.0.0
 *
 * @property  HubspotService $hubspotService
 */
class Hubspot extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Hubspot
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'hubspot' => HubspotService::class,
        ]);

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'hubspot/default';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'hubspot/default/do-something';
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('hubspot', HubspotVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'hubspot',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
		);
		
		Event::on(Order::class, Order::EVENT_AFTER_COMPLETE_ORDER, function(Event $e) {

			$order = $e->sender;

			// get review email
			$reviewPath = "review/".$order->number;
			$url = UrlHelper::siteUrl($reviewPath);

			$dataArray['order_date'] = 1000 * strtotime($order->dateOrdered->format('Y-m-d'));
			$dataArray['order_review_link'] = $url;
			$dataArray['lifecyclestage'] = "customer";
	
			$data = [];
	
			foreach($dataArray as $key=>$value) {
				 $data['properties'][] = ['property'=>$key,'value'=>$value];
			}

			Hubspot::$plugin->hubspot->updateByEmail($data,$order->email);

		});

        // Event::on(UserElement::class, UserElement::EVENT_AFTER_SAVE,function(Event $event) {

        //     $user = $event->sender;  

        //     if($user->subscribe) {
        //         Hubspot::$plugin->hubspot->saveUser($user);
        //     } else {
        //         Hubspot::$plugin->hubspot->unsubscribeByEmail($user->email);
        //     }
		// });
		
		// reviews

    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'hubspot/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
