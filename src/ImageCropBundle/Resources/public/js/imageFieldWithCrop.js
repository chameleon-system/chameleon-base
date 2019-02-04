(function ($, window) {
    window.openImageCropEditor = function (url, windowTitle) {

        var buffer = 200;
        var height = $(window).height() - buffer;
        var width = $(window).width() - buffer;

        CreateModalIFrameDialogCloseButton(url, width, height, windowTitle);
        $('#modal_dialog').dialog({
            close: function (event, ui) {
                CloseModalIFrameDialog();
            }
        });
    };

    window.imageCropResetButtonCallBack = function (evt) {
        var fieldName = $(this).data('field-name');
        $('#' + fieldName + '_image_crop_id').val('');
        $('#label_' + fieldName + '_image_crop_id span').html('-');
        $(this).hide();
        window._SetImage($('#' + fieldName).val());
        evt.preventDefault();
    };

    window.imageCropEditorCallback = function (fieldName, cropId, imageId, urlToGetImage) {
        $('#' + fieldName + '_image_crop_id').val(cropId);
        $('#' + fieldName + '_image_crop_id_reset_button').show();
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
                $('#label_' + fieldName + '_image_crop_id span').html(jsonData.cropName);
                $('#' + fieldName + '_image_crop_id_reset_button').on('click', window.imageCropResetButtonCallBack);
                CloseModalIFrameDialog();
            },
            dataType: 'json'
        }).fail(function ($xhr) {
            var data = $xhr.responseJSON;
            if (null === data || 'undefined' === typeof data) {
                data = $.parseJSON($xhr.responseText);
            }
            new PNotify({
                text: data.errorMessage,
                type: 'error'
            });
        });
    };


    var oldResetFunction = window._ResetImage;
    window._ResetImage = function (fieldName, position) {
        oldResetFunction(fieldName, position);
        $('#' + fieldName + '-crop-button').hide();
        $('#' + fieldName + '_image_crop_id').val('');
        $('#label_' + fieldName + '_image_crop_id span').html('-');
        $('#' + fieldName + '_image_crop_id_reset_button').hide();
    };

    var oldSetFunction = window._SetImage;
    window._SetImage = function (imageID, customCallback) {
        var fieldName = getCMSRegistryEntry('_currentFieldName');
        $('#' + fieldName + '_image_crop_id').val('');
        $('#label_' + fieldName + '_image_crop_id span').html('-');
        $('#' + fieldName + '_image_crop_id_reset_button').hide();
        oldSetFunction(imageID, customCallback);
        $('#' + fieldName + '-crop-button').show();
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

})(jQuery, window);