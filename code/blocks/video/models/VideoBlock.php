<?php

/**
 * @author    Donatas Navidonskis <donatas@navidonskis.com>
 * @since     2017
 * @class     VideoBlock
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
 */
class VideoBlock extends BaseBlock {

    /**
     * @var array
     * @config
     */
    private static $db = [
        'Type'     => 'Enum(array("Youtube", "Vimeo", "File"), "File")',
        'URL'      => 'Varchar(1024)',
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
        'renderVideo'
    ];

    /**
     * Load javascript plugin to load all block features. Set to false
     * and add yours. This will load /assets/javascript/video-block.js file.
     *
     * @var bool
     * @config
     */
    private static $load_javascript_plugin = true;

    /**
     * If the singular name is set in a private static $singular_name, it cannot be changed using the translation files
     * for some reason. Fix it by defining a method that handles the translation.
     * @return string
     */
    public function singular_name() {
        return _t('VideoBlock.SINGULARNAME', 'Video Block');
    }

    /**
     * If the plural name is set in a private static $plural_name, it cannot be changed using the translation files
     * for some reason. Fix it by defining a method that handles the translation.
     * @return string
     */
    public function plural_name() {
        return _t('VideoBlock.PLURALNAME', 'Video Blocks');
    }

    /**
     * @return string
     */
    public function getVideoType() {
        return strtolower($this->Type);
    }

    /**
     * @return array
     */
    public function getVideoTypes() {
        $types = [];

        foreach ($this->dbObject('Type')->enumValues() as $type) {
            $types[$type] = $this->fieldLabel($type);
        }

        return $types;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName(['Type', 'Mp4', 'WebM', 'Ogg', 'URL', 'AutoPlay', 'Cover', 'Content']);
        $fields->findOrMakeTab('Root.Media', $this->fieldLabel('Media'));

        $fields->addFieldsToTab('Root.Media', [
            $coverField = UploadField::create('Cover', $this->fieldLabel('Cover')),
            DropdownField::create('AutoPlay', $this->fieldLabel('TurnOnAutoPlayMode'), BlocksUtility::localized_answers()),
            $videoType = OptionsetField::create('Type', $this->fieldLabel('Type'), $this->getVideoTypes(), 'File'),
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
            )->setFolderName(sprintf('%s/covers', BaseBlock::config()->upload_directory));

        $mp4UploadField
            ->setAllowedMaxFileNumber(1)
            ->setAllowedExtensions('mp4')
            ->setRightTitle(
                _t('VideoSliderItem.ALLOWED_FILE_EXTENSIONS', 'Allowed file extensions: {extensions}', [
                    'extensions' => '.mp4',
                ])
            )->setFolderName(sprintf('%s/videos', BaseBlock::config()->upload_directory));

        $webMUploadField
            ->setAllowedMaxFileNumber(1)
            ->setAllowedExtensions('webm')
            ->setRightTitle(
                _t('VideoSliderItem.ALLOWED_FILE_EXTENSIONS', 'Allowed file extensions: {extensions}', [
                    'extensions' => '.webm',
                ])
            )->setFolderName(sprintf('%s/videos', BaseBlock::config()->upload_directory));

        $oggUploadField
            ->setAllowedMaxFileNumber(1)
            ->setAllowedExtensions('ogg')
            ->setRightTitle(
                _t('VideoSliderItem.ALLOWED_FILE_EXTENSIONS', 'Allowed file extensions: {extensions}', [
                    'extensions' => '.ogg',
                ])
            )->setFolderName(sprintf('%s/videos', BaseBlock::config()->upload_directory));

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
        return array_merge(parent::fieldLabels($includeRelations), VideoSliderItem::labels());
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
     * VideoSliderItem::$embed_links property.
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

            if ($videoId && array_key_exists($this->Type, ($options = VideoSliderItem::config()->embed_links))) {
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
    public function getCoverImage() {
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

        if ($this->Type == 'File' && $this->Mp4()->exists() && ! empty(BlocksUtility::whichFFMPEG())) {
            if (! $this->WebM()->exists() || ! $this->Ogg()->exists()) {
                $fields->push(BetterButtonCustomAction::create('renderVideo', _t('VideoBlock.RENDER_HTML5_VIDEO', 'Render HTML5 Video (WebM & Ogg)')));
            }
        }

        return $fields;
    }

    /**
     * @return bool|string
     */
    public function getMp4VideoUrl() {
        $file = $this->Mp4();

        if (! ($file instanceof File) || ! $file->exists()) {
            return false;
        }

        return $file->getAbsoluteURL();
    }

    /**
     * @return bool|string
     */
    public function getWebMVideoUrl() {
        $file = $this->WebM();

        if (! ($file instanceof File) || ! $file->exists()) {
            return false;
        }

        return $file->getAbsoluteURL();
    }

    /**
     * @return bool|string
     */
    public function getOggVideoUrl() {
        $file = $this->Ogg();

        if (! ($file instanceof File) || ! $file->exists()) {
            return false;
        }

        return $file->getAbsoluteURL();
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
        $type = strtolower($this->Type);
        $fileContent = file_get_contents(str_replace('{VideoId}', $videoId, VideoSliderItem::config()->thumbnail_links[$type]));

        switch ($type) {
            case 'vimeo':
                $fileContent = unserialize($fileContent);
                $fileContent = file_get_contents($fileContent[0]['thumbnail_large']);
                break;
            case 'file':
                // get picture from video (via ffmpeg)
                if ($this->Mp4()->exists() && ($fileUrl = $this->getMp4VideoUrl())) {

                }

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

    public function renderVideo() {
        $config = array(
            'ffmpeg.bin' => BlocksUtility::whichFFMPEG(),
            'qt-faststart.bin' => '/usr/local/bin/qt-faststart',
        );
        $html5 = new \Html5Video\Html5Video($config);

        $source = Controller::join_links(
            Director::baseFolder(),
            $this->Mp4()->getFilename()
        );

        $destination = Controller::join_links(
            Director::baseFolder(),
            '/assets/test.webm'
        );

        // target format is the file extension of $targetVideo. One of mp4, webm, or ogg
        $profileName = '720p-hd'; // other profiles are listed in src/Html5Video/profiles
        $html5->convert($source, $destination, $profileName);
    }

    public function getVideoOptions() {
        $options = [];

        if ($this->Type == 'File') {
            $options = [
                'videoTypes' => [
                    'mp4'  => $this->getMp4VideoUrl(),
                    'webm' => $this->getWebMVideoUrl(),
                    'ogg'  => $this->getOggVideoUrl(),
                ],
            ];
        } else {
            $options['embed'] = $this->getEmbedLink();
        }

        $options = array_merge([
            'autoPlay'   => (boolean) $this->AutoPlay,
            'type'       => $this->getVideoType(),
            'coverImage' => (($cover = $this->getCoverImage()) ? $cover->getAbsoluteURL() : false),
        ], $options);

        return Convert::raw2att(Convert::array2json($options));
    }

}

class VideoBlock_Controller extends Block_Controller {

    public function init() {
        if (VideoBlock::config()->load_javascript_plugin) {
            Requirements::javascript(CONTENT_BLOCKS_DIR."/assets/javascript/video-block.js");
        }

        parent::init();
    }

}