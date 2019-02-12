;(function ( $, window, document, undefined ) {

    "use strict";

    var pluginName = "chameleonJqueryFileUploader",
        defaults = {
            chunkSize: 100000,
            maxUploadSize: 1000000,
            allowedFileTypes: [],
            uploadUrl: null,
            singleMode: false,
            queueCompleteCallback: function(){},
            fileUploadSuccessCallback: function(data){}
        };


    function Plugin ( element, options ) {
        this.element = element;
        this.settings = $.extend( {}, defaults, options );
        this._defaults = defaults;
        this._name = pluginName;
        this._queueComplete = true;
        this.init();
    }


    $.extend(Plugin.prototype, {
        init: function () {

            var fileTypeRegEx = '/.*/';
            if(this.settings.allowedFileTypes.length > 0) {
                var fileTypePattern = '(\.|\/)(' + this.settings.allowedFileTypes.join('|') + ')$';
                fileTypeRegEx = new RegExp(fileTypePattern, 'i');
                $('input[type=file]', this.element).attr('accept', '.' + this.settings.allowedFileTypes.join(',.'));
            }

            var singleMode = false;
            if(this.settings.singleMode === true) {
                singleMode = true;
            }

            $(this.element).fileupload({
                maxChunkSize: this.settings.chunkSize,
                maxFileSize: this.settings.maxUploadSize,
                acceptFileTypes: fileTypeRegEx,
                url: this.settings.uploadUrl,
                filesContainer: $('.file-container', this.element),
                dropZone: $('.dropzone', this.element),
                sequentialUploads: true,
                singleFileMode: singleMode,
                messages: {
                    maxNumberOfFiles: CHAMELEON.CORE.i18n.Translate('chameleon_system_core.universal_uploader.too_many_files'),
                    acceptFileTypes: CHAMELEON.CORE.i18n.Translate('chameleon_system_core.universal_uploader.invalid_file_type'),
                    maxFileSize: CHAMELEON.CORE.i18n.Translate('chameleon_system_core.universal_uploader.file_to_large'),
                    minFileSize: CHAMELEON.CORE.i18n.Translate('chameleon_system_core.universal_uploader.file_to_small')
                },
                uploadTemplate: function (o) {
                    var rows = $();
                    $.each(o.files, function (index, file) {

                        var row = o.options.filesContainer.parent().find('.queue-header').clone();
                        row.removeClass('queue-header').addClass('queue-element');

                        row.addClass('template-upload fade show');
                        row.find('.filename').text(file.name);
                        row.find('.size').text(o.formatFileSize(file.size));
                        row.find('.state').html('<div class="progress"></div>' +
                        (!index ? '<a class="cancel" title="Abbrechen"><i class="far fa-times-circle"></i></a>' : ''));
                        if (file.error) {
                            row.append('<div class="error-container"><div class="alert alert-danger">' + file.error + '</div></div>');
                        }
                        rows = rows.add(row);
                    });

                    return rows;
                },
                downloadTemplate: function (o) {
                    var rows = $();
                    $.each(o.files, function (index, file) {
                        var row = o.options.filesContainer.parent().find('.queue-header').clone();
                        row.removeClass('queue-header').addClass('queue-element');

                        row.addClass('template-download fade');
                        row.find('.filename').text(file.name);
                        row.find('.size').text(o.formatFileSize(file.size));
                        row.find('.state').html('<i class="fas fa-check"></i>');
                        if (file.error) {
                            row.append('<div class="error-container"><div class="alert alert-danger">' + file.error + '</div></div>');
                        }
                        rows = rows.add(row);
                    });

                    return rows;
                }
            });

            this.initDropZoneEffects();
            this.initAdditionalFormFields();
            this.initCallbacks();
        },
        initCallbacks: function() {
            var that = this;

            $(that.element).bind('fileuploadstart', function (e, data) {
                that._queueComplete = false;

            });

            $(that.element).bind('fileuploaddone', function (e, data) {
                $(data._response.result.files).each(function(){
                    var dataForCallback = this.recordId;
                    that.settings.fileUploadSuccessCallback(dataForCallback);
                });

                if(that._queueComplete === true) { //set by fileuploadprogressall
                    that.settings.queueCompleteCallback();
                    that._queueComplete = false;
                }

            });

            $(that.element).bind('fileuploadprogressall', function (e, data) {
                if(data.total === data.loaded) {
                    that._queueComplete = true;
                }
            });

        },
        initAdditionalFormFields: function() {
            var that = this;
            $(that.element).bind('fileuploadsubmit', function (e, data) {
                var inputs = $(that.element).find(':input');
                data.formData = inputs.serializeArray();
            });
        },
        initDropZoneEffects: function() {
            var that = this;
            $(document).bind('dragover', function (e) {
                var dropZone = $('.dropzone', that.element),
                    timeout = window.dropZoneTimeout;
                if (!timeout) {
                    dropZone.addClass('in');
                } else {
                    clearTimeout(timeout);
                }
                var found = false,
                    node = e.target;
                do {
                    if (node === dropZone[0]) {
                        found = true;
                        break;
                    }
                    node = node.parentNode;
                } while (node != null);
                if (found) {
                    dropZone.addClass('hover');
                } else {
                    dropZone.removeClass('hover');
                }
                window.dropZoneTimeout = setTimeout(function () {
                    window.dropZoneTimeout = null;
                    dropZone.removeClass('in hover');
                }, 1000);
            });
        }
    });

    $.fn[ pluginName ] = function ( options ) {
        return this.each(function() {
            if ( !$.data( this, "plugin_" + pluginName ) ) {
                $.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
            }
        });
    };

})( jQuery, window, document );