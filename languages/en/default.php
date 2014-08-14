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
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['avatar_error']      = 'Unknown error occured.';
$GLOBALS['TL_LANG']['MSC']['avatar_drop']       = 'Drop files here to upload';
$GLOBALS['TL_LANG']['MSC']['avatar_upload']     = 'Upload a file';
$GLOBALS['TL_LANG']['MSC']['avatar_processing'] = 'Processing dropped files&hellip;';
$GLOBALS['TL_LANG']['MSC']['avatar_crop']       = 'Crop';

/**
 * Fineuploader labels
 */
$GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_formatProgress']        = '{percent}% of {total_size}';
$GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_failUpload']            = 'Upload failed';
$GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_waitingForResponse']    = 'Processing...';
$GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_paused']                = 'Paused';
$GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_tooManyFilesError']     = 'You may only drop one file';
$GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_unsupportedBrowser']    = 'Unrecoverable error - this browser does not permit file uploading of any kind.';
$GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_autoRetryNote']         = 'Retrying {retryNum}/{maxAuto}...';
$GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_confirmMessage']        = 'Are you sure you want to delete {filename}?';
$GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_deletingStatusText']    = 'Deleting...';
$GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_deletingFailedText']    = 'Delete failed';
$GLOBALS['TL_LANG']['MSC']['avatar_fineuploader_namePromptMessage']     = 'Please name this image';

/**
 * Errors
 */
$GLOBALS['TL_LANG']['ERR']['avatar_width']  = 'File height is below the minimum width of %d pixels!';
$GLOBALS['TL_LANG']['ERR']['avatar_height'] = 'File height is below the minimum height of %d pixels!';
