<?php
/**
 * Hubspot plugin for Craft CMS 3.x
 *
 * Hubspot plugin for Craft CMS
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\hubspot\assetbundles\hubspot;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Kurious Agency
 * @package   Hubspot
 * @since     1.0.0
 */
class HubspotAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@kuriousagency/hubspot/assetbundles/hubspot/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Hubspot.js',
        ];

        $this->css = [
            'css/Hubspot.css',
        ];

        parent::init();
    }
}
