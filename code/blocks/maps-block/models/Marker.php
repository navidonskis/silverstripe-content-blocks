<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     Marker
 *
 * @property int     BlockID
 * @property int     MarkerID
 * @property string  InstanceId
 * @property string  Address
 * @property string  Content
 * @property string  Coordinates
 * @property boolean DisplayWindow
 *
 * @method MapBlock Block
 * @method Image Marker
 */
class Marker extends DataObject {

    /**
     * @var array
     * @config
     */
    private static $db = [
        'InstanceId'    => 'Varchar',
        'Address'       => 'Varchar',
        'Content'       => 'HTMLText',
        'Coordinates'   => 'Varchar(255)',
        'DisplayWindow' => 'Boolean(true)',
    ];

    /**
     * @var array
     * @config
     */
    private static $has_one = [
        'Block'  => 'MapBlock',
        'Marker' => 'Image',
    ];

    /**
     * @return array
     */
    public function summaryFields() {
        return [
            'InstanceId'  => $this->fieldLabel('InstanceId'),
            'Address'     => $this->fieldLabel('Address'),
            'Coordinates' => $this->fieldLabel('Coordinates'),
        ];
    }

    /**
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            $instanceId = TextField::create('InstanceId', $this->fieldLabel('InstanceId')),
            $address = TextField::create('Address', $this->fieldLabel('Address')),
            $content = HtmlEditorField::create('Content', $this->fieldLabel('Content')),
            $coordinates = TextField::create('Coordinates', $this->fieldLabel('Coordinates')),
            $displayWindow = CheckboxField::create('DisplayWindow', $this->fieldLabel('DisplayWindow')),
            $block = DropdownField::create('BlockID', $this->fieldLabel('Block'), MapBlock::get()->map()),
            $marker = UploadField::create('Marker', $this->fieldLabel('Marker')),
        ]);

        $marker
            ->setAllowedFileCategories('image')
            ->setAllowedMaxFileNumber(1)
            ->setFolderName('Uploads/Blocks/Markers');

        $content
            ->setRows(15);

        return $fields;
    }

    /**
     * @param bool $includeRelations
     *
     * @return array
     */
    public function fieldLabels($includeRelations = true) {
        return array_merge(parent::fieldLabels($includeRelations), [
            'InstanceId'    => _t('Marker.INSTANCE_ID', 'Instance Id'),
            'Address'       => _t('Marker.ADDRESS', 'Address'),
            'Content'       => _t('Marker.CONTENT', 'Content'),
            'Coordinates'   => _t('Marker.COORDINATES', 'Coordinates'),
            'DisplayWindow' => _t('Marker.DISPLAY_WINDOW', 'Display content in window popup?'),
            'Block'         => _t('Marker.BLOCK', 'Block'),
            'Marker'        => _t('Marker.MARKER', 'Marker'),
        ]);
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->InstanceId;
    }

    /**
     * @param string $instanceId
     *
     * @return Marker
     */
    public static function getByInstanceId($instanceId) {
        return static::get()->filter('InstanceId', $instanceId)->first();
    }
}