<?php

/**
 * avatar extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2008-2014, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-avatar
 */

class Avatar
{

    /**
     * Root path
     * @var string
     */
    protected static $strRootPath = 'assets/avatars';

    /**
     * Member path
     * @var string
     */
    protected static $strMemberPath = 'assets/avatars/members';

    /**
     * User path
     * @var string
     */
    protected static $strUserPath = 'assets/avatars/users';

    /**
     * Get the member avatar
     * @param integer
     * @param integer
     * @param integer
     * @return string
     */
    public static function getMember($intId, $intWidth=null, $intHeight=null)
    {
        $objMember = \MemberModel::findByPk($intId);

        // Use the default size
        if (!$intWidth || !$intHeight) {
            list($intWidth, $intHeight) = static::getMemberSize();
        }

        // Use the Gravatar
        if ($objMember->avatar_gravatar) {
            return static::getGravatar($objMember->email, $intWidth);
        }

        $strFile = static::find($intId, static::getMemberPath($intId));

        // Use placeholder member has no avatar
        if ($strFile == '') {
            if (\Config::get('avatar_member_placeholder') == '') {
                return '';
            }

            $objFile = \FilesModel::findByUuid(\Config::get('avatar_member_placeholder'));

            if ($objFile === null || !is_file(TL_ROOT . '/' . $objFile->path)) {
                return '';
            }

            $strFile = $objFile->path;
        }

        return \Image::get($strFile, $intWidth, $intHeight);
    }

    /**
     * Get the member avatar as HTML string
     * @param integer
     * @param integer
     * @param integer
     * @return string
     */
    public static function getMemberHtml($intId, $intWidth=null, $intHeight=null)
    {
        $strFile = static::getMember($intId, $intWidth, $intHeight);

        if ($strFile == '') {
            return '';
        }

        // Gravatar
        if (stripos($strFile, 'gravatar.com') !== false) {

            // Use the default size
            if (!$intWidth || !$intHeight) {
                list($intWidth, $intHeight) = static::getMemberSize();
            }

            // Do not use $intHeight here as gravatar is always a square
            return '<img src="' . $strFile . '" width="' . $intWidth . '" height="' . $intWidth . '" alt="">';
        }

        return \Image::getHtml($strFile);
    }

    /**
     * Get the member avatar size
     * @return array
     */
    public static function getMemberSize()
    {
        return deserialize(\Config::get('avatar_member_size'), true);
    }

    /**
     * Get the user avatar
     * @param integer
     * @param integer
     * @param integer
     * @return string
     */
    public static function getUser($intId, $intWidth=null, $intHeight=null)
    {
        $objUser = \UserModel::findByPk($intId);

        // Use the default size
        if (!$intWidth || !$intHeight) {
            list($intWidth, $intHeight) = static::getUserSize();
        }

        // Use the Gravatar
        if ($objUser->avatar_gravatar) {
            return static::getGravatar($objUser->email, $intWidth);
        }

        $strFile = static::find($intId, static::getUserPath());

        // Use placeholder user has no avatar
        if ($strFile == '') {
            if (\Config::get('avatar_user_placeholder') == '') {
                return '';
            }

            $objFile = \FilesModel::findByUuid(\Config::get('avatar_user_placeholder'));

            if ($objFile === null || !is_file(TL_ROOT . '/' . $objFile->path)) {
                return '';
            }

            $strFile = $objFile->path;
        }

        return \Image::get($strFile, $intWidth, $intHeight);
    }

    /**
     * Get the user avatar as HTML string
     * @param integer
     * @param integer
     * @param integer
     * @return string
     */
    public static function getUserHtml($intId, $intWidth=null, $intHeight=null)
    {
        $strFile = static::getUser($intId, $intWidth, $intHeight);

        if ($strFile == '') {
            return '';
        }

        // Gravatar
        if (stripos($strFile, 'gravatar.com') !== false) {

            // Use the default size
            if (!$intWidth || !$intHeight) {
                list($intWidth, $intHeight) = static::getUserSize();
            }

            // Do not use $intHeight here as gravatar is always a square
            return '<img src="' . $strFile . '" width="' . $intWidth . '" height="' . $intWidth . '" alt="">';
        }

        return \Image::getHtml($strFile);
    }

    /**
     * Get the user avatar size
     * @return array
     */
    public static function getUserSize()
    {
        return deserialize(\Config::get('avatar_user_size'), true);
    }

    /**
     * Return true if member has avatar
     * @param integer
     * @return boolean
     */
    public static function hasMember($intId)
    {
        return (static::find($intId, static::getMemberPath($intId)) != '') ? true : false;
    }

    /**
     * Return true if user has avatar
     * @param integer
     * @return boolean
     */
    public static function hasUser($intId)
    {
        return (static::find($intId, static::getUserPath()) != '') ? true : false;
    }

    /**
     * Find the avatar in the folder
     * @param integer
     * @param string
     * @return string
     */
    public static function find($intId, $strFolder)
    {
        $strReturn = '';

        foreach (scan(TL_ROOT . '/' . $strFolder) as $file) {
            if (!is_file(TL_ROOT . '/' . $strFolder . '/' . $file)) {
                continue;
            }

            list($id) = explode('-', $file, 2);

            if ($id == $intId) {
                $strReturn = $strFolder . '/' . $file;
                break;
            }
        }

        return $strReturn;
    }

    /**
     * Return the member path
     * @return string
     */
    public static function getMemberPath($intId = 0)
    {
        //For frontend and if user is logged in
        if (!$intId && FE_USER_LOGGED_IN === true) {
            $intId =  \FrontendUser::getInstance()->id;
        }

        //For backend edit member 
        if (!$intId && \Input::get('id')) {
            $intId =  \Input::get('id');
        }

        //Find member's home dir when possible
        $objMember = \MemberModel::findById($intId);
        if($objMember->assignDir) {
            $objMemberFolder = \FilesModel::findByUuid($objMember->homeDir);
            
            if(is_dir($objMemberFolder->path)) {

                //be nice put avatar in own directory
                if (!is_dir($objMemberFolder->path. '/avatar')) {
                    \Files::getInstance()->mkdir($objMemberFolder->path. '/avatar');
                }

                return $objMemberFolder->path. '/avatar';
            }
        }
        
        // Fallback to default path        
        static::initFileSystem();
        return static::$strMemberPath;
    }

    /**
     * Return the user path
     * @return string
     */
    public static function getUserPath()
    {
        static::initFileSystem();
        return static::$strUserPath;
    }

    /**
     * Initialize file system
     */
    public static function initFileSystem()
    {
        if (!is_dir(TL_ROOT . '/' . static::$strRootPath)) {
            \Files::getInstance()->mkdir(static::$strRootPath);
        }

        if (!is_dir(TL_ROOT . '/' . static::$strMemberPath)) {
            \Files::getInstance()->mkdir(static::$strMemberPath);
        }

        if (!is_dir(TL_ROOT . '/' . static::$strUserPath)) {
            \Files::getInstance()->mkdir(static::$strUserPath);
        }
    }

    /**
     * Get the Gravatar URL
     * @param string
     * @param integer
     * @return string
     */
    protected static function getGravatar($strEmail, $intSize)
    {
        return 'http://www.gravatar.com/avatar/' . md5(strtolower($strEmail)) . '?s=' . $intSize;
    }

    /**
     * Dispatch an AJAX request
     * @param string
     * @param \DataContainer
     */
    public function dispatchAjaxRequest($strAction, \DataContainer $dc)
    {
        switch ($strAction) {
            // Upload the file
            case 'avatar_upload':
                $strField = \Input::post('name');
                $arrData = $GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['eval'];

                // The field does not exist
                if (!isset($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField])) {
                    \System::log('Field "' . $strField . '" does not exist in DCA "' . $dc->table . '"', __METHOD__, TL_ERROR);
                    header('HTTP/1.1 400 Bad Request');
                    die('Bad Request');
                }

                $arrData['strTable'] = $dc->table;
                $arrData['id'] = $dc->id;
                $arrData['name'] = $strField;

                $objWidget = new $GLOBALS['BE_FFL']['avatar']($arrData, $dc);
                $strFile = $objWidget->validateUpload();

                if ($objWidget->hasErrors()) {
                    $arrResponse = array('success'=>false, 'error'=>$objWidget->getErrorAsString(), 'preventRetry'=>true);
                } else {
                    $arrResponse = array('success'=>true, 'file'=>$strFile);
                }

                echo json_encode($arrResponse);
                exit; break;

            // Reload the widget
            case 'avatar_reload':
                $intId = \Input::get('id');
                $strField = $dc->field = \Input::post('name');

                // Handle the keys in "edit multiple" mode
                if (\Input::get('act') == 'editAll') {
                    $intId = preg_replace('/.*_([0-9a-zA-Z]+)$/', '$1', $strField);
                    $strField = preg_replace('/(.*)_[0-9a-zA-Z]+$/', '$1', $strField);
                }

                // The field does not exist
                if (!isset($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField])) {
                    \System::log('Field "' . $strField . '" does not exist in DCA "' . $dc->table . '"', __METHOD__, TL_ERROR);
                    header('HTTP/1.1 400 Bad Request');
                    die('Bad Request');
                }

                $objRow = null;
                $varValue = null;

                // Load the value
                if ($intId > 0 && \Database::getInstance()->tableExists($dc->table)) {
                    $objRow = \Database::getInstance()->prepare("SELECT * FROM " . $dc->table . " WHERE id=?")
                                                      ->execute($intId);

                    // The record does not exist
                    if ($objRow->numRows < 1) {
                        \System::log('A record with the ID "' . $intId . '" does not exist in table "' . $dc->table . '"', __METHOD__, TL_ERROR);
                        header('HTTP/1.1 400 Bad Request');
                        die('Bad Request');
                    }

                    $varValue = $objRow->$strField;
                    $dc->activeRecord = $objRow;
                }

                // Call the load_callback
                if (is_array($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['load_callback'])) {
                    foreach ($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['load_callback'] as $callback) {
                        if (is_array($callback)) {
                            $this->import($callback[0]);
                            $varValue = $this->$callback[0]->$callback[1]($varValue, $dc);
                        } elseif (is_callable($callback)) {
                            $varValue = $callback($varValue, $dc);
                        }
                    }
                }

                // Build the attributes based on the "eval" array
                $arrAttribs = $GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['eval'];

                $arrAttribs['id'] = $dc->field;
                $arrAttribs['name'] = $dc->field;
                $arrAttribs['value'] = \Input::post('value', true);
                $arrAttribs['strTable'] = $dc->table;
                $arrAttribs['strField'] = $strField;
                $arrAttribs['activeRecord'] = $dc->activeRecord;

                $objWidget = new $GLOBALS['BE_FFL']['avatar']($arrAttribs);
                echo $objWidget->parse();
                exit; break;
        }
    }

    /**
     * Execute AJAX actions in front end
     * @param array
     */
    public function executeAjaxActions($arrData)
    {
        \Input::setGet('no_ajax', 1); // Avoid circular reference

        switch (\Input::post('action')) {
            // Upload the file
            case 'avatar_upload':
                $arrData['name'] = \Input::post('name');

                $objWidget = new $GLOBALS['TL_FFL']['avatar']($arrData);
                $strFile = $objWidget->validateUpload();

                if ($objWidget->hasErrors()) {
                    $arrResponse = array('success'=>false, 'error'=>$objWidget->getErrorAsString(), 'preventRetry'=>true);
                } else {
                    $arrResponse = array('success'=>true, 'file'=>$strFile);
                }

                echo json_encode($arrResponse);
                exit; break;

            // Reload the widget
            case 'avatar_reload':
                $strField = \Input::post('name');

                $arrAttribs['id'] = $strField;
                $arrAttribs['name'] = $strField;
                $arrAttribs['value'] = \Input::post('value', true);
                $arrAttribs['strField'] = $strField;
                $arrAttribs['activeRecord'] = null;

                $objWidget = new $GLOBALS['TL_FFL']['avatar']($arrAttribs);
                echo $objWidget->parse();
                exit; break;
        }
    }

    /**
     * Replace the avatar insert tags
     * - {{avatar::member_current}} - current member
     * - {{avatar::member_current::100x100}} - current member 100x100
     * - {{avatar::member::ID}} - member ID
     * - {{avatar::member::ID::100x100}} - member ID 100x100
     * - {{avatar::user::ID}} - user ID
     * - {{avatar::user::ID::100x100}} - user ID 100x100
     * @param string
     * @return mixed
     */
    public function replaceInsertTags($strTag)
    {
        $arrTag = explode('::', $strTag);

        if ($arrTag[0] != 'avatar') {
            return false;
        }

        switch ($arrTag[1]) {
            case 'member_current':
                if (!FE_USER_LOGGED_IN) {
                    return false;
                }

                list($width, $height) = $this->parseTagSize($arrTag[2]);
                return static::getMemberHtml(\FrontendUser::getInstance()->id, $width, $height);

            case 'member':
                list($width, $height) = $this->parseTagSize($arrTag[3]);
                return static::getMemberHtml($arrTag[2], $width, $height);

            case 'user':
                list($width, $height) = $this->parseTagSize($arrTag[3]);
                return static::getUserHtml($arrTag[2], $width, $height);
        }

        return false;
    }

    /**
     * Parse the tag size and return the values
     * @param mixed
     * @return array
     */
    protected function parseTagSize($varValue)
    {
        $width = null;
        $height = null;

        if ($varValue != '') {
            list($width, $height) = explode('x', $varValue);
        }

        return array($width, $height);
    }

    /**
     * Load the default value
     * @param mixed
     * @param \DataContainer
     * @return mixed
     */
    public function loadDefaultValue($varValue, \DataContainer $dc)
    {
        if (!$varValue) {
            $varValue = $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['default'];
        }

        return $varValue;
    }
}
