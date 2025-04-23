# Universal Uploader

The universal uploader handles both media and document uploads.

## Module

The module `CMSModuleUniversalUploader` handles as little as possible and just calls the services handling the actual logic.

## Parameters

The uploader can be called with a few get-parameters which will change its behaviour:

- **sAllowedFileTypes** An array of file-extensions allowed. If none given, the uploader will set file types based on mode.
- **bProportionExactMatch** Uploaded images have to be in the exact size set by iMaxUploadHeight and iMaxUploadWidth.
- **iMaxUploadHeight** If bProportionExactMatch is not set, only accept images with a maximum height of iMaxUploadHeight.
- **iMaxUploadWidth** If bProportionExactMatch is not set, only accept images with a maximum width of iMaxUploadWidth.
- **queueCompleteCallback** Javascript callback when queue completes. Is always called on parent, so `testCallback` will be called as `parent.testCallback()`
- **callback** Javascript callback called for each file separately when uploaded. Is always called on parent with id of uploaded record. So testCallback will be called as parent.testCallback(id)
- **mode** Media or document.
- **recordID** If an existing record has to be replaced.
- **sUploadDescription** Default for description field.
- **sUploadName** Default for name field.
- **treeNodeID** ID of document or media tree the image/document belongs to.
- **singleMode** Set to `true` to allow only one file to be uploaded.
- **showMetaFields** Show description and name fields

All the parameters get mapped into the UploaderParametersDataModel by UploaderParameterFromRequestService.

## Js upload component integration

It is possible to integrate different solutions for the actual implementation of the upload form:

- `CMSModuleUniversalUploader/uploader.html.twig` is used as the module's view
- `chameleon_system_core.universal_uploader.plugin_integration_service` is used to load all the assets needed
- `chameleon_system_core.universal_uploader.uploader_post_handler` is the service that handles the posted data and returns a response to be handled by your plugin
- Always wrap the initialization of any js-component into its own plugin, that can be called with a set of options that matches our settings for the uploader (see `Resources/public/chameleon/blackbox/universalUploader/jqueryFileUpload/uploader.js`)

## Configuration

Services are configured in `universal_uploader.xml` in Core Bundle.

Some configuration comes from cms_config, some is based on php-settings. Both are wrapped in `UploaderConfiguration` class.