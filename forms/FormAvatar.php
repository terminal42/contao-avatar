<?php

/**
 * avatar extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2008-2014, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-avatar
 */

class FormAvatar extends \AvatarWidgetBase
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'form_avatar';

    /**
     * Values are already prepared
     * @var boolean
     */
    protected $blnValuesPrepared = false;

    /**
     * Load the database object
     * @param array
     */
    public function __construct($arrAttributes=null)
    {
        // Execute the AJAX actions in front end
        if (\Environment::get('isAjaxRequest') && \Input::get('no_ajax') != 1) {
            $objHandler = new \Avatar();
            $objHandler->executeAjaxActions($this->arrConfiguration);
            return;
        }

        $GLOBALS['TL_CSS']['avatar_fineuploader'] = 'system/modules/avatar/assets/fineuploader/fineuploader-5.0.2.min.css';

        parent::__construct($arrAttributes);

        // Set the value
        if ($this->varValue == '') {
            $this->varValue = \Avatar::find($this->getId(), $this->getUploadPath());
        }
    }

    /**
     * Store the file information in the session
     * @param mixed
     * @return mixed
     */
    protected function validator($varInput)
    {
        $varReturn = parent::validator($varInput);
        $arrReturn = array_filter((array) $varReturn);
        $intCount = 0;

        foreach ($arrReturn as $varFile) {
            // Get the file model
            if (\Validator::isBinaryUuid($varFile)) {
                $objModel = \FilesModel::findByUuid($varFile);

                if ($objModel === null) {
                    continue;
                }

                $varFile = $objModel->path;
            }

            $objFile = new \File($varFile, true);

            $_SESSION['FILES'][$this->strName . '_' . $intCount++] = array
            (
                'name'     => $objFile->path,
                'type'     => $objFile->mime,
                'tmp_name' => TL_ROOT . '/' . $objFile->path,
                'error'    => 0,
                'size'     => $objFile->size,
                'uploaded' => true,
                'uuid'     => ($objModel !== null) ? \String::binToUuid($objFile->uuid) : ''
            );
        }

        return $varReturn;
    }

    /**
     * Generate the widget and return it as string
     * @param array
     * @return string
     */
    public function parse($arrAttributes=null)
    {
        if ($this->blnValuesPrepared) {
            return parent::parse($arrAttributes);
        }

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

        $this->labels = array
        (
            'drop' => $GLOBALS['TL_LANG']['MSC']['avatar_drop'],
            'upload' => $GLOBALS['TL_LANG']['MSC']['avatar_upload'],
            'processing' => $GLOBALS['TL_LANG']['MSC']['avatar_processing'],
        );

        $this->blnValuesPrepared = true;

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
        return deserialize(\Config::get('avatar_member_thumb'), true);
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    protected function getAvatarSize()
    {
        return \Avatar::getMemberSize();
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function getAllowedExtensions()
    {
        return \Config::get('avatar_member_extensions');
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function getMaximumFileSize()
    {
        return \Config::get('avatar_member_maxlength');
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function getUploadPath()
    {
        return \Avatar::getMemberPath();
    }

    /**
     * {@inheritdoc}
     * @return integer
     */
    protected function getId()
    {
        return \FrontendUser::getInstance()->id;
    }
}
