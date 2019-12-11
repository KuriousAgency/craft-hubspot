<?php
/**
 * Hubspot plugin for Craft CMS 3.x
 *
 * Hubspot plugin for Craft CMS
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\hubspot\models;

use kuriousagency\hubspot\Hubspot;

use Craft;
use craft\base\Model;

/**
 * @author    Kurious Agency
 * @package   Hubspot
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $apiKey = '';
    public $userDescriptionField = '';
    public $jobTitleField = '';
    public $pipeline = '';
    public $dealStage = '';
    public $orderReferenceField = '';
    public $productDescriptionField = '';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['apiKey', 'required'],
        ];
    }
}
