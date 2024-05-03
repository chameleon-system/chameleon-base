CHANGELOG For 7.1.x
===================

List of changes, the newer versions at the top, with following possible types:
- New Features
- Changed Features
- New Interfaces
- Changed Interfaces

# Changes in 7.1.6

## New Features

* Using a parametrized URL for CMS, e.g. a link to a certain data record, you will be redirected to the 
  desired page after login.
  Note that this feature needs an adaption later, because the Security Symfony Bundle, currently part of the main
  branch, affects the login process fundamentally.
* Using a new service tag `chameleon_system_core.field_extension`, you have the possibility to append additional 
  rendering output for backend fields.
  By implementing the interface `ChameleonSystem\CoreBundle\Interfaces\FieldExtensionInterface`, you can filter by field
  name and types and adding scripts and assets as you like.
  See also `/CoreBundle/Resources/doc/table-editor.md`
  Also added:
  - `\ChameleonSystem\CoreBundle\Service\FieldExtensionRenderService` a service managing any custom rendering services
    as described above
  - 
* A new service class `\ChameleonSystem\CoreBundle\Service\PreviewModeService` is provided to check if the current
  frontend user is also a backend user currently. This can be useful for previewing backend settings in the frontend.  
  Technically, there is an additional cookie to manage the preview mode access via a generated backend user token. This
  cookie will be created after backend login and deleted after logout.

  Two methods are available:
  - `currentSessionHasPreviewAccess(): bool`
    Returns the state on an active preview mode
  - `grantPreviewAccess(bool $previewGranted, string $cmsUserId): void`
    (De-)activates the preview mode of a user explicitly (normally just used by the login/logout methods)

* A new class `\ChameleonSystem\CoreBundle\Field\FieldIconFontSelector extends \TCMSFieldVarchar` to create an icon
  selector dialog.
* A new Service `\ChameleonSystem\CoreBundle\Service\FontAwesomeService` to manage Awesome Font icon class names with
  their prefixes.
* A global Javascript event `tableEditorBeforeSaveEvent` in the backend is added in order to write back CKEditor's 
  internal stored values into the hidden fields before submitting the form instances at the save process. So dispatch
  this event if you add a custom Javascript save method. 
  
## Changed Features

* Backend's "breadcrumb", actually a history of user's pages, is now more reliable. Using the cache now to store the
  history and in a cookie just as a fallback, this feature is usually not available in development mode.
  * `ImageCropBundle`: In former versions, there was the need to create symlinks for use of this bundle.
    Now these symlinks can be removed:
    - `customer/src/extensions/snippets-cms/imageCrop`
    - `customer/src/extensions/objectviews/TCMSFields/TCMSFieldMediaWithImageCrop`

    See also change of `customer/vendor/chameleon-system/chameleon-base/src/ImageCropBundle/Resources/doc/index.rst`

    * Any folder under `customer/src/extensions/objectviews` can now be moved to a preferred theme, using the folder
      `Resources/views/objectviews/`.

      Eventually, you have to add the used bundle paths to your backend theme snippets to grant access to these resources.
      * The <i>Google GeoCode</i> feature is replaced by <i>Nominatim API</i>.
        An example of an adequate configuration would be
        ```yaml
        parameters:
            chameleon_system_core:
              geocoder:
                  geo_json_endpoint: 'https://nominatim.openstreetmap.org/search?format=geojson&country=de&q={query}'
                  attribution:
                      show: true
                      name: 'nominatim'
                      url: 'https://nominatim.org/'
        ```
    
        See also `\ChameleonSystem\CoreBundle\DependencyInjection\Configuration::getGeoJsonGeocoderConfig()`.

* By-side the implementation of Nominatim API, an additional http client `symfony/http-client` is now available
* The CMS field type "CSS Icon" now provides an additional option to configure a list of CSS resource files. 
  A graphical icon list selector dialog easies the choice of the preferred icon, considering also Awesome Font prefixes 
  for the class names.
  Example for the new configuration field: `iconFontCssUrls=/chameleon/blackbox/iconFonts/fontawesome-free-5.8.1/css/all.css`.
  For the purpose of a selector, a new field class instead of `TCMSFieldIconList` 
* After fixing the bug, now it's possible to redirect to a configured page after a successful extranet user login as
  provided. The fallback is the user's home (account) page. 
* The wishlist cookie now allows up to 25 entries.

## Changed Interfaces

Additional methods:
* `\ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface`
  - `isPreviewMode(): bool`
    Implementations should return true if the current request is a frontend request and the preview
    mode is active.
  - `setChameleonRequestType(int $requestType): void`
    Changes the request type (frontend, backend) which is usually
    determined by the main request; can be used to pretend/perform a frontend request in a current backend request by a
    temporary change.
* `\ChameleonSystem\CoreBundle\Service\SystemPageServiceInterface`
  - `getSystemPageTree(string $systemPageNameInternal, ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null): ?\TdbCmsTree`
    Returns the tree node connected with the system page $systemPageNameInternal.
  - `getPageDataModel(string $systemPageNameInternal, array $parameters = [], ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null): ?PageDataModel`
    Returns an absolute URL to the system page with the passed internal name for the passed portal in the passed language.
* class `ChameleonSystem\NewsletterBundle\PostProcessing\Bridge\NewsletterUserDataModel`:
  - The has constructor with typed parameters now.
  - A getter and setter for a extranet use `string $extranetUserId` was added.
*  protected method `\MTLoginEndPoint::postLoginRedirect($bIsRefreshLogin)` changed the signature to
   `postLoginRedirect(): void`
* backend class `\TCMSField`
  - `getFieldExtensionHtml(): string` collecting any field extension rendered output of a field
  - `getHeadIncludesForFieldExtension(): array` collecting any field extension header includes
  - `getFooterIncludesForFieldExtension(): array` collecting any field extension footer includes
  - `getValueForFieldExtension(): string` Ajax function to request the rendering of a given service as a parameter

## Deprecations

- \TCMSFieldIconList
- \TCMSFieldSmallIconList
- \CMSiconList
- CoreBundle/private/modules/CMSiconList/views/standard.view.php
- CoreBundle/Resources/BackendPageDefs/iconlist.pagedef.php

# Changes in 7.1.4

## New Features

* There are separate sessions created for frontend and backend.
  If `CHAMELEON_EXTRANET_USER_IS_PORTAL_DEPENDANT` is set to `true`, each portal will have its own session as well.

# Changes in 7.1.0

## Changed Features

* Field Type "Document manager" extends `TCMSFieldLookupMultiselect` - This changes public methods - especially 
  `GetMLTTableName`.
* Class `Esono\CustomerBundle\Bridge\Chameleon\customImageMagick` has been updated radically, so keep an
  eye on this class if you inherited this class for example.
  Some changed their signature, e.g.:
  - `ResizeImage($iWidth, $iHeight)` → `ResizeImage(int $iWidth, int $iHeight): bool`
  This method was added:
  - `getExifData(): ?array`
 