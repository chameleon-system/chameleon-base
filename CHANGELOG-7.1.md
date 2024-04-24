CHANGELOG For 7.1.x
===================

# New Features

In v7.1.4 there are separate sessions created for frontend and backend.
If `CHAMELEON_EXTRANET_USER_IS_PORTAL_DEPENDANT` is set to `true`, each portal will have its own session as well.

# Changed Features

* now a <i>Preview Mode</i> (`chameleon_system_core.preview_mode_service`) is provided to check if the current frontend 
  user is also a backend user currently. This can be useful for previewing backend settings in the frontend.  
  Technically, there is an additional cookie to manage the preview mode access via a generated backend user token. This 
  cookie will be created after backend login and deleted after logout.

  Two methods are available:
  - `currentSessionHasPreviewAccess` has a user an active preview mode
  - `grantPreviewAccess` (de-)activates the preview mode of a user explicitly (normally just used by the login/logout
  methods)
   

## Field Type "Document manager" extends `TCMSFieldLookupMultiselect`

This changes public methods - especially `GetMLTTableName`.

# Changed Interfaces

In v7.1.4 `\ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface` has an additional method:
`isPreviewMode();`
Implementations should return true if the current request is a frontend request and the preview mode is active.
