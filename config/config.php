<?php

/**
 * avatar extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2008-2014, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-avatar
 */

/**
 * Back end form fields
 */
$GLOBALS['BE_FFL']['avatar'] = 'AvatarWidget';

/**
 * Front end form fields
 */
$GLOBALS['TL_FFL']['avatar'] = 'FormAvatar';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['executePostActions'][] = array('Avatar', 'dispatchAjaxRequest');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('Avatar', 'replaceInsertTags');

/**
 * Config defaults
 */
$GLOBALS['TL_CONFIG']['avatar_member_size']         = 'a:2:{i:0;s:3:"100";i:1;s:3:"100";}';
$GLOBALS['TL_CONFIG']['avatar_member_thumb']        = 'a:2:{i:0;s:3:"160";i:1;s:3:"160";}';
$GLOBALS['TL_CONFIG']['avatar_member_extensions']   = 'jpg,jpeg,gif,png';
$GLOBALS['TL_CONFIG']['avatar_member_maxlength']    = 500000;
$GLOBALS['TL_CONFIG']['avatar_user_size']           = 'a:2:{i:0;s:3:"100";i:1;s:3:"100";}';
$GLOBALS['TL_CONFIG']['avatar_user_thumb']          = 'a:2:{i:0;s:3:"160";i:1;s:3:"160";}';
$GLOBALS['TL_CONFIG']['avatar_user_extensions']     = 'jpg,jpeg,gif,png';
$GLOBALS['TL_CONFIG']['avatar_user_maxlength']      = 500000;