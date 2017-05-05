<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     VideoSliderItem
 *
 * @property int     CoverID
 * @property int     Mp4ID
 * @property int     WebMID
 * @property int     OggID
 * @property string  Type
 * @property string  URL
 * @property boolean AutoPlay
 *
 * @method File Mp4
 * @method File WebM
 * @method File Ogg
 * @method Image Cover
 *
 * @TODO      implement https://github.com/xemle/html5-video-php package to convert webm and ogg videos when saving.
 */
class VideoSliderItem extends BaseSliderItem {

    /**
     * @var array
     * @config
     */
    private static $db = [
        'Type'     => 'Enum(array("Youtube", "Vimeo", "File"), "File")',
        'URL'      => 'Varchar(128)',
        'AutoPlay' => 'Boolean(true)',
    ];

    /**
     * @var array
     * @config
     */
    private static $has_one = [
        'Mp4'   => 'File',
        'WebM'  => 'File',
        'Ogg'   => 'File',
        'Cover' => 'Image',
    ];

    /**
     * Allow to call those functions.
     *
     * @var array
     * @config
     */
    private static $better_buttons_actions = [
        'fetchVideosPicture',
    ];

    /**
     * Set providers default embed link. The key value should
     * be equal within Type field value.
     * {VideoId} - will be replaced with actual video id
     * {AutoPlay} - will be replaced within key of AutoPlay if AutoPlay field is true.
     *
     * @var array
     * @config
     */
    private static $embed_links = [
        "Youtube" => [
            "Link"     => "https://www.youtube.com/embed/{VideoId}{AutoPlay}",
            "AutoPlay" => "?autoplay=1",
        ],
        "Vimeo"   => [
            "Link"     => "https://player.vimeo.com/video/{VideoId}{AutoPlay}",
            "AutoPlay" => "?autoplay=1",
        ],
        // Set your own providers options
    ];

    /**
     * @return string
     */
    public function singular_name() {
        return _t('VideoSliderItem.SINGULARNAME', 'Video slider');
    }

    /**
     * @return string
     */
    public function plural_name() {
        return _t('VideoSliderItem.PLURALNAME', 'Video sliders');
    }

    /**
     * @return array
     */
    public function getSliderTypes() {
        $types = [];

        foreach ($this->dbObject('Type')->enumValues() as $type) {
            $types[$type] = $this->fieldLabel($type);
        }

        return $types;
    }

    /**
     * @return \FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName(['Type', 'Mp4', 'WebM', 'Ogg', 'URL', 'AutoPlay', 'Cover']);
        $fields->findOrMakeTab('Root.Media', $this->fieldLabel('Media'));

        $fields->addFieldsToTab('Root.Media', [
            $coverField = UploadField::create('Cover', $this->fieldLabel('Cover')),
            DropdownField::create('AutoPlay', $this->fieldLabel('TurnOnAutoPlayMode'), BlocksUtility::localized_answers()),
            $videoType = OptionsetField::create('Type', $this->fieldLabel('Type'), $this->getSliderTypes(), 'File'),
            $uploadFieldContainer = DisplayLogicWrapper::create(
                $mp4UploadField = UploadField::create('Mp4', $this->fieldLabel('VideoMp4')),
                $webMUploadField = UploadField::create('WebM', $this->fieldLabel('VideoWebM')),
                $oggUploadField = UploadField::create('Ogg', $this->fieldLabel('VideoOgg'))
            ),
            $urlAddressField = TextField::create('URL', $this->fieldLabel('URLAddress'))->setRightTitle(
                $this->fieldLabel('SetVideoURLAddress')
            ),
        ]);

        $coverField
            ->setAllowedMaxFileNumber(1)
            ->setAllowedFileCategories('image')
            ->setRightTitle(
                _t('VideoSliderItem.SET_VIDEO_COVER_IMAGE', 'Set video cover image')
            )
            ->setFolderName(
                sprintf('%s/Sliders', BaseBlock::config()->upload_directory)
            );

        $mp4UploadField
            ->setAllowedMaxFileNumber(1)
            ->setAllowedExtensions('mp4')
            ->setRightTitle(
                _t('VideoSliderItem.ALLOWED_FILE_EXTENSIONS', 'Allowed file extensions: {extensions}', [
                    'extensions' => '.mp4',
                ])
            )
            ->setFolderName(
                sprintf('%s/Video-Sliders', BaseBlock::config()->upload_directory)
            );

        $webMUploadField
            ->setAllowedMaxFileNumber(1)
            ->setAllowedExtensions('webm')
            ->setRightTitle(
                _t('VideoSliderItem.ALLOWED_FILE_EXTENSIONS', 'Allowed file extensions: {extensions}', [
                    'extensions' => '.webm',
                ])
            )
            ->setFolderName(
                sprintf('%s/Video-Sliders', BaseBlock::config()->upload_directory)
            );

        $oggUploadField
            ->setAllowedMaxFileNumber(1)
            ->setAllowedExtensions('ogg')
            ->setRightTitle(
                _t('VideoSliderItem.ALLOWED_FILE_EXTENSIONS', 'Allowed file extensions: {extensions}', [
                    'extensions' => '.ogg',
                ])
            )
            ->setFolderName(
                sprintf('%s/Video-Sliders', BaseBlock::config()->upload_directory)
            );

        $uploadFieldContainer->displayIf('Type')->isEqualTo('File');
        $urlAddressField->displayIf('Type')->isNotEqualTo('File');

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
            'Type'               => _t('VideoSliderItem.TYPE', 'Type'),
            'Youtube'            => _t('VideoSliderItem.YOUTUBE', 'Youtube'),
            'Vimeo'              => _t('VideoSliderItem.VIMEO', 'Vimeo'),
            'File'               => _t('VideoSliderItem.FILE', 'File'),
            'URLAddress'         => _t('VideoSliderItem.URL_ADDRESS', 'URL address'),
            'SetVideoURLAddress' => _t('VideoSliderItem.SET_VIDEO_URL_ADDRESS', 'Set video URL address'),
            'VideoMp4'           => _t('SliderItem.VIDEO_MP4', 'Video Mp4'),
            'VideoWebM'          => _t('SliderItem.VIDEO_WEBM', 'Video WebM'),
            'VideoOgg'           => _t('SliderItem.VIDEO_OGG', 'Video Ogg'),
            'TurnOnAutoPlayMode' => _t('SliderItem.TURN_ON_AUTO_PLAY_MODE', 'Turn on auto play mode?'),
        ]);
    }

    /**
     * This will get an id of the URL address or false
     * if can't parsed, object type not one of supported
     * providers or just empty url address field.
     *
     * @return string|false
     * @throws ProviderNotFound
     */
    public function getVideoId() {
        if (! empty($this->URL) && $this->Type != 'File') {
            $videoId = BlocksUtility::parse_video_id($this->URL, $this->Type);

            return $videoId;
        }

        return false;
    }

    /**
     * Get embed link by the set of Type field. Method depends by
     * static::$embed_links property.
     *
     * @return bool|string
     */
    public function getEmbedLink() {
        if (! empty($this->URL) && $this->Type != 'File') {
            try {
                $videoId = BlocksUtility::parse_video_id($this->URL, $this->Type);
            } catch (ProviderNotFound $ex) {
                return false;
            }

            if ($videoId && array_key_exists($this->Type, ($options = static::config()->embed_links))) {
                $options = $options[$this->Type];
                $autoPlay = array_key_exists("AutoPlay", $options) && ! empty($options["AutoPlay"]) ? $options["AutoPlay"] : '';

                return str_replace(
                    ['{VideoId}', '{AutoPlay}'],
                    [$videoId, $autoPlay],
                    $options["Link"]
                );
            }
        }

        return false;
    }

    /**
     * @return \ValidationResult
     */
    protected function validate() {
        $validation = parent::validate();

        if (! empty($this->URL) && $this->Type != 'File') {
            try {
                $result = $this->getVideoId();
            } catch (ProviderNotFound $ex) {
                $validation->error($ex->getMessage());

                return $validation;
            }

            // if we can't parse url address, return an error with bad url address or
            // the type is not of the url address providers.
            if (! $result) {
                $validation->error(_t('VideoSliderItem.INVALID_URL_ADDRESS_OR_THE_TYPE', 'Invalid URL address or the type'));
            }
        }

        return $validation;
    }

    /**
     * @return Image|false
     */
    public function getSliderImage() {
        if ($this->Cover()->exists()) {
            return $this->Cover();
        }

        return false;
    }

    /**
     * Creating a button to fetch videos picture if cover image not exists.
     *
     * @return FieldList
     */
    public function getBetterButtonsActions() {
        $fields = parent::getBetterButtonsActions();

        if ($this->Type != 'File' && ! $this->Cover()->exists() && ! empty($this->URL)) {
            $fields->push(BetterButtonCustomAction::create('fetchVideosPicture', _t('VideoSliderItem.FETCH_VIDEOS_PICTURE', 'Fetch videos picture')));
        }

        return $fields;
    }

    /**
     * Fetching/downloading picture from the providers url address and
     * saving as Image object.
     *
     * @return false
     */
    public function fetchVideosPicture() {
        try {
            $videoId = $this->getVideoId();
        } catch (ProviderNotFound $ex) {
            return false;
        }

        $directoryPath = sprintf("%s/Sliders", BaseBlock::config()->upload_directory);
        $folder = Folder::find_or_make($directoryPath);

        if (empty($this->URL)) {
            return false;
        }

        $title = ! empty($this->Title) ? FileNameFilter::create()->filter($this->Title)."-{$this->ID}" : "video-{$this->ID}";
        $fileName = strtolower(sprintf("%s.jpg", $title));
        $baseFolder = Director::baseFolder()."/".$folder->getFilename();

        switch (strtolower($this->Type)) {
            case 'youtube':

                $fileContent = file_get_contents("https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg");

                break;

            case 'vimeo':

                $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/{$videoId}.php"));
                $fileContent = file_get_contents($hash[0]['thumbnail_large']);

                break;
        }

        if ($fileContent) {
            if (file_put_contents($absoluteFileName = ($baseFolder.$fileName), $fileContent)) {
                $image = Image::create([
                    "Filename" => $folder->getFilename().$fileName,
                    "Title"    => $this->Title,
                    "Name"     => $fileName,
                    "ParentID" => $folder->ID,
                    "OwnerID"  => Member::currentUserID(),
                ]);

                if ($image->write()) {
                    $this->CoverID = $image->ID;
                    $this->write();
                }
            }
        }
    }

}