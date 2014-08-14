<?php

/**
 * avatar extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2008-2014, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-avatar
 */

class AvatarWidget extends \AvatarWidgetBase
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'widget_avatar';

    /**
     * Load the database object
     * @param array
     */
    public function __construct($arrAttributes=null)
    {
        $GLOBALS['TL_CSS']['avatar_handler'] = 'system/modules/avatar/assets/handler/handler.min.css';

        // Include jQuery
        $this->addJQuery = true;

        parent::__construct($arrAttributes);

        // Set the value
        if ($this->varValue == '') {
            $this->varValue = \Avatar::find($this->getId(), $this->getUploadPath());
        }
    }

    /**
     * Generate the widget and return it as string
     * @param array
     * @return string
     */
    public function parse($arrAttributes=null)
    {
        if ($this->varValue != '') {
            $blnTemporaryFile = $this->isTemporaryFile($this->varValue);

            // If the file is temporary but has the exact avatar dimensions
            // there is no need to crop it just treat it as a ready avatar
            if ($blnTemporaryFile) {
                $arrSize = @getimagesize(TL_ROOT . '/' . $this->varValue);

                if ($arrSize[0] == $this->arrAvatarSize[0] && $arrSize[1] == $this->arrAvatarSize[1]) {
                    $strNew = $this->getThumbnailPath($this->varValue);

                    // Copy the file
                    if (\Files::getInstance()->rename($this->varValue, $strNew)) {
                        $this->varValue = $strNew;
                        $blnTemporaryFile = false;
                    }
                }
            }

            // Temporary file
            if ($blnTemporaryFile) {

                // Crop the file
                if (\Input::post('crop') != '') {
                    list($intPositionX, $intPositionY) = explode(',', \Input::post('crop'));
                    $this->varValue = $this->cropImage($this->varValue, $intPositionX, $intPositionY);

                    $this->thumbnail = \Image::getHtml($this->varValue);
                    $this->imgSize = @getimagesize(TL_ROOT . '/' . $this->varValue);
                    $this->set = $this->varValue;
                    $this->noCrop = true;
                } else {
                    // Crop mode
                    $strThumbnail = $this->getThumbnail($this->varValue);

                    $this->thumbnail = \Image::getHtml($strThumbnail);
                    $this->imgSize = @getimagesize(TL_ROOT . '/' . $strThumbnail);
                }
            } else {
                // Avatar
                $this->avatar = \Image::getHtml(\Image::get($this->varValue, $this->arrAvatarSize[0], $this->arrAvatarSize[1], 'center_center'));
                $this->set = $this->varValue;
            }
        }

        $this->ajax = \Environment::get('isAjaxRequest');
        $this->delete = $GLOBALS['TL_LANG']['MSC']['delete'];
        $this->deleteTitle = specialchars($GLOBALS['TL_LANG']['MSC']['delete']);
        $this->crop = $GLOBALS['TL_LANG']['MSC']['avatar_crop'];
        $this->cropTitle = specialchars($GLOBALS['TL_LANG']['MSC']['avatar_crop']);
        $this->extensions = json_encode(trimsplit(',', $this->getAllowedExtensions()));
        $this->sizeLimit = $this->getMaximumFileSize();
        $this->avatarSize = json_encode($this->arrAvatarSize);

        $this->texts = json_encode(array
        (
            'text' => array
            (
                'formatProgress' => $GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_formatProgress'],
                'failUpload' => $GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_failUpload'],
                'waitingForResponse' => $GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_waitingForResponse'],
                'paused' => $GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_paused'],
            ),
            'messages' => array
            (
                'tooManyFilesError' => $GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_tooManyFilesError'],
                'unsupportedBrowser' => $GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_unsupportedBrowser'],
            ),
            'retry' => array
            (
                'autoRetryNote' => $GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_autoRetryNote'],
            ),
            'deleteFile' => array
            (
                'confirmMessage' => $GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_confirmMessage'],
                'deletingStatusText' => $GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_deletingStatusText'],
                'deletingFailedText' => $GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_deletingFailedText'],
            ),
            'paste' => array
            (
                'namePromptMessage' => $GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_namePromptMessage'],
            ),
        ));

        $this->labels = array
        (
            'drop' => $GLOBALS['TL_LANG']['MSC']['avatar_drop'],
            'upload' => $GLOBALS['TL_LANG']['MSC']['avatar_upload'],
            'processing' => $GLOBALS['TL_LANG']['MSC']['avatar_processing'],
        );

        return parent::parse($arrAttributes);
    }

    /**
     * Use the parse() method instead
     * @throw \BadMethodCallException
     */
    public function generate()
    {
        throw new \BadMethodCallException('Please use the parse() method instead!');
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    protected function getThumbnailSize()
    {
        return deserialize($this->arrConfiguration['thumbnailSize'], true);
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    protected function getAvatarSize()
    {
        return deserialize($this->arrConfiguration['avatarSize'], true);
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function getAllowedExtensions()
    {
        return $this->arrConfiguration['extensions'];
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function getMaximumFileSize()
    {
        return $this->arrConfiguration['maxlength'];
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function getUploadPath()
    {
        return $this->arrConfiguration['uploadPath'];
    }

    /**
     * {@inheritdoc}
     * @return integer
     */
    protected function getId()
    {
        return $this->objDca->id;
    }
}
