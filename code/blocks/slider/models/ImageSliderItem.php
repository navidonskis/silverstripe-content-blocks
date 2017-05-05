<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     ImageSliderItem
 *
 * @property int PictureID
 *
 * @method Image Picture
 */
class ImageSliderItem extends BaseSliderItem {

    /**
     * @var array
     * @config
     */
    private static $has_one = [
        'Picture' => 'Image',
    ];

    /**
     * @return string
     */
    public function singular_name() {
        return _t('ImageSliderItem.SINGULARNAME', 'Image slider');
    }

    /**
     * @return string
     */
    public function plural_name() {
        return _t('ImageSliderItem.PLURALNAME', 'Image sliders');
    }

    /**
     * @return \FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName(['Picture']);
        $fields->findOrMakeTab('Root.Media', $this->fieldLabel('Media'));

        $fields->addFieldToTab('Root.Media', $uploadField = UploadField::create('Picture', $this->fieldLabel('Picture')));

        $uploadField
            ->setAllowedMaxFileNumber(1)
            ->setAllowedFileCategories('image')
            ->setFolderName(
                sprintf('%s/Sliders', BaseBlock::config()->upload_directory)
            );

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    /**
     * @return Image|false
     */
    public function getSliderImage() {
        if ($this->Picture()->exists()) {
            return $this->Picture();
        }

        return false;
    }
}