<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     BaseSliderItem
 *
 * @property int    BlockID
 * @property int    SortOrder
 * @property string Title
 * @property string Content
 * @property string AlignX
 * @property string AlignY
 * @property string Style
 *
 * @method SliderBlock Block
 */
class BaseSliderItem extends \DataObject {

    /**
     * @var array
     * @config
     */
    private static $db = [
        'Title'     => 'Varchar',
        'Content'   => 'HTMLText',
        'AlignX'    => 'Enum(array("Left", "Center", "Right"), "Left")',
        'AlignY'    => 'Enum(array("Top", "Middle", "Bottom"), "Middle")',
        'Style'     => 'Enum(array("Dark", "Light"), "Light")',
        'SortOrder' => 'Int',
    ];

    /**
     * @var array
     * @config
     */
    private static $has_one = [
        'Block' => 'SliderBlock',
    ];

    /**
     * Get localized types
     *
     * @return array
     */
    public function getHorizontallyTypes() {
        $types = [];

        foreach ($this->dbObject('AlignX')->enumValues() as $alignType) {
            $types[$alignType] = $this->fieldLabel($alignType);
        }

        return $types;
    }

    /**
     * Get localized types
     *
     * @return array
     */
    public function getVerticallyTypes() {
        $types = [];

        foreach ($this->dbObject('AlignY')->enumValues() as $alignType) {
            $types[$alignType] = $this->fieldLabel($alignType);
        }

        return $types;
    }

    /**
     * Get localized types
     *
     * @return array
     */
    public function getStyles() {
        $types = [];

        foreach ($this->dbObject('Style')->enumValues() as $style) {
            $types[$style] = $this->fieldLabel($style);
        }

        return $types;
    }

    /**
     * @return string
     */
    public function getHorizontalType() {
        return strtolower($this->AlignX);
    }

    /**
     * @return string
     */
    public function getLowerStyle() {
        return strtolower($this->Style);
    }

    /**
     * @return string
     */
    public function getVerticalType() {
        return strtolower($this->AlignY);
    }

    /**
     * The default sort expression. This will be inserted in the ORDER BY
     * clause of a SQL query if no other sort expression is provided.
     *
     * @var string
     * @config
     */
    private static $default_sort = 'SortOrder ASC';

    /**
     * @return string
     */
    public function singular_name() {
        return _t('SliderItem.SINGULARNAME', 'Slider');
    }

    /**
     * @return string
     */
    public function plural_name() {
        return _t('SliderItem.PLURALNAME', 'Sliders');
    }

    /**
     * Default summary fields within localized label title's.
     *
     * @return array
     */
    public function summaryFields() {
        return [
            'Title'      => $this->fieldLabel('Title'),
            'SliderType' => $this->fieldLabel('SliderType'),
        ];
    }

    /**
     * @return string
     */
    public function getSliderType() {
        return $this->singular_name();
    }

    /**
     * @return \FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName(['SortOrder', 'Title', 'Content', 'AlignX', 'AlignY', 'Style']);
        $fields->findOrMakeTab('Root.Media', $this->fieldLabel('Media'));

        $fields->addFieldsToTab('Root.Main', [
            OptionsetField::create('AlignX', $this->fieldLabel('ContentHorizontallyAlignment'), $this->getHorizontallyTypes()),
            OptionsetField::create('AlignY', $this->fieldLabel('ContentVerticallyAlignment'), $this->getVerticallyTypes()),
            OptionsetField::create('Style', $this->fieldLabel('Style'), $this->getStyles()),
            \TextField::create('Title', $this->fieldLabel('Title')),
            $contentField = \HtmlEditorField::create('Content', $this->fieldLabel('Content')),
        ]);

        $contentField->setRows(15);

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    /**
     * @param bool $includeRelations
     *
     * @return array
     */
    public function fieldLabels($includeRelations = true) {
        return array_merge(parent::fieldLabels($includeRelations), [
            'Title'                        => _t('BaseSliderItem.TITLE', 'Title'),
            'Content'                      => _t('BaseSliderItem.CONTENT', 'Content'),
            'Picture'                      => _t('BaseSliderItem.PICTURE', 'Picture'),
            'Media'                        => _t('BaseSliderItem.MEDIA', 'Media'),
            'Video'                        => _t('BaseSliderItem.VIDEO', 'Video'),
            'SliderType'                   => _t('BaseSliderItem.SLIDER_TYPE', 'Slider type'),
            'ContentHorizontallyAlignment' => _t('BaseSliderItem.CONTENT_HORIZONTALLY_ALIGNMENT', 'Content horizontally alignment'),
            'ContentVerticallyAlignment'   => _t('BaseSliderItem.CONTENT_VERTICALLY_ALIGNMENT', 'Content vertically alignment'),
            'Left'                         => _t('BaseSliderItem.LEFT', 'Left'),
            'Center'                       => _t('BaseSliderItem.CENTER', 'Center'),
            'Right'                        => _t('BaseSliderItem.RIGHT', 'Right'),
            'Top'                          => _t('BaseSliderItem.TOP', 'Top'),
            'Middle'                       => _t('BaseSliderItem.MIDDLE', 'Middle'),
            'Bottom'                       => _t('BaseSliderItem.BOTTOM', 'Bottom'),
            'Dark'                         => _t('BaseSliderItem.DARK', 'Dark'),
            'Light'                        => _t('BaseSliderItem.LIGHT', 'Light'),
        ]);
    }

    /**
     * @return \HTMLText
     */
    public function forTemplate() {
        $template = sprintf("%s_%s", $this->Block()->ClassName, $this->ClassName);

        return $this->renderWith($template);
    }

    /**
     * @return false
     */
    public function getSliderImage() {
        return false;
    }

    public function getHeading() {
        $this->extend('updateBeforeHeading', $this->Title);

        return $this->Title;
    }
}