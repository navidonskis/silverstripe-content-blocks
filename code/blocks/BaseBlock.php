<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     BaseBlock
 *
 * @property string  Title
 * @property string  Content
 * @property string  BlockArea
 * @property string  CanViewType
 * @property string  ExtraCSSClasses
 * @property int     Weight
 * @property string  Area
 * @property boolean Published
 */
class BaseBlock extends ContentBlock {

    /**
     * Default directory to upload your files.
     *
     * @var string
     * @config
     */
    protected static $upload_directory = 'Uploads/ContentBlocks';

    /**
     * Include default styles (css) at frontend side?
     *
     * @var bool
     */
    private static $default_styles = true;

    /**
     * Re-add (un)publish images.
     *
     * @return HTMLText
     */
    public function isPublishedIcon() {
        $content = HTMLText::create();
        $images = [
            0 => sprintf('%s/assets/images/cancel.png', CONTENT_BLOCKS_DIR),
            1 => sprintf('%s/assets/images/success.png', CONTENT_BLOCKS_DIR),
        ];

        $content->setValue(sprintf('<img src="%s" />', $images[(int) $this->isPublished()]));

        return $content;
    }

    /**
     * Renders this block with appropriate templates
     * looks for templates that match BlockClassName_AreaName
     * falls back to BlockClassName.
     *
     * Updates: if function will find the template at blocks/ directory,
     * this will also render it.
     *
     * @return string
     **/
    public function forTemplate() {
        if (BaseBlock::config()->default_styles) {
            Requirements::css(sprintf('%s/assets/styles/app.css', CONTENT_BLOCKS_DIR));
        }

        if ($this->BlockArea) {
            $template = [$this->class.'_'.$this->BlockArea];

            if (SSViewer::hasTemplate($template)) {
                return $this->renderWith($template);
            }
        }

        if (SSViewer::hasTemplate($path = "blocks/{$this->ClassName}")) {
            return $this->renderWith($path, $this->getController());
        }

        return $this->renderWith($this->ClassName, $this->getController());
    }

    /**
     * @return bool|string
     */
    public function Link() {
        if ($page = $this->getCurrentPage()) {
            return $page->Link();
        }

        return false;
    }

    /**
     * @return string
     */
    public function getUploadDirectory() {
        return static::config()->upload_directory;
    }

    /**
     * @return string
     */
    public function getBlockName() {
        return strtolower(str_replace(' ', '-', FormField::name_to_label($this->ClassName)));
    }
}