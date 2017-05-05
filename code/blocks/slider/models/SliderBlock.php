<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     SliderBlock
 *
 * @method DataList Sliders
 */
class SliderBlock extends BaseBlock {

    /**
     * @var array
     * @config
     */
    private static $has_many = [
        'Sliders' => 'BaseSliderItem',
    ];

    /**
     * When slider is rendering, load javascript module for the slider.
     *
     * @var bool
     * @config
     */
    private static $load_slider_script = true;

    /**
     * @return string
     */
    public function singular_name() {
        return _t('SliderBlock.SINGULARNAME', 'Slider Block');
    }

    /**
     * @return string
     */
    public function plural_name() {
        return _t('SliderBlock.PLURALNAME', 'Slider Blocks');
    }

    /**
     * @return \FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName(['Content']);
        $fields->findOrMakeTab('Root.Sliders', _t('SliderItem.PLURALNAME', 'Sliders'));

        if ($this->ID) {
            $sliderField = $this->createSliderGridField();
        } else {
            $label = _t('SliderBlock.PLEASE_SAVE_BEFORE_ADD_SLIDERS', 'Please save block before add sliders');
            $sliderField = LiteralField::create("PleaseSaveBeforeAddSliders", "<p class=\"message notice\">{$label}</p>");
        }

        $fields->addFieldToTab('Root.Sliders', $sliderField);

        return $fields;
    }

    /**
     * @return GridField
     */
    protected function createSliderGridField() {
        $config = GridFieldConfig::create()->addComponents(
            new GridFieldToolbarHeader(),
            new GridFieldSortableHeader(),
            new GridFieldDataColumns(),
            new GridFieldPaginator(20),
            new GridFieldDetailForm()
        );

        if ($this->ID) {
            $config->addComponent(new GridFieldOrderableRows('SortOrder'));
        }

        if ($this->canEdit()) {
            $multiClass = new GridFieldAddNewMultiClass();
            $multiClass->setClasses($this->getSliderClasses());

            $config->addComponents(
                $multiClass,
                new GridFieldEditButton()
            );
        }

        if ($this->canDelete()) {
            $config->addComponent(new GridFieldDeleteAction());
        }

        $sliders = new GridField('Sliders', null, $this->Sliders(), $config);

        return $sliders;
    }

    /**
     * Get slider types which are sub classes for BaseSliderItem
     *
     * @return array
     */
    public function getSliderClasses() {
        $classes = ArrayLib::valuekey(ClassInfo::subclassesFor('BaseSliderItem'));
        array_shift($classes);
        foreach ($classes as $k => $v) {
            $classes[$k] = singleton($k)->singular_name();
        }

        return $classes;
    }
}

class SliderBlock_Controller extends Block_Controller {

    public function init() {
        if (SliderBlock::config()->load_slider_script) {
            Requirements::javascript("//cdnjs.cloudflare.com/ajax/libs/lory.js/2.2.0/lory.min.js");
            Requirements::javascript(CONTENT_BLOCKS_DIR."/assets/javascript/universal-slider.js");
        }

        parent::init();
    }

}