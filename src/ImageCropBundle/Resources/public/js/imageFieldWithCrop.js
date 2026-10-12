(function ($, window) {
    function getHostWindowWithCloseModalFunction() {
        if ('function' === typeof window.CloseModalIFrameDialog) {
            return window;
        }
        if (window.parent && 'function' === typeof window.parent.CloseModalIFrameDialog) {
            return window.parent;
        }
        return null;
    }

    window.openImageCropEditor = function (url, windowTitle) {

        var buffer = 200;
        var height = $(window).height() - buffer;
        var width = $(window).width() - buffer;

        const urlParams = new URLSearchParams(new URL(url).search);
        const parentIsInModal = urlParams.get('parentIsInModal');

        if (true === urlParams.has('parentIFrame') && (null === parentIsInModal || '' === parentIsInModal)) {
            parent.CreateModalIFrameDialogCloseButton(url, width, height, windowTitle);
        } else {
            CreateModalIFrameDialogCloseButton(url, width, height, windowTitle);
        }
    };

    window.imageCropResetButtonCallBack = function (evt, imageId) {
        var fieldName = $(this).data('field-name');
        $('#' + fieldName + '_image_crop_id').val('');
        $('#label_' + fieldName + '_image_crop_id span').html('-');
        $(this).hide();
        saveCMSRegistryEntry('_currentFieldName', fieldName);
        saveCMSRegistryEntry('_currentPosition', 0);
        if ('undefined' === typeof imageId) {
            imageId = $('#' + fieldName).val();
        }
        window._SetImage(imageId);
        evt.preventDefault();
    };

    window.refreshImageCropFieldDetails = function (fieldName, imageId) {
        var cropId = $('#' + fieldName + '_image_crop_id').val();
        var urlToGetImage = window.returnUrlToGetCroppedImageForImageCropField();
        if ('function' === typeof CMSAddGlobalParametersToURL) {
            urlToGetImage = CMSAddGlobalParametersToURL(urlToGetImage);
        }
        $.ajax({
            url: urlToGetImage,
            type: "GET",
            async: true,
            data: {
                imageId: imageId,
                cropId: cropId,
                _fieldName: fieldName
            },
            success: function (jsonData) {
                if (jsonData.imageUrl) {
                    $('#cmsimagefielditem_imagediv_' + fieldName + '0 img').attr('src', jsonData.imageUrl);
                }
                $('#label_' + fieldName + '_image_crop_id span').text(jsonData.cropName);
                var $savedCropsList = $('#saved_crops_list_' + fieldName);
                if ($savedCropsList.length) {
                    var $savedCropsListContainer = $('#' + fieldName + '-saved-crops-list-container');
                    $savedCropsList.empty();
                    if (jsonData.savedCrops && jsonData.savedCrops.length > 0) {
                        $savedCropsListContainer.removeClass('d-none');
                        jsonData.savedCrops.forEach(function (savedCrop) {
                            $('<li>')
                                .toggleClass('active', !!savedCrop.selected)
                                .append($('<a href="#">')
                                    .attr({
                                        'data-crop-id': savedCrop.id,
                                        'data-field-name': fieldName,
                                        'data-image-id': imageId
                                    })
                                    .text(savedCrop.name))
                                .appendTo($savedCropsList);
                        });
                    } else {
                        $savedCropsListContainer.addClass('d-none');
                        $savedCropsList.append('<li>-</li>');
                    }
                }
            },
            dataType: 'json'
        });
    };

    window.imageCropEditorCallback = function (fieldName, cropId, imageId, urlToGetImage, parentIFrame = '', closeModal = true) {
        $('#' + fieldName + '_image_crop_id').val(cropId);
        $('#' + fieldName + '_image_crop_id_reset_button').show();
        if ('function' === typeof CMSAddGlobalParametersToURL) {
            urlToGetImage = CMSAddGlobalParametersToURL(urlToGetImage);
        }
        $.ajax({
            url: urlToGetImage,
            type: "GET",
            async: true,
            data: {
                imageId: imageId,
                cropId: cropId,
                _fieldName: fieldName
            },
            success: function (jsonData) {
                $('#cmsimagefielditem_imagediv_' + fieldName + '0 img').attr('src', jsonData.imageUrl);
                $('#label_' + fieldName + '_image_crop_id span').text(jsonData.cropName);
                $('#' + fieldName + '_image_crop_id_reset_button').on('click', window.imageCropResetButtonCallBack);
                window.refreshImageCropFieldDetails(fieldName, imageId);
                if (true === closeModal) {
                    var modalHost = getHostWindowWithCloseModalFunction();
                    if (modalHost) {
                        if ('' !== parentIFrame) {
                            modalHost.CloseModalIFrameDialog(parentIFrame);
                        } else {
                            modalHost.CloseModalIFrameDialog();
                        }
                    }
                }
            },
            dataType: 'json'
        }).fail(function ($xhr) {
            var data = $xhr.responseJSON;
            if (null === data || 'undefined' === typeof data || 'object' !== typeof data) {
                data = {};
                try {
                    data = $.parseJSON($xhr.responseText);
                } catch (e) {
                    data = {errorMessage: 'Bildausschnitt konnte nicht gesetzt werden.'};
                }
            }
            new PNotify({
                text: data.errorMessage || 'Bildausschnitt konnte nicht gesetzt werden.',
                type: 'error'
            });
        });
    };


    var oldResetFunction = window._ResetImage;
    window._ResetImage = function (fieldName, position) {
        oldResetFunction(fieldName, position);
        $('#' + fieldName + '_image_crop_id').val('');
        $('#label_' + fieldName + '_image_crop_id span').html('-');
        $('#' + fieldName + '_image_crop_id_reset_button').hide();
        $('#' + fieldName + '-saved-crops-list-container').addClass('d-none');
    };

    var oldSetFunction = window._SetImage;
    window._SetImage = function (imageID, customCallback) {
        var fieldName = getCMSRegistryEntry('_currentFieldName');
        $('#' + fieldName + '_image_crop_id').val('');
        $('#label_' + fieldName + '_image_crop_id span').html('-');
        $('#' + fieldName + '_image_crop_id_reset_button').hide();
        oldSetFunction(imageID, customCallback);
        if ('function' === typeof window.refreshImageCropFieldDetails && imageID) {
            window.refreshImageCropFieldDetails(fieldName, imageID);
        }
    };

    window.setImageWithCrop = function (imageId, cropId) {
        var fieldName = getCMSRegistryEntry('_currentFieldName');

        var callback = function (data, responseMessage) {
            $.when(window.SetImageResponse(data, responseMessage)).done(function (result) {
                if (cropId) {
                    window.imageCropEditorCallback(fieldName, cropId, imageId, window.returnUrlToGetCroppedImageForImageCropField());
                }
            });
        };

        window._SetImage(imageId, callback);
    }

    $(document).on('click', '.saved-crops-list a', function (evt) {
        evt.preventDefault();
        var fieldName = $(this).data('field-name');
        var imageId = $(this).data('image-id');
        var cropId = $(this).data('crop-id');
        window.imageCropEditorCallback(fieldName, cropId, imageId, window.returnUrlToGetCroppedImageForImageCropField(), '', false);
    });

})(jQuery, window);
