Chameleon System CmsBackendBundle
======================================

Overview
--------
The CmsBackendBundle provides backend-specific services for the Chameleon System administrative interface.
It includes session management for the current edit language and integrates with the user profile and portal settings.

Services
--------
- **chameleon_system_cms_backend.backend_session**
  - Implementation: `ChameleonSystem\CmsBackendBundle\BackendSession\BackendSession`
  - Interface: `ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface`
  - Manages the current edit language (ISO code and database ID) in the user session and persists changes to the `cms_user` table.

Usage
-----
The BackendSession service is autowired by default. Inject it into your controller or service:

```php
use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;

class MyBackendService
{
    public function __construct(
        private readonly BackendSessionInterface $backendSession
    ) {
    }

    public function example(): void
    {
        // Get the current edit language ISO code (e.g. 'en', 'de')
        $isoCode = $this->backendSession->getCurrentEditLanguageIso6391();

        // Get the current edit language ID (database identifier)
        $languageId = $this->backendSession->getCurrentEditLanguageId();

        // Change the edit language and persist for current user and session
        $this->backendSession->setCurrentEditLanguageIso6391('de');

        // Reset to the user's default edit language (removes session override)
        $this->backendSession->resetCurrentEditLanguage();
    }
}
```

License
-------
This bundle is licensed under the MIT License. See the `LICENSE` file at the project root for details.