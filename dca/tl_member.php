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
 * Extend the tl_member palettes
 */
foreach ($GLOBALS['TL_DCA']['tl_member']['palettes'] as $k => $v) {
    $GLOBALS['TL_DCA']['tl_member']['palettes'][$k] = str_replace('language;', 'language;{avatar_legend},avatar,avatar_gravatar;', $v);
}

/**
 * Add fields to tl_member
 */
$GLOBALS['TL_DCA']['tl_member']['fields']['avatar'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['avatar'],
    'exclude'                 => true,
    'inputType'               => 'avatar',
    'eval'                    => array
    (
        'uploadPath' => \Avatar::getMemberPath(),
        'avatarSize' => \Avatar::getMemberSize(),
        'thumbnailSize' => \Config::get('avatar_user_thumb'),
        'extensions' => \Config::get('avatar_user_extensions'),
        'maxlength' => \Config::get('avatar_user_maxlength'),
        'feEditable' => true,
        'feViewable' => true,
        'feGroup' => 'personal'
    ),
);

$GLOBALS['TL_DCA']['tl_member']['fields']['avatar_gravatar'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['avatar_gravatar'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'personal'),
    'sql'                     => "char(1) NOT NULL default ''"
);
