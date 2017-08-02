<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     ContentImageBlock
 *
 * @property string Template
 *
 * @method \DataList Images
 */
class ContentImageBlock extends BaseBlock {

    /**
     * @var array
     * @config
     */
    private static $db = [
        'Template' => 'Enum(array(
            "BottomImageTopContent",
            "TopImageBottomContent",
            
            "LeftImageRightContentWrap",
            "LeftBiggerImageRightContentWrap",
            "RightImageLeftContentWrap",
            "RightBiggerImageLeftContentWrap",

            "LeftImageRightContent",
            "LeftBiggerImageRightContent",
            "RightImageLeftContent",
            "RightBiggerImageLeftContent",

            "BottomImageListTopContent",
            "TopImageListBottomContent",

            "FullWidthImageLeftContent"
        ), "LeftBiggerImageRightContent")',
    ];

    /**
     * @var array
     * @config
     */
    private static $many_many = [
        'Images' => 'Image',
    ];

    /**
     * @var array
     * @config
     */
    private static $many_many_extraFields = [
        'Images' => ['SortOrder' => 'Int'],
    ];

    /**
     * If the singular name is set in a private static $singular_name, it cannot be changed using the translation files
     * for some reason. Fix it by defining a method that handles the translation.
     * @return string
     */
    public function singular_name() {
        return _t('ContentImageBlock.SINGULARNAME', 'Content Image Block');
    }

    /**
     * If the plural name is set in a private static $plural_name, it cannot be changed using the translation files
     * for some reason. Fix it by defining a method that handles the translation.
     * @return string
     */
    public function plural_name() {
        return _t('ContentImageBlock.PLURALNAME', 'Content Image Blocks');
    }

    /**
     * Get template types as lowercase and dashed string.
     *
     * @param string $currentType
     *
     * @return array|string
     */
    public function getTemplateTypes($currentType = null) {
        $templates = (array) $this->dbObject('Template')->enumValues();
        $types = [];
        $fileSource = sprintf('%s/assets/images/content-image-block', CONTENT_BLOCKS_DIR);

        foreach ($templates as $type) {
            $types[$type] = sprintf('%s/%s.png', $fileSource, str_replace(' ', '-', strtolower(\FormField::name_to_label($type))));
        }

        $this->extend('updateTemplateTypes', $types);

        return $currentType !== null && array_key_exists($currentType, $types) ? $types[$currentType] : $types;
    }

    /**
     * Get current template type as lowercase and dashed string.
     *
     * @return string
     */
    public function getTemplateType() {
        return $this->getTemplateTypes($this->Template);
    }

    /**
     * @return \FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName(['Template', 'Images']);

        $fields->findOrMakeTab('Root.Template', $this->fieldLabel('Template'));
        $fields->findOrMakeTab('Root.Images', $this->fieldLabel('Images'));

        $fields->addFieldsToTab('Root.Template', [
            \OptionsetField::create('Template', $this->fieldLabel('ChooseTemplate'), $this->getTemplateOptions(), $this->Template)->addExtraClass('content-image-block-cms'),
        ]);

        $fields->addFieldsToTab('Root.Images', [
            \SortableUploadField::create('Images', $this->fieldLabel('Images'))
                                ->setAllowedFileCategories('image')
                                ->setFolderName($this->getUploadDirectory()),
        ]);

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
            'Template'                        => _t("ContentImageBlock.TEMPLATE", "Template"),
            'Images'                          => _t("ContentImageBlock.IMAGES", "Images"),
            'ChooseTemplate'                  => _t("ContentImageBlock.CHOOSE_TEMPLATE", "Choose a template"),
            "BottomImageTopContent"           => _t("ContentImageBlock.BOTTOM_IMAGE_TOP_CONTENT", "Bottom image top content"),
            "TopImageBottomContent"           => _t("ContentImageBlock.TOP_IMAGE_BOTTOM_CONTENT", "Top image bottom content"),
            "LeftImageRightContentWrap"       => _t("ContentImageBlock.LEFT_IMAGE_RIGHT_CONTENT_WRAP", "Left image right content wrap"),
            "LeftBiggerImageRightContentWrap" => _t("ContentImageBlock.LEFT_BIGGER_IMAGE_RIGHT_CONTENT_WRAP", "Left bigger image right content wrap"),
            "RightImageLeftContentWrap"       => _t("ContentImageBlock.RIGHT_IMAGE_LEFT_CONTENT_WRAP", "Right image left content wrap"),
            "RightBiggerImageLeftContentWrap" => _t("ContentImageBlock.RIGHT_BIGGER_IMAGE_LEFT_CONTENT_WRAP", "Right bigger image left content wrap"),
            "LeftImageRightContent"           => _t("ContentImageBlock.LEFT_IMAGE_RIGHT_CONTENT", "Left image right content"),
            "LeftBiggerImageRightContent"     => _t("ContentImageBlock.LEFT_BIGGER_IMAGE_RIGHT_CONTENT", "Left bigger image right content"),
            "RightImageLeftContent"           => _t("ContentImageBlock.RIGHT_IMAGE_LEFT_CONTENT", "Right image left content"),
            "RightBiggerImageLeftContent"     => _t("ContentImageBlock.RIGHT_BIGGER_IMAGE_LEFT_CONTENT", "Right bigger image left content"),
            "BottomImageListTopContent"       => _t("ContentImageBlock.BOTTOM_IMAGE_LIST_TOP_CONTENT", "Bottom image list top content"),
            "TopImageListBottomContent"       => _t("ContentImageBlock.TOP_IMAGE_LIST_BOTTOM_CONTENT", "Top image list bottom content"),
            "FullWidthImageLeftContent"       => _t("ContentImageBlock.FULL_WIDTH_IMAGE_LEFT_CONTENT", "Full width image left content"),
        ]);
    }

    /**
     * @return array
     */
    protected function getTemplateOptions() {
        $options = [];

        foreach ($this->getTemplateTypes() as $type => $fileName) {
            if (\Director::fileExists($fileName)) {
                $thumbnail = "<img src=\"{$fileName}\" title=\"{$this->fieldLabel($type)}\" class=\"content-image-block-cms__thumbnail--picture\" />";
                $content = "<div class=\"content-image-block-cms__thumbnail\">{$thumbnail}</div>";
                $content .= "<p class=\"content-image-block-cms__thumbnail--right-title\">{$this->fieldLabel($type)}</p>";

                $options[$type] = \DBField::create_field("HTMLText", $content);
            }
        }

        $this->extend('updateTemplateOptions', $options);

        return $options;
    }

    /**
     * @return \HTMLText
     */
    public function forTemplate() {
        if (BaseBlock::config()->default_styles) {
            \Requirements::css(sprintf('%s/assets/styles/app.css', CONTENT_BLOCKS_DIR));
        }

        return $this->renderWith($this->ClassName, [
            'Layout' => $this->renderWith("{$this->ClassName}_{$this->Template}"),
        ]);
    }

    /**
     * @return bool|\Image
     */
    public function getFirstImage() {
        $image = $this->Images()->sort('SortOrder', 'ASC')->first();

        return $image instanceof Image ? $image : false;
    }

}