CHANGELOG For 7.1.x
===================

# New Features

In v7.1.4 there are separate sessions created for frontend and backend.
If `CHAMELEON_EXTRANET_USER_IS_PORTAL_DEPENDANT` is set to `true`, each portal will have its own session as well.

# Changed Features

## Field Type "Document manager" extends `TCMSFieldLookupMultiselect`

This changes public methods - especially `GetMLTTableName`.

# Changed Interfaces

In v7.1.4 `\ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface` has an additional method:
`isPreviewMode();`
Implementations should return true if the current request is a frontend request and the current page is a preview page.
