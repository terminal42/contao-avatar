<?php

/**
 * avatar extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2008-2014, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-avatar
 */

abstract class AvatarWidgetBase extends \Widget
{

    /**
     * Submit user input
     * @var boolean
     */
    protected $blnSubmitInput = false;

    /**
     * Thumbnail size
     * @var array
     */
    protected $arrThumbSize = array();

    /**
     * Avatar size
     * @var array
     */
    protected $arrAvatarSize = array();

    /**
     * Temporary upload path
     * @var string
     */
    protected $strTemporaryPath = 'system/tmp';

    /**
     * Temporary thumbnail path
     * @var string
     */
    protected $strThumbnailPath = 'assets/images';

    /**
     * Load the database object
     * @param array
     */
    public function __construct($arrAttributes=null)
    {
        parent::__construct($arrAttributes);

        $this->arrThumbSize = $this->getThumbnailSize();
        $this->arrAvatarSize = $this->getAvatarSize();

        // Include the jQuery
        if ($this->addJQuery) {
            if (defined('JQUERY')) {
                $GLOBALS['TL_JAVASCRIPT']['jquery'] = 'assets/jquery/core/' . JQUERY . '/jquery.min.js';
            } else {
                $GLOBALS['TL_JAVASCRIPT']['jquery'] = 'assets/jquery/js/jquery.min.js';
            }
        }

        $GLOBALS['TL_JAVASCRIPT']['avatar_fineuploader'] = 'system/modules/avatar/assets/fineuploader/fineuploader-5.0.2.min.js';
        $GLOBALS['TL_JAVASCRIPT']['avatar_jcrop'] = 'system/modules/avatar/assets/Jcrop/js/jquery.Jcrop.min.js';
        $GLOBALS['TL_JAVASCRIPT']['avatar_handler'] = 'system/modules/avatar/assets/handler/handler.min.js';
        $GLOBALS['TL_CSS']['avatar_jcrop'] = 'system/modules/avatar/assets/Jcrop/css/jquery.Jcrop.min.css';
    }

    /**
     * Add specific attributes
     * @param string
     * @param mixed
     */
    public function __set($strKey, $varValue)
    {
        switch ($strKey) {
            case 'maxlength':
                // Do not add as attribute (see #3094)
                $this->arrConfiguration['maxlength'] = $varValue;
                break;

            case 'mandatory':
                if ($varValue) {
                    $this->arrAttributes['required'] = 'required';
                } else {
                    unset($this->arrAttributes['required']);
                }
                parent::__set($strKey, $varValue);
                break;

            default:
                parent::__set($strKey, $varValue);
                break;
        }
    }

    /**
     * Validate the upload
     * @return string
     */
    public function validateUpload()
    {
        \Message::reset();
        $strTempName = $this->strName . '_upload';
        $objUploader = new \FileUpload();
        $objUploader->setName($this->strName);

        // Convert the $_FILES array to Contao format
        if (!empty($_FILES[$strTempName])) {
            $strFileName = $_FILES[$strTempName]['name'];
            $strFileName = str_replace(' ', '-', $strFileName);
            $strFileName = str_replace('_', '-', $strFileName);
            $lastDot = strrpos($strFileName, '.');
            $strFileName = str_replace('.', '-', substr($strFileName, 0, $lastDot)) . substr($strFileName, $lastDot);

            $arrFile = array
            (
                'name' => array($strFileName),
                'type' => array($_FILES[$strTempName]['type']),
                'tmp_name' => array($_FILES[$strTempName]['tmp_name']),
                'error' => array($_FILES[$strTempName]['error']),
                'size' => array($_FILES[$strTempName]['size']),
            );

            // Check if the file exists
            if (file_exists(TL_ROOT . '/' . $this->strTemporaryPath . '/' . $arrFile['name'][0])) {
                $arrFile['name'][0] = $this->getFileName($arrFile['name'][0], $this->strTemporaryPath);
            }

            $_FILES[$this->strName] = $arrFile;
            unset($_FILES[$strTempName]); // Unset the temporary file
        }

        $varInput = '';
        $maxlength = null;

        // Override the default maxlength value
        if (isset($this->arrConfiguration['maxlength'])) {
            $maxlength = $GLOBALS['TL_CONFIG']['maxFileSize'];
            $GLOBALS['TL_CONFIG']['maxFileSize'] = $this->getMaximumFileSize();
        }

        try {
            $varInput = $objUploader->uploadTo($this->strTemporaryPath);

            if ($objUploader->hasError()) {
                foreach ($_SESSION['TL_ERROR'] as $strError) {
                    $this->addError($strError);
                }
            }

            \Message::reset();
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
        }

        // Restore the default maxlength value
        if ($maxlength !== null) {
            $GLOBALS['TL_CONFIG']['maxFileSize'] = $maxlength;
        }

        if (!is_array($varInput) || empty($varInput)) {
            $this->addError($GLOBALS['TL_LANG']['MSC']['avatar_error']);
        }

        $varInput = $varInput[0];
        $strExtension = pathinfo($varInput, PATHINFO_EXTENSION);
        $arrAllowedTypes = trimsplit(',', strtolower($this->getAllowedExtensions()));

        // File type not allowed
        if (!in_array(strtolower($strExtension), $arrAllowedTypes)) {
            $this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $strExtension));
        }

        // Check image size
        if (($arrImageSize = @getimagesize(TL_ROOT . '/' . $varInput)) !== false) {

            // Image exceeds maximum image width
            if ($arrImageSize[0] > $GLOBALS['TL_CONFIG']['imageWidth']) {
                $this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['filewidth'], '', $GLOBALS['TL_CONFIG']['imageWidth']));
            }

            // Image exceeds maximum image height
            if ($arrImageSize[1] > $GLOBALS['TL_CONFIG']['imageHeight']) {
                $this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['fileheight'], '', $GLOBALS['TL_CONFIG']['imageHeight']));
            }

            // Image exceeds minimum image width
            if ($arrImageSize[0] < $this->arrAvatarSize[0]) {
                $this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['avatar_width'], $this->arrAvatarSize[0]));
            }

            // Image exceeds minimum image height
            if ($arrImageSize[1] < $this->arrAvatarSize[1]) {
                $this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['avatar_height'], $this->arrAvatarSize[1]));
            }
        }

        return $varInput;
    }

    /**
     * Return the value
     * @param mixed
     * @return mixed
     */
    protected function validator($varInput)
    {
        // Check the mandatoriness
        if ($varInput == '' && $this->mandatory) {
            $this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['mandatory'], $this->strLabel));
            return '';
        }

        $varReturn = $varInput;

        // Delete the file
        if ($varInput == '' && $this->varValue != '') {
            \Files::getInstance()->delete($this->varValue);
        }

        // Move file to the destination folder
        if (is_file(TL_ROOT . '/' . $varInput) && $this->isTemporaryFile($varInput)) {
            $strAvatar = \Avatar::find($this->getId(), $this->getUploadPath());

            // Delete the current avatar
            if ($strAvatar != '') {
                \Files::getInstance()->delete($strAvatar);
            }

            $strNew = $this->getUploadPath() . '/' . $this->getId() . '-' . md5(uniqid('', true)) . '.' . pathinfo($varInput, PATHINFO_EXTENSION);

            if (\Files::getInstance()->rename($varInput, $strNew)) {
                $varReturn = $strNew;
            }
        }

        return $varReturn;
    }

    /**
     * Check if file is temporary
     * @param stirng
     * @return boolean
     */
    protected function isTemporaryFile($strFile)
    {
        if (stripos($strFile, $this->strThumbnailPath) !== false) {
            return true;
        }

        return stripos($strFile, $this->strTemporaryPath) !== false;
    }

    /**
     * Return the thumbnail path for a file
     * @param string
     * @return string
     */
    protected function getThumbnailPath($strFile)
    {
        $strFile = basename($strFile);
        $strFolder = $this->strThumbnailPath . '/' . substr(md5($strFile), 0, 1);

        // Make sure the folder exists
        new \Folder($strFolder);

        if (file_exists(TL_ROOT . '/' . $strFolder . '/' . $strFile)) {
            $strFile = $this->getFileName(basename($strFile), $strFolder); // @todo - basename here?
        }

        return $strFolder . '/' . $strFile;
    }

    /**
     * Get a thumbnail and return it as path
     * @param string
     * @return string
     */
    protected function getThumbnail($strFile)
    {
        if (!is_file(TL_ROOT . '/' . $strFile)) {
            return '';
        }

        list($intWidth, $intHeight) = $this->getThumbnailDimensions($strFile);
        $strNew = $this->getThumbnailPath($strFile);

        // Copy the file
        if (\Files::getInstance()->copy($strFile, $strNew)) {
            $strFile = $strNew;
        }

        return \Image::get($strFile, $intWidth, $intHeight, 'proportional');
    }

    /**
     * Get a thumbnail dimensions as array
     * @param string
     * @return array
     */
    protected function getThumbnailDimensions($strFile)
    {
        if (!is_file(TL_ROOT . '/' . $strFile)) {
            return array();
        }

        $intWidth = $this->arrThumbSize[0];
        $intHeight = $this->arrThumbSize[1];
        $arrSize = @getimagesize(TL_ROOT . '/' . $strFile);

        // Do not enlarge image
        if ($arrSize[0] < $intWidth && $arrSize[1] < $intHeight) {
            $intWidth = $arrSize[0];
            $intHeight = $arrSize[1];
        }

        return array($intWidth, $intHeight);
    }

    /**
     * Get the new file name if it already exists in the folder
     * @param string
     * @param string
     * @return string
     */
    protected function getFileName($strFile, $strFolder)
    {
        if (!file_exists(TL_ROOT . '/' . $strFolder . '/' . $strFile)) {
            return $strFile;
        }

        $offset = 1;
        $pathinfo = pathinfo($strFile);
        $name = $pathinfo['filename'];

        $arrAll = scan(TL_ROOT . '/' . $strFolder);
        $arrFiles = preg_grep('/^' . preg_quote($name, '/') . '.*\.' . preg_quote($pathinfo['extension'], '/') . '/', $arrAll);

        if (is_array($arrFiles)) {
            foreach ($arrFiles as $file) {
                if (preg_match('/__[0-9]+\.' . preg_quote($pathinfo['extension'], '/') . '$/', $file)) {
                    $file = str_replace('.' . $pathinfo['extension'], '', $file);
                    $intValue = intval(substr($file, (strrpos($file, '_') + 1)));

                    $offset = max($offset, $intValue);
                }
            }
        }

        return str_replace($name, $name . '__' . ++$offset, $strFile);
    }

    /**
     * Crop the image and return the file path
     * @param string
     * @param integer
     * @param integer
     * @return string
     */
    protected function cropImage($strFile, $intPositionX, $intPositionY, $intSelectionWidth, $intSelectionHeight)
    {
        $strThumbnail = $this->getThumbnailPath($strFile);
        list($intWidth, $intHeight) = $this->getThumbnailDimensions($strFile);

        // Resize to thumbnail size first
        $sizeConfig = new \Contao\Image\ResizeConfiguration();
        $sizeConfig->setWidth($intWidth);
        $sizeConfig->setHeight($intHeight);
        $sizeConfig->setMode(\Contao\Image\ResizeConfigurationInterface::MODE_PROPORTIONAL);

        $strResizedFile = substr($strFile, 0, strrpos($strFile, '.')) . '-resized' . substr($strFile, strrpos($strFile, '.'));

        $imageService = \System::getContainer()->get('contao.image.image_factory');
        $imageService->create(
            TL_ROOT . '/' . $strFile,
            $sizeConfig,
            TL_ROOT . '/' . $strResizedFile
        );

        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->remove(TL_ROOT . '/' . $strFile);
        $fs->copy(TL_ROOT . '/' . $strResizedFile, TL_ROOT . '/' . $strFile);
        $fs->remove(TL_ROOT . '/' . $strResizedFile);
        unset($strResizedFile);

        $arrGdinfo = gd_info();
        $strGdVersion = preg_replace('/[^0-9\.]+/', '', $arrGdinfo['GD Version']);
        $strNewImage = imagecreatetruecolor($this->arrAvatarSize[0], $this->arrAvatarSize[1]);
        $strExtension = strtolower(pathinfo($strFile, PATHINFO_EXTENSION));

        switch ($strExtension) {
            case 'gif':
                if ($arrGdinfo['GIF Read Support']) {
                    $strSourceImage = imagecreatefromgif(TL_ROOT . '/' . $strFile);
                    $intTranspIndex = imagecolortransparent($strSourceImage);

                    // Handle transparency
                    if ($intTranspIndex >= 0 && $intTranspIndex < imagecolorstotal($strSourceImage)) {
                        $arrColor = imagecolorsforindex($strSourceImage, $intTranspIndex);
                        $intTranspIndex = imagecolorallocate($strNewImage, $arrColor['red'], $arrColor['green'], $arrColor['blue']);
                        imagefill($strNewImage, 0, 0, $intTranspIndex);
                        imagecolortransparent($strNewImage, $intTranspIndex);
                    }
                }
                break;

            case 'jpg':
            case 'jpeg':
                if ($arrGdinfo['JPG Support'] || $arrGdinfo['JPEG Support']) {
                    $strSourceImage = imagecreatefromjpeg(TL_ROOT . '/' . $strFile);
                }
                break;

            case 'png':
                if ($arrGdinfo['PNG Support']) {
                    $strSourceImage = imagecreatefrompng(TL_ROOT . '/' . $strFile);

                    // Handle transparency (GDlib >= 2.0 required)
                    if (version_compare($strGdVersion, '2.0', '>=')) {
                        imagealphablending($strNewImage, false);
                        $intTranspIndex = imagecolorallocatealpha($strNewImage, 0, 0, 0, 127);
                        imagefill($strNewImage, 0, 0, $intTranspIndex);
                        imagesavealpha($strNewImage, true);
                    }
                }
                break;
        }

        // The new image could not be created
        if (!$strSourceImage) {
            imagedestroy($strNewImage);
            \System::log('Image "' . $strFile . '" could not be processed', __METHOD__, TL_ERROR);
            return null;
        }

        imagecopyresampled($strNewImage, $strSourceImage, 0, 0, intval($intPositionX), intval($intPositionY), $this->arrAvatarSize[0], $this->arrAvatarSize[1], $intSelectionWidth, $intSelectionHeight);

        // Create the new image
        switch ($strExtension) {
            case 'gif':
                imagegif($strNewImage, TL_ROOT . '/' . $strThumbnail);
                break;

            case 'jpg':
            case 'jpeg':
                imagejpeg($strNewImage, TL_ROOT . '/' . $strThumbnail, (!$GLOBALS['TL_CONFIG']['jpgQuality'] ? 80 : $GLOBALS['TL_CONFIG']['jpgQuality']));
                break;

            case 'png':
                // Optimize non-truecolor images (see #2426)
                if (version_compare($strGdVersion, '2.0', '>=') && function_exists('imagecolormatch') && !imageistruecolor($strSourceImage)) {
                    // TODO: make it work with transparent images, too
                    if (imagecolortransparent($strSourceImage) == -1) {
                        $intColors = imagecolorstotal($strSourceImage);

                        // Convert to a palette image
                        // @see http://www.php.net/manual/de/function.imagetruecolortopalette.php#44803
                        if ($intColors > 0 && $intColors < 256) {
                            $wi = imagesx($strNewImage);
                            $he = imagesy($strNewImage);
                            $ch = imagecreatetruecolor($wi, $he);
                            imagecopymerge($ch, $strNewImage, 0, 0, 0, 0, $wi, $he, 100);
                            imagetruecolortopalette($strNewImage, false, $intColors);
                            imagecolormatch($ch, $strNewImage);
                            imagedestroy($ch);
                        }
                    }
                }

                imagepng($strNewImage, TL_ROOT . '/' . $strThumbnail);
                break;
        }

        // Destroy the temporary images
        imagedestroy($strSourceImage);
        imagedestroy($strNewImage);

        return $strThumbnail;
    }

    /**
     * Get the thumbnail size
     * @return array
     */
    abstract protected function getThumbnailSize();

    /**
     * Get the avatar size
     * @return array
     */
    abstract protected function getAvatarSize();

    /**
     * Get the allowed extensions
     * @return string
     */
    abstract protected function getAllowedExtensions();

    /**
     * Get the maximum file size
     * @return string
     */
    abstract protected function getMaximumFileSize();

    /**
     * Get the upload path
     * @return string
     */
    abstract protected function getUploadPath();

    /**
     * Get the reference ID
     * @return integer
     */
    abstract protected function getId();
}
