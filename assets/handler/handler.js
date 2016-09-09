/**
 * avatar extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2008-2014, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-avatar
 */

jQuery.noConflict();

;(function($) {
    "use strict";

    /**
     * Default config
     */
    var config = {
        backend: false
    };

    /**
     * Widget
     * @var object
     */
    var widget;

    /**
     * Field
     * @var object
     */
    var field;

    /**
     * Uploader
     * @var object
     */
    var uploader;

    /**
     * Current value
     * @var string
     */
    var current_value = '';

    /**
     * Backend mode
     * @var boolean
     */
    var backend_mode = false;

    /**
     * Initialize the uploader
     */
    var initUploader = function() {
        uploader = new qq.FineUploader({
            element: widget.find('.upload_container')[0],
            debug: false,
            multiple: false,
            request: {
                endpoint: window.location.href,
                inputName: config.field + '_upload',
                params: {
                    action: 'avatar_upload',
                    name: config.field,
                    REQUEST_TOKEN: config.request_token
                }
            },
            failedUploadTextDisplay: {
                mode: 'custom',
                maxChars: 1000,
                responseProperty: 'error'
            },
            validation: {
                allowedExtensions: config.extensions,
                sizeLimit: config.sizeLimit
            },
            text: {
                formatProgress: config.labels.text.formatProgress,
                failUpload: config.labels.text.failUpload,
                waitingForResponse: config.labels.text.waitingForResponse,
                paused: config.labels.text.paused,
            },
            messages: {
                tooManyFilesError: config.labels.messages.tooManyFilesError,
                unsupportedBrowser: config.labels.messages.unsupportedBrowser,
            },
            retry: {
                autoRetryNote: config.labels.retry.autoRetryNote,
            },
            deleteFile: {
                confirmMessage: config.labels.deleteFile.confirmMessage,
                deletingStatusText: config.labels.deleteFile.deletingStatusText,
                deletingFailedText: config.labels.deleteFile.deletingFailedText,
            },
            paste: {
                namePromptMessage: config.labels.paste.namePromptMessage,
            },
            callbacks: {
                onUpload: function() {
                    if (backend_mode) {
                        AjaxRequest.displayBox(Contao.lang.loading + ' …')
                    }
                },
                onComplete: function(id, name, result) {
                    if (!result.success) {
                        if (backend_mode) {
                            AjaxRequest.hideBox();
                        }

                        return;
                    }

                    current_value = result.file;

                    $.ajax({
                        url: window.location.href,
                        data: {
                            'action': 'avatar_reload',
                            'name': config.field,
                            'value': current_value,
                            'REQUEST_TOKEN': config.request_token
                        },
                        type: 'POST',
                        complete: function(r) {
                            if (backend_mode) {
                                AjaxRequest.hideBox();
                                window.fireEvent('ajax_change');
                            }
                        },
                        success: function(r) {
                            widget.find('.ajax_container').html(r);
                            initCrop();
                            initRemove();
                        }
                    });
                }
            }
        });
    };

    /**
     * Initialize the crop
     */
    var initCrop = function() {
        var crop_api;

        widget.find('.thumbnail').Jcrop({
            allowResize: true,
            allowSelect: false,
            setSelect: [0, 0, config.avatarSize[0], config.avatarSize[1]]
        }, function() {
            crop_api = this;
        });

        widget.find('.crop_link').on('click', function(e) {
            e.preventDefault();
            var coordinates = crop_api.tellSelect();

            $.ajax({
                url: window.location.href,
                data: {
                    'action': 'avatar_reload',
                    'name': config.field,
                    'value': current_value,
                    'crop': coordinates.x + ',' + coordinates.y + ',' + coordinates.w + ',' + coordinates.h,
                    'REQUEST_TOKEN': config.request_token
                },
                type: 'POST',
                beforeSend: function() {
                    if (backend_mode) {
                        AjaxRequest.displayBox(Contao.lang.loading + ' …')
                    }
                },
                complete: function(r) {
                    if (backend_mode) {
                        AjaxRequest.hideBox();
                        window.fireEvent('ajax_change');
                    }
                },
                success: function(r) {
                    widget.find('.ajax_container').html(r);
                    initRemove();
                }
            });
        });
    };

    /**
     * Initialize the remove link
     */
    var initRemove = function() {
        widget.find('.delete_link').on('click', function(e) {
            e.preventDefault();
            remove();
        });
    };

    /**
     * Remove the value
     */
    var remove = function() {
        field.val('');
        widget.find('.work_container').html('');
    };

    /**
     * Initialize the plugin
     * @param object
     */
    $.fn.Avatar = function(options) {
        $.extend(config, options);
        widget = $(this);

        // Initialize the uploader
        initUploader();

        // Set the field
        field = $('#ctrl_' + config.field);

        // Backend mode
        if (config.backend) {
            backend_mode = true;
        }

        // Set the current value
        current_value = field.val();

        // Initialize the remove link
        if (current_value != '') {
            initRemove();
        }

        return this;
    };
})(jQuery);
