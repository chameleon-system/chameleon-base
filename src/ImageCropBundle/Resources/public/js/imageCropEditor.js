(function ($) {
    var iframe = window.frameElement;
    var imageContainer = document.querySelector('.snippetImageCropImageCropEditorImageCropEditor');
    var image = document.getElementById('imageToCrop');
    var modalBodyHeight = iframe.getAttribute('data-modal-body-height');

    if (modalBodyHeight) {
        var offsetHeight = 32; //the modal-body padding
        const cropPresetList = document.querySelector('.snippetImageCropImageCropEditorPresetList');
        if (cropPresetList) {
            offsetHeight = offsetHeight + cropPresetList.offsetHeight;
        }
        const cropEditForm = document.querySelector('.snippetImageCropImageCropEditorImageCropEditorForm');
        if (cropEditForm) {
            offsetHeight = offsetHeight + cropEditForm.offsetHeight + 24; //+margin-bottom
        }
        const remainingHeight = modalBodyHeight - offsetHeight - 30; //a little more space to remove the scrolling bar
        imageContainer.style.maxHeight = remainingHeight + 'px';
    }

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
        dragMode: 'crop',
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
        const currentURL = window.location.href;
        const urlParams = new URLSearchParams(currentURL);
        let parentIframeElement = null;
        let parentIframe = '';
        if (urlParams.has('parentIFrame')) {
            parentIframe = urlParams.get('parentIFrame');
            if ('' !== parentIframe) {
                parentIframeElement = parent.document.getElementById(parentIframe);
            }
        }
        if (null !== parentIframeElement) {
            parentIframeElement.contentWindow.imageCropEditorCallback(callbackFieldName, callbackExistingCropId, callbackCmsImageId, callbackUrlToGetImage, parentIframe);
        } else {
            parent.imageCropEditorCallback(callbackFieldName, callbackExistingCropId, callbackCmsImageId, callbackUrlToGetImage);
        }
    }

})(jQuery);