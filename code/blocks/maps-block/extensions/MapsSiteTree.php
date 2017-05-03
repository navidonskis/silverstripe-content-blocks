<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     MapsSiteTree
 *
 */
class MapsSiteTree extends \DataExtension {

    public function MetaTags(&$tags) {
        $api = \GoogleMapField::config()->api;

        if (empty($api)) {
            $api = \SiteConfig::current_site_config()->GoogleMapsApiKey;
        }

        if (! empty($api)) {
            $tags .= "<meta property=\"maps:key\" content=\"{$api}\" />\n";
        }
    }
}