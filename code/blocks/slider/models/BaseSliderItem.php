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
        $fields->removeByName(['SortOrder', 'Title', 'Content']);
        $fields->findOrMakeTab('Root.Media', $this->fieldLabel('Media'));

        $fields->addFieldsToTab('Root.Main', [
            \TextField::create('Title', $this->fieldLabel('Title')),
            $contentField = \HtmlEditorField::create('Content', $this->fieldLabel('Content')),
        ]);

        $contentField
            ->setRows(15);

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
            'Title'      => _t('SliderItem.TITLE', 'Title'),
            'Content'    => _t('SliderItem.CONTENT', 'Content'),
            'Picture'    => _t('SliderItem.PICTURE', 'Picture'),
            'Media'      => _t('SliderItem.MEDIA', 'Media'),
            'Video'      => _t('SliderItem.VIDEO', 'Video'),
            'SliderType' => _t('SliderItem.SLIDER_TYPE', 'Slider type'),
        ]);
    }

    /**
     * @return \HTMLText
     */
    public function forTemplate() {
        return $this->renderWith(
            sprintf("%s_%s", $this->Block()->ClassName, $this->ClassName)
        );
    }
}