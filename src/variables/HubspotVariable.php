<?php
/**
 * Hubspot plugin for Craft CMS 3.x
 *
 * Hubspot plugin for Craft CMS
 *
 * @link      https://kurious.agency
 * @copyright Copyright (c) 2018 Kurious Agency
 */

namespace kuriousagency\hubspot\variables;

use kuriousagency\hubspot\Hubspot;

use Craft;

/**
 * @author    Kurious Agency
 * @package   Hubspot
 * @since     1.0.0
 */
class HubspotVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @param null $optional
     * @return string
     */
    public function exampleVariable($optional = null)
    {
        $result = "And away we go to the Twig template...";
        if ($optional) {
            $result = "I'm feeling optional today...";
        }
        return $result;
    }
}
