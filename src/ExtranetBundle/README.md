# Chameleon System ExtranetBundle
===================================

Overview
--------
The ExtranetBundle provides frontend user authentication and access control for Chameleon System. It includes:
- User login and logout flows
- User registration and confirmation
- Password reset (forgot password)
- Token-based login
- Access-denied handling
- Failed-login throttling

Installation
------------
The bundle is included with `chameleon-system/chameleon-base`. No separate Composer install is needed.

Bundle Registration
-------------------
Symfony Flex (4+) auto-registers bundles. Without Symfony Flex, add it to `app/AppKernel.php` manually:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // ...
        new ChameleonSystem\\ExtranetBundle\\ChameleonSystemExtranetBundle(),
    ];
    return $bundles;
}
```

Modules & Views
---------------
Place the **MTExtranet** module on pages by configuring:
```
Module name: extranetHandler
Model: MTExtranet
Template: inc/system
Static: yes
```
Assign the following **views** to pages in the CMS backend:
- `login` (login form)
- `loginSuccess` (post-login landing)
- `forgotPassword` (enter email to reset)
- `confirmRegistration` (registration link landing)
- `registration` (signup form)
- `registrationSuccess` (post-signup landing)
- `accessDenied` (custom 403 page)
- `postLogout` (page shown after logout)

Services
--------
Key service IDs (defined in `Resources/config/services.xml`):
- `chameleon_system_extranet.cronjob.cleanup_extranet_login_history_cronjob`  – cleans old login records
- `chameleon_system_extranet.event_listener.convert_password_listener`      – rehashes old passwords on login
- `chameleon_system_extranet.event_listener.delay_failed_login_attempt_listener` – throttles repeated login failures
- `chameleon_system_extranet.event_listener.refresh_authenticity_token_listener` – updates CSRF tokens on login
- `chameleon_system_extranet.extranet_user_provider`                      – implements `ExtranetUserProviderInterface`
- `chameleon_system_extranet.extranet_config`                              – implements `ExtranetConfigurationInterface`
- `chameleon_system_extranet.util.extranet_authentication`                – helper utilities for login checks
- `chameleon_system_extranet.login_by_token.login_by_token_controller`     – controller for token login
- `chameleon_system_extranet.login_by_token.service.login_token`           – service to generate and validate login tokens

Routes
------
Defines a token-login route in `Resources/config/route.yml`:
```yaml
chameleon_system_extranet.login_by_token:
  path: /_login_by_token_/{token}/
  controller: chameleon_system_extranet.login_by_token.login_by_token_controller::loginAction
```

Configuration
-------------
All extranet page URLs and settings are stored in the `data_extranet` table and accessed via `ExtranetConfiguration` service.
Use `ExtranetConfigurationInterface::getLink(pageConstant)` to retrieve URLs:
- `PAGE_LOGIN`
- `PAGE_LOGIN_SUCCESS`
- `PAGE_REGISTER`
- `PAGE_CONFIRM_REGISTRATION`
- `PAGE_FORGOT_PASSWORD`
- `PAGE_MY_ACCOUNT`
- `PAGE_ACCESS_DENIED_*`
- `PAGE_LOGOUT`, `PAGE_POST_LOGOUT`

Login Flow & Redirects
----------------------
Restricted pages show the login form inline (no URL change). On successful login, users are redirected to:
1. The originally requested page (if not a login page)
2. Configured `loginSuccess` page
3. `myAccount` page
4. Site home

Usage
-----
Inject the `ExtranetUserProviderInterface` to get the current user:
```php
public function __construct(ExtranetUserProviderInterface $provider) {
    $this->user = $provider->getActiveUser();
}
```

License
-------
This bundle is released under the same license as the Chameleon System.