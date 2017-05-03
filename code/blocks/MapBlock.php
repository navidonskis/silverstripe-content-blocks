<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     MapBlock
 *
 * @property int    GlobalMarkerID
 * @property string Coordinates
 * @property int    ZoomLevel
 *
 * @method Image GlobalMarker
 * @method DataList Markers
 */
class MapBlock extends BaseBlock {

    /**
     * @var array
     * @config
     */
    private static $db = [
        'Coordinates' => 'Varchar(255)', // center coordinates
        'ZoomLevel'   => 'Int(3)', // zoom level of map
    ];

    /**
     * @var array
     * @config
     */
    private static $has_many = [
        'Markers' => 'Marker',
    ];

    /**
     * @var array
     * @config
     */
    private static $has_one = [
        'GlobalMarker' => 'Image',
    ];

    /**
     * Google Maps styles in string of JSON format. See
     * docs/GOOGLE_MAPS_BLOCK.md how to use it.
     *
     * @var string|null
     * @config
     */
    private static $map_styles = null;

    /**
     * @return string
     */
    public function singular_name() {
        return _t('MapBlock.SINGULARNAME', 'Map Block');
    }

    /**
     * @return string
     */
    public function plural_name() {
        return _t('MapBlock.PLURALNAME', 'Map Blocks');
    }

    /**
     * @return array
     */
    public function getCoordinatesAsOption() {
        $coordinates = [];

        if (! empty($this->Coordinates)) {
            list($lat, $lng) = explode(',', $this->Coordinates);
            $coordinates['center'] = [
                'lat' => (float) $lat,
                'lng' => (float) $lng,
            ];
        }

        return $coordinates;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName(['Coordinates', 'ZoomLevel']);

        $coordinates = $this->getCoordinatesAsOption();

        $fields->addFieldsToTab('Root.Main', [
            $marker = UploadField::create('GlobalMarker', _t('MapBlock.Marker', 'Marker')),
            GoogleMapField::create(
                'GoogleMap',
                $this,
                $this->Markers(),
                [
                    'api' => SiteConfig::current_site_config()->GoogleMapsApiKey,
                    'map' => array_merge(
                        [
                            'zoom' => (int) $this->ZoomLevel,
                        ],
                        $coordinates
                    ),
                ]
            ),
        ], 'Content');

        $marker
            ->setRightTitle(_t('MapBlock.MARKER_RIGHT_TITLE', 'Set a global marker image'))
            ->setAllowedFileCategories('image')
            ->setAllowedMaxFileNumber(1)
            ->setFolderName('Uploads/Blocks/Markers');

        $fields->removeByName(['Content']);

        return $fields;
    }

    /**
     * @return HTMLText
     */
    public function forTemplate() {
        Requirements::javascript(CONTENT_BLOCKS_DIR.'/assets/javascript/maps-frontend.js');

        return parent::forTemplate();
    }

    /**
     * @return bool|string
     */
    public function getMarkersAsJson() {
        if (count($markers = $this->Markers())) {
            $list = [];

            /** @var Marker $marker */
            foreach ($markers as $marker) {
                $markerImage = [];

                if ($marker->Marker()->exists()) {
                    $markerImage = ['icon' => $marker->Marker()->getAbsoluteURL()];
                }

                $markerAsArray = $marker->toMap();

                $list[] = array_merge([
                    'instanceId'    => isset($markerAsArray['InstanceId']) ? $markerAsArray['InstanceId'] : '',
                    'address'       => isset($markerAsArray['Address']) ? $markerAsArray['Address'] : '',
                    'coordinates'   => isset($markerAsArray['Coordinates']) ? $markerAsArray['Coordinates'] : '',
                    'content'       => isset($markerAsArray['Content']) ? $markerAsArray['Content'] : '',
                    'displayWindow' => (bool) (isset($markerAsArray['DisplayWindow']) ? $markerAsArray['DisplayWindow'] : false),
                ], $markerImage);
            }

            return Convert::raw2att(Convert::array2json($list));
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getOptionsAsJson() {
        $data = [];

        if (count($coordinates = $this->getCoordinatesAsOption())) {
            $data['map'] = $coordinates;
        }

        if (! empty($this->ZoomLevel)) {
            if (! isset($data['map'])) {
                $data['map'] = [];
            }

            $data['map']['zoom'] = (int) $this->ZoomLevel;
        }

        if ($this->GlobalMarker()->exists()) {
            $data['globalMarkerIcon'] = $this->GlobalMarker()->getAbsoluteURL();
        }

        $styles = static::config()->map_styles;

        if (! empty($styles)) {
            if (! isset($data['map'])) {
                $data['map'] = [];
            }

            $data['map']['styles'] = (string) $styles;
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = json_decode(json_encode((object) $value), false);
            }
        }

        if (count($data)) {
            return Convert::raw2att(Convert::array2json($data));
        }

        return false;
    }

}