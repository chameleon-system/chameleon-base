(function ($, window) {
    function getCallbackTargetWindow() {
        const currentURL = window.location.href;
        const urlParams = new URLSearchParams(currentURL);
        const parentIsInModal = urlParams.get('parentIsInModal');
        if (urlParams.has('parentIFrame') && (null === parentIsInModal || '' === parentIsInModal)) {
            const parentIFrame = urlParams.get('parentIFrame');
            if (parentIFrame && parent && parent.document) {
                const parentIframeElement = parent.document.getElementById(parentIFrame);
                if (parentIframeElement && parentIframeElement.contentWindow) {
                    return parentIframeElement.contentWindow;
                }
            }
        }
        if (parent && typeof parent.imageCropEditorCallback === 'function') {
            return parent;
        }
        if (parent && parent.parent && typeof parent.parent.imageCropEditorCallback === 'function') {
            return parent.parent;
        }
        return parent;
    }

    function refreshParentFieldIfAvailable() {
        const currentURL = window.location.href;
        const urlParams = new URLSearchParams(currentURL);
        const fieldName = urlParams.get('fieldName');
        const imageId = urlParams.get('cmsMediaId');
        const callbackTarget = getCallbackTargetWindow();
        if (fieldName && imageId && callbackTarget && callbackTarget.refreshImageCropFieldDetails) {
            callbackTarget.refreshImageCropFieldDetails(fieldName, imageId);
        }
    }

    function closeEditorModal() {
        refreshParentFieldIfAvailable();
        window.parent.CloseModalIFrameDialog();
    }

    function updateImageViewportHeight() {
        var iframe = window.frameElement;
        if (!iframe) {
            return;
        }
        var imageContainer = document.querySelector('.snippetImageCropImageCropEditorImageCropEditor');
        if (!imageContainer) {
            return;
        }
        var modalBodyHeight = iframe.getAttribute('data-modal-body-height');
        if (modalBodyHeight) {
            var offsetHeight = 32;
            const cropPresetList = document.querySelector('.snippetImageCropImageCropEditorPresetList');
            if (cropPresetList) {
                offsetHeight = offsetHeight + cropPresetList.offsetHeight;
            }
            const cropEditForm = document.querySelector('.snippetImageCropImageCropEditorImageCropEditorForm');
            if (cropEditForm) {
                offsetHeight = offsetHeight + cropEditForm.offsetHeight + 24;
            }
            const remainingHeight = modalBodyHeight - offsetHeight - 30;
            imageContainer.style.maxHeight = remainingHeight + 'px';
        }
    }

    function initCropper() {
        var image = document.getElementById('imageToCrop');
        if (!image) {
            return;
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

        new Cropper(image, {
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
    }

    function replaceEditorFromResponseHtml(html, urlForHistory) {
        var $responseDom = $('<div>').append($.parseHTML(html));
        var $newContent = $responseDom.find('#imageCropEditorContent').first();
        if (0 === $newContent.length) {
            return false;
        }
        $('#imageCropEditorContent').replaceWith($newContent);
        if (window.history && window.history.replaceState && urlForHistory) {
            window.history.replaceState({}, '', urlForHistory);
        }
        updateImageViewportHeight();
        initCropper();
        return true;
    }

    function loadEditor(url) {
        $.ajax({
            url: url,
            type: 'GET',
            async: true,
            dataType: 'html',
            success: function (html) {
                replaceEditorFromResponseHtml(html, url);
            }
        }).fail(function () {
            new PNotify({
                text: 'Bildausschnitt konnte nicht geladen werden.',
                type: 'error'
            });
        });
    }

    $(document).on('click', '.snippetImageCropImageCropEditorPresetList a', function (evt) {
        evt.preventDefault();
        loadEditor($(this).attr('href'));
    });

    $(document).on('click', '#closeImageCropEditorButton', function (evt) {
        evt.preventDefault();
        closeEditorModal();
    });

    $(document).on('click', '.snippetImageCropImageCropEditorImageCropEditorForm button[type="submit"]', function () {
        var saveAction = $(this).data('save-action') || 'save';
        $('#saveAction').val(saveAction);
    });

    $(document).on('submit', '.snippetImageCropImageCropEditorImageCropEditorForm', function (evt) {
        evt.preventDefault();
        var $form = $(this);
        $('#imageCropAjaxMode').val('1');
        var submitUrl = $form.attr('action');
        $.ajax({
            url: submitUrl,
            type: 'POST',
            async: true,
            dataType: 'json',
            data: $form.serialize()
        }).done(function (response) {
            if (response.redirectUrl) {
                loadEditor(response.redirectUrl);
            }
            const callbackTarget = getCallbackTargetWindow();
            if ('save_and_set' === response.saveAction && response.fieldName && response.cropId) {
                if (!callbackTarget || 'function' !== typeof callbackTarget.imageCropEditorCallback) {
                    new PNotify({
                        text: 'Setzen im Parent-Feld nicht möglich (Callback nicht gefunden).',
                        type: 'error'
                    });
                    return;
                }
                callbackTarget.imageCropEditorCallback(
                    response.fieldName,
                    response.cropId,
                    response.cmsImageId,
                    response.urlToGetImage,
                    '',
                    false
                );
            } else if (response.fieldName && response.cmsImageId) {
                if (callbackTarget && callbackTarget.refreshImageCropFieldDetails) {
                    callbackTarget.refreshImageCropFieldDetails(response.fieldName, response.cmsImageId);
                }
            }
        }).fail(function ($xhr) {
            var data = $xhr.responseJSON;
            if (null === data || 'undefined' === typeof data || 'object' !== typeof data) {
                data = {};
                try {
                    data = $.parseJSON($xhr.responseText);
                } catch (e) {
                    data = {errorMessage: 'Bildausschnitt konnte nicht gespeichert werden.'};
                }
            }
            new PNotify({
                text: data.errorMessage || 'Bildausschnitt konnte nicht gespeichert werden.',
                type: 'error'
            });
        });
    });

    updateImageViewportHeight();
    initCropper();
    window.addEventListener('beforeunload', function () {
        refreshParentFieldIfAvailable();
    });
})(jQuery, window);
