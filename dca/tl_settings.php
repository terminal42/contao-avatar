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
 * Extend the tl_settings palette
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace('gdMaxImgHeight;', 'gdMaxImgHeight;
{avatar_legend:hide},
avatar_member_size,avatar_member_thumb,avatar_member_extensions,avatar_member_maxlength,avatar_member_placeholder,
avatar_member_autoresize,avatar_user_size,avatar_user_thumb,avatar_user_extensions,avatar_user_maxlength,
avatar_user_placeholder,avatar_user_autoresize;', $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);

/**
 * Add fields to tl_settings
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_member_size'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['avatar_member_size'],
    'inputType'               => 'text',
    'eval'                    => array('multiple'=>true, 'size'=>2, 'rgxp'=>'digit', 'tl_class'=>'w50'),
    'load_callback' => array
    (
        array('Avatar', 'loadDefaultValue')
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_member_thumb'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['avatar_member_thumb'],
    'inputType'               => 'text',
    'eval'                    => array('multiple'=>true, 'size'=>2, 'rgxp'=>'digit', 'tl_class'=>'w50'),
    'load_callback' => array
    (
        array('Avatar', 'loadDefaultValue')
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_member_extensions'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['avatar_member_extensions'],
    'inputType'               => 'text',
    'eval'                    => array('tl_class'=>'w50'),
    'load_callback' => array
    (
        array('Avatar', 'loadDefaultValue')
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_member_maxlength'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['avatar_member_maxlength'],
    'inputType'               => 'text',
    'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50'),
    'load_callback' => array
    (
        array('Avatar', 'loadDefaultValue')
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_member_placeholder'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['avatar_member_placeholder'],
    'inputType'               => 'fileTree',
    'eval'                    => array('files'=>true, 'filesOnly'=>true, 'fieldType'=>'radio', 'extensions'=>$GLOBALS['TL_CONFIG']['validImageTypes'], 'tl_class'=>'clr'),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_member_autoresize'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['avatar_member_autoresize'],
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'clr m12'),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_user_size'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['avatar_user_size'],
    'inputType'               => 'text',
    'eval'                    => array('multiple'=>true, 'size'=>2, 'rgxp'=>'digit', 'tl_class'=>'clr w50'),
    'load_callback' => array
    (
        array('Avatar', 'loadDefaultValue')
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_user_thumb'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['avatar_user_thumb'],
    'inputType'               => 'text',
    'eval'                    => array('multiple'=>true, 'size'=>2, 'rgxp'=>'digit', 'tl_class'=>'w50'),
    'load_callback' => array
    (
        array('Avatar', 'loadDefaultValue')
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_user_extensions'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['avatar_user_extensions'],
    'inputType'               => 'text',
    'eval'                    => array('tl_class'=>'w50'),
    'load_callback' => array
    (
        array('Avatar', 'loadDefaultValue')
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_user_maxlength'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['avatar_user_maxlength'],
    'inputType'               => 'text',
    'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50'),
    'load_callback' => array
    (
        array('Avatar', 'loadDefaultValue')
    )
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_user_placeholder'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['avatar_user_placeholder'],
    'inputType'               => 'fileTree',
    'eval'                    => array('files'=>true, 'filesOnly'=>true, 'fieldType'=>'radio', 'extensions'=>$GLOBALS['TL_CONFIG']['validImageTypes'], 'tl_class'=>'clr'),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['avatar_user_autoresize'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['avatar_user_autoresize'],
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'clr m12'),
);
