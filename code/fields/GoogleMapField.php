<?php

/**
 * @author    Donatas Navidonskis <donatas@pixelneat.com>
 * @since     2017
 * @class     GoogleMapField
 *
 */
class GoogleMapField extends FormField {

    /**
     * @var array
     * @config
     */
    private static $allowed_actions = [
        'updateMarkers',
        'deleteMarkers',
        'zoomChanged',
        'coordinatesChanged',
    ];

    /**
     * Google maps api key
     *
     * @var string
     * @config
     */
    private static $api = '';

    /**
     * @var SS_List
     * @config
     */
    protected $markers = null;

    /**
     * @var array
     * @config
     */
    private static $options = [
        'map' => [
            'center' => [
                'lat' => 55.1309504,
                'lng' => 24.5499231,
            ],
        ],
    ];

    /**
     * @var MapBlock
     */
    protected $block;

    /**
     * GoogleMapField constructor.
     *
     * @param string       $name
     * @param MapBlock     $block
     * @param SS_List|null $markers
     * @param array        $options
     */
    public function __construct($name, MapBlock $block, SS_List $markers = null, $options = []) {
        if (array_key_exists('api', $options)) {
            $this->setApi($options['api']);
            unset($options['api']);
        }

        $this->block = $block;
        $this->setMarkers($markers);
        $this->setOptions($options);

        parent::__construct($name);
    }

    /**
     * @return string
     */
    public function getApi() {
        return static::config()->api;
    }

    /**
     * @param string $api
     *
     * @return void
     */
    public function setApi($api) {
        static::config()->api = $api;
    }

    /**
     * @param array $options
     *
     * @return void
     */
    public function setOptions($options = []) {
        if (array_key_exists('map', $options) && is_array($options['map'])) {
            $settings = static::config()->options;
            $settings['map'] = array_merge($settings['map'], $options['map']);

            static::config()->options = $settings;
            unset($options['map']);
        }

        static::config()->options = array_merge(
            static::config()->options,
            [
                'translations' => $this->getTranslations(),
                'sprite'       => Controller::join_links(
                    Director::absoluteBaseURL(),
                    CONTENT_BLOCKS_DIR,
                    '/assets/images/sprite.png'
                ),
            ],
            $options
        );
    }

    /**
     * @return array
     */
    public function getTranslations() {
        return [
            'close'       => _t('GoogleMapField.CLOSE', 'Close'),
            'address'     => _t('GoogleMapField.TYPE_AN_ADDRESS', 'Type an address'),
            'editMarker'  => _t('GoogleMapField.EDIT_MARKER', 'Edit marker'),
            'displayInfo' => _t('GoogleMapField.DISPLAY_INFO', 'Display content info window?'),
            'content'     => _t('GoogleMapField.CONTENT', 'Content'),
            'delete'      => _t('GoogleMapField.DELETE', 'Delete'),
            'save'        => _t('GoogleMapField.SAVE', 'Save'),
        ];
    }

    /**
     * @return string
     */
    public function getOptions() {
        return Convert::raw2att(Convert::array2json(
            static::array_keys_to_objects(static::config()->options)
        ));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private static function array_keys_to_objects(array $data = []) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = json_decode(json_encode((object) $value), false);
            }
        }

        return $data;
    }

    /**
     * @param SS_List $markers
     *
     * @return void
     */
    public function setMarkers(SS_List $markers) {
        $this->markers = $markers;
    }

    /**
     * @return bool|string
     */
    public function getMarkers() {
        if (count($markers = $this->markers->toNestedArray())) {
            $markers = array_map(function ($marker) {
                return [
                    'instanceId'    => isset($marker['InstanceId']) ? $marker['InstanceId'] : '',
                    'address'       => isset($marker['Address']) ? $marker['Address'] : '',
                    'coordinates'   => isset($marker['Coordinates']) ? $marker['Coordinates'] : '',
                    'content'       => isset($marker['Content']) ? $marker['Content'] : '',
                    'displayWindow' => (bool) (isset($marker['DisplayWindow']) ? $marker['DisplayWindow'] : false),
                ];
            }, $markers);

            return Convert::raw2att(Convert::array2json($markers));
        }

        return false;
    }

    public function deleteMarkers(SS_HTTPRequest $request) {
        $data = $request->getVars();

        if (isset($data['InstanceId']) && (($marker = $this->block->Markers()->filter('InstanceId', $data['InstanceId'])) && $marker->exists())) {
            $marker = $marker->first();
            $marker->delete();
        }
    }

    public function updateMarkers(SS_HTTPRequest $request) {
        $data = $request->getVars();

        if (array_key_exists('InstanceId', $data)) {
            $marker = $this->block->Markers()->filter('InstanceId', $data['InstanceId']);

            if ($marker->exists()) {
                $marker = $marker->first();

                foreach ($data as $field => $value) {
                    if ($marker->hasField($field)) {
                        $marker->setField($field, $value);
                    }
                }

                $marker->write();
            } else {
                $marker = Marker::create(array_merge($data, [
                    'BlockID' => $this->block->ID,
                ]));
                $marker->write();

                $this->block->Markers()->add($marker);
            }
        }
    }

    public function zoomChanged(SS_HTTPRequest $request) {
        $data = $request->getVars();

        if (array_key_exists('zoom', $data)) {
            $zoomLevel = (int) $data['zoom'];
            $this->block->ZoomLevel = $zoomLevel;
            $this->block->write();
        }
    }

    public function coordinatesChanged(SS_HTTPRequest $request) {
        $data = $request->getVars();

        if (array_key_exists('coordinates', $data)) {
            $this->block->Coordinates = $data['coordinates'];
            $this->block->write();
        }
    }
}