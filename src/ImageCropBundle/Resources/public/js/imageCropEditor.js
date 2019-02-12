(function ($) {
    var image = document.getElementById('imageToCrop');
    var config = {};
    if (undefined !== typeof image.dataset.cropperAspectRatio) {
        config.aspectRatio = image.dataset.cropperAspectRatio;
    }
    if (undefined !== typeof image.dataset.cropperCropX) {
        config.data = config.data || {};
        config.data.x = parseInt(image.dataset.cropperCropX);
    }
    if (undefined !== typeof image.dataset.cropperCropY) {
        config.data = config.data || {};
        config.data.y = parseInt(image.dataset.cropperCropY);
    }
    if (undefined !== typeof image.dataset.cropperCropWidth) {
        config.data = config.data || {};
        config.data.width = parseInt(image.dataset.cropperCropWidth);
    }
    if (undefined !== typeof image.dataset.cropperCropHeight) {
        config.data = config.data || {};
        config.data.height = parseInt(image.dataset.cropperCropHeight);
    }
    var cropper = new Cropper(image, {
        viewMode: 1,
        aspectRatio: config.aspectRatio,
        rotatable: false,
        zoomable: false,
        zoomOnWheel: false,
        scalable: false,
        autoCrop: true,
        autoCropArea: 1,
        data: config.data,
        crop: function (e) {
            $('#pos_x').val(e.detail.x);
            $('#pos_y').val(e.detail.y);
            $('#width').val(e.detail.width);
            $('#height').val(e.detail.height);
        }
    });
    var callbackFieldName = image.dataset.callbackFieldName,
        callbackExistingCropId = image.dataset.callbackExistingCropId,
        callbackCmsImageId = image.dataset.callbackCmsImageId,
        callbackUrlToGetImage = image.dataset.callbackUrlToGetImage;
    if (undefined !== callbackFieldName && undefined !== callbackExistingCropId && undefined !== callbackCmsImageId && undefined !== callbackUrlToGetImage) {
        parent.imageCropEditorCallback(callbackFieldName, callbackExistingCropId, callbackCmsImageId, callbackUrlToGetImage);
    }

})(jQuery);