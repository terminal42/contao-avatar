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
 * Register the classes
 */
ClassLoader::addClasses(array
(
    'Avatar'           => 'system/modules/avatar/classes/Avatar.php',
    'FormAvatar'       => 'system/modules/avatar/forms/FormAvatar.php',
    'AvatarWidget'     => 'system/modules/avatar/widgets/AvatarWidget.php',
    'AvatarWidgetBase' => 'system/modules/avatar/widgets/AvatarWidgetBase.php',
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'form_avatar'   => 'system/modules/avatar/templates/forms',
    'widget_avatar' => 'system/modules/avatar/templates/widgets',
));
