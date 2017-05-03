<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     MapsSiteConfig
 *
 */
class MapsSiteConfig extends \DataExtension {

    /**
     * @var array
     * @config
     */
    private static $db = [
        'GoogleMapsApiKey' => 'Varchar(255)',
    ];

    public function updateCMSFields(FieldList $fields) {
        $fields->addFieldToTab('Root.Main', \TextField::create(
            'GoogleMapsApiKey',
            _t('MapsSiteConfig.GOOGLE_MAPS_API_KEY', 'Google maps API key')
        ));

        parent::updateCMSFields($fields);
    }
}