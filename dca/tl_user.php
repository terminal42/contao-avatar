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
 * Extend the tl_user palettes
 */
foreach ($GLOBALS['TL_DCA']['tl_user']['palettes'] as $k => $v) {
    $GLOBALS['TL_DCA']['tl_user']['palettes'][$k] = str_replace('email;', 'email;{avatar_legend},avatar,avatar_gravatar;', $v);
}

/**
 * Add fields to tl_user
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['avatar'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_user']['avatar'],
    'exclude'                 => true,
    'inputType'               => 'avatar',
    'eval'                    => array
    (
        'uploadPath' => \Avatar::getUserPath(),
        'avatarSize' => \Avatar::getUserSize(),
        'thumbnailSize' => \Config::get('avatar_user_thumb'),
        'extensions' => \Config::get('avatar_user_extensions'),
        'maxlength' => \Config::get('avatar_user_maxlength'),
    )
);

$GLOBALS['TL_DCA']['tl_user']['fields']['avatar_gravatar'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_user']['avatar_gravatar'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'sql'                     => "char(1) NOT NULL default ''"
);
