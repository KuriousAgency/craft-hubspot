<?php

namespace kuriousagency\hubspot\migrations;

use Craft;
use craft\db\Migration;

use mmikkel\incognitofield\fields\IncognitoFieldType as IncognitoField;
use craft\elements\User;

/**
 * m191210_121349_add_fields_to_user migration.
 */
class m191210_121349_add_fields_to_user extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Need to create the vid field and add it to customers
        $fieldService = Craft::$app->getFields();
        $vidField = $fieldService->createField([
            'type' => IncognitoField::class,
            'groupId' => 11,
            'name' => 'HubSpot VID',
            'handle' => 'hubspotVid',
            'settings' => [
                'mode' => 'readonly',
                'modeOverride' => '',
                'placeholder' => '',
                'code' => '',
                'multiline' => '',
                'initialRows' => '4',
                'charLimit' => '',
                'columnType' => 'text'
            ]
        ]);
        if(!$fieldService->saveField($vidField)){
			echo "Couldn't save Hubspot Vid Field";
			return false;
        }
        
        // Get user field Layout
        $userFieldLayout = $fieldService->getLayoutByType(User::class);
        $tabs = $userFieldLayout->getTabs();
        $newLayout = [
            'Notes' => [],
            'Customer Info' => []
        ];
        foreach($tabs as $index => $tab) {
            $newLayout[$tab->name] = array_column($tab->fields,'id');
        }
        $newLayout['Notes'][] = $vidField->id;
        $newLayout = $fieldService->assembleLayout($newLayout,[]);
        $newLayout->type = User::class;
        $fieldService->deleteLayoutsByType(User::class);
        if (!Craft::$app->getFields()->saveLayout($newLayout)){
            // If it doesn't work, leave it as we found it
			$fieldService->saveLayout($userFieldLayout);
			return false;
        };
        
        $users = User::find()->groupId(5)->all();
        foreach ($users as $user) {
            if (!$user->hubspotVid) {
                Hubspot::$plugin->hubspot->saveUser($user);
            }
        }
        
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m191210_121349_add_fields_to_user cannot be reverted.\n";
        return false;
    }
}
