Backend State
=============

The backend state provides access to the users current editing language. The service is available via 
`chameleon_system_cms_backend.backend_session`.

If you need to access the service where dependency injection is not possible, you can use the `ServiceLocator`

```php
/** @var BackendSessionInterface $backendSession */
$backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');
```