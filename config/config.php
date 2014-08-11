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
