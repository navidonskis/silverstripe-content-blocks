<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     SliderBlock
 *
 * @property int     SlideSpeed
 * @property boolean EnableMouseEvents
 * @property boolean Infinite
 *
 * @method DataList Sliders
 */
class SliderBlock extends BaseBlock {

    /**
     * @var array
     * @config
     */
    private static $db = [
        'SlideSpeed'        => 'Int(600)',
        'EnableMouseEvents' => 'Boolean(true)',
        'Infinite'          => 'Boolean(true)',
    ];

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
     * @var array
     * @config
     */
    private static $defaults = [
        'SlideSpeed'        => 600,
        'EnableMouseEvents' => true,
        'Infinite'          => true,
    ];

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
        $fields->removeByName(['Content', 'SlideSpeed', 'EnableMouseEvents', 'Infinite']);
        $fields->findOrMakeTab('Root.Sliders', _t('SliderItem.PLURALNAME', 'Sliders'));

        if ($this->ID) {
            $sliderField = $this->createSliderGridField();
        } else {
            $label = _t('SliderBlock.PLEASE_SAVE_BEFORE_ADD_SLIDERS', 'Please save block before add sliders');
            $sliderField = LiteralField::create("PleaseSaveBeforeAddSliders", "<p class=\"message notice\">{$label}</p>");
        }

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('SlideSpeed', _t('SliderBlock.SLIDE_SPEED', 'Slide speed (ms)')),
            DropdownField::create('EnableMouseEvents', _t('SliderBlock.ENABLE_MOUSE_EVENTS', 'Enable mouse events?'), BlocksUtility::localized_answers()),
            DropdownField::create('Infinite', _t('SliderBlock.INFINITE', 'Infinite?'), BlocksUtility::localized_answers()),
        ]);

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

    /**
     * @return array|string
     */
    public function getSliderOptions() {
        return \Convert::raw2att(\Convert::array2json([
            'slideSpeed'        => ! empty($this->SlideSpeed) ? (int) $this->SlideSpeed : 300,
            'enableMouseEvents' => (bool) $this->EnableMouseEvents,
            'infinite'          => (int) $this->Infinite,
        ]));
    }
}

class SliderBlock_Controller extends Block_Controller {

    public function init() {
        if (SliderBlock::config()->load_slider_script) {
//            Requirements::javascript("//cdnjs.cloudflare.com/ajax/libs/lory.js/2.2.0/lory.min.js");
//            Requirements::javascript(CONTENT_BLOCKS_DIR."/assets/javascript/universal-slider.js");
        }

        parent::init();
    }

}