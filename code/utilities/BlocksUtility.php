<?php

/**
 * @author      Donatas Navidonskis <donatas@navidonskis.com>
 * @since       2017
 * @class       BlocksUtility
 * @description Utilities for module of Content Blocks.
 */
class BlocksUtility extends Object {

    /**
     * Patterns to parse video's id on different providers.
     *
     * @var array
     * @config
     */
    private static $patterns = [
        'youtube' => '/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"\'>]+)/',
        'vimeo'   => '/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/',
    ];

    /**
     * Parse video's id of the given URL address.
     *
     * @param string $url      | url address to the video
     * @param string $provider | default: youtube (supports - youtube, vimeo)
     *
     * @return string
     * @throws ProviderNotFound
     */
    public static function parse_video_id($url, $provider = 'youtube') {
        switch (strtolower($provider)) {
            case 'youtube':

                if (preg_match(static::config()->patterns['youtube'], $url, $matches)) {
                    return $matches[1];
                }

                break;

            case 'vimeo':

                if (preg_match(static::config()->patterns['vimeo'], $url, $matches)) {
                    return $matches[3];
                }

                break;
        }

        throw new ProviderNotFound('Provider not found when parsing video id by given url');
    }

    /**
     * Get localized answers example 0 - No, 1 - Yes. This mostly using
     * for drop down fields.
     *
     * @return array
     */
    public static function localized_answers() {
        return [
            0 => _t('BlocksUtility.NO', 'No'),
            1 => _t('BlocksUtility.YES', 'Yes'),
        ];
    }
}