Chameleon System SecurityBundle
===============================

Overview
--------
The SecurityBundle integrates Symfony Security and SchebTwoFactorBundle into the Chameleon System, providing:
- Backend user authentication (login, logout, switch user)
- Role- and voter-based access control for CMS resources
- Two-Factor Authentication (2FA) with Google Authenticator
- CMS endpoint modules for login and host redirection
- Event listeners for login success handling and preview mode

Key Features
------------
- **User Provider**: `CmsUserDataAccess` loads CMS users from the database.
- **Firewalls**:
  - `backend`: Secures `/cms` routes with form login, logout, and switch_user.
  - `dev`: Disables security for profiler and asset paths.
  - `frontend`: Public access by default.
- **Form Login**: Configurable login route (`cms_login`), check path, and default target path.
- **Switch User**: Enable impersonation via `CMS_RIGHT_CMS_AUTO_SWITCH_TO_ANY_USER` role.
- **Access Control**: URL-based rules to restrict CMS, 2FA setup, and logout routes.
- **Two-Factor Auth**:
  - Integrated with SchebTwoFactorBundle for Google Authenticator.
  - Configurable via `scheb_two_factor` and `chameleon_system_security.two_factor` settings.
  - Setup, challenge, and check paths under `/cms/2fa`.
- **Event Listeners**:
  - `RedirectOnPendingUpdatesEventListener`: handle pending CMS updates after login.
  - `PreviewTokenEventListener`: manage preview mode tokens on login/logout.
  - `TwoFactorSetupRedirectListener`: redirect to 2FA setup if enabled.
- **Security Helper**: `SecurityHelperAccess` service for checking permissions programmatically.
- **Voters**:
  - `UserHasRightVoter`, `CmsRightVoter`, `CmsGroupVoter`, `CmsRoleVoter`
  - `CmsTableNameVoter`, `CmsTableObjectVoter` for granular table/object access.
- **Data Access**: `RightsDataAccess` for fetching CMS rights.
- **TwoFactorService**: encapsulates logic to generate secrets, QR codes, and verify codes.
- **Condition**: `GoogleLoginDeactivateTwoFactorCondition` to globally enable/disable 2FA.

Installation
------------
This bundle is included in the `chameleon-system/chameleon-base` package and registered automatically.
To register manually (no Flex auto-registration), add to `app/AppKernel.php`:
```php
 // app/AppKernel.php
 public function registerBundles()
 {
     $bundles = [
         // ...
         new ChameleonSystem\SecurityBundle\ChameleonSystemSecurityBundle(),
     ];
     return $bundles;
 }
```

Configuration
-------------
Configure security rules in `config/packages/security.yaml` and 2FA in `config/packages/scheb_two_factor.yaml`:

```yaml
# config/packages/security.yaml
security:
  providers:
    chameleon_backend_user_provider:
      id: ChameleonSystem\SecurityBundle\CmsUser\CmsUserDataAccess
  password_hashers:
    Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: { algorithm: bcrypt, cost: 12 }
  firewalls:
    dev:
      pattern: ^/(_(profiler|wdt)|css|images|js)/
      security: false
    backend:
      pattern: ^/cms
      provider: chameleon_backend_user_provider
      entry_point: form_login
      form_login:
        login_path: cms_login
        check_path: cms_login
        default_target_path: /cms
      logout:
        path: app_logout
        target: /cms
      switch_user: { role: CMS_RIGHT_CMS_AUTO_SWITCH_TO_ANY_USER }
      two_factor:
        auth_form_path: 2fa_login
        check_path: 2fa_login_check
    frontend:
      security: false
  access_control:
    - { path: ^/cms/2fa/setup, roles: IS_AUTHENTICATED }
    - { path: ^/cms/2fa, roles: IS_AUTHENTICATED_2FA_IN_PROGRESS }
    - { path: ^/cms, roles: ROLE_CMS_USER }
```

```yaml
# config/packages/scheb_two_factor.yaml
scheb_two_factor:
  two_factor_condition: 'chameleon_system_security.condition.google_login_deactivate_two_factor_condition'
  google:
    enabled: true
    issuer: 'ChameleonCMS'
    template: '@ChameleonSystemSecurity/cms/2fa/form.html.twig'
```

Enable or disable 2FA globally with:
```yaml
chameleon_system_security:
  two_factor:
    enabled: true  # or false
```

Usage
-----
### Frontend 2FA Setup and Challenge
1. Visit `/cms/2fa/setup` to scan the QR code or enter the secret manually.
2. After login at `/cms/login`, you will be redirected to `/cms/2fa` to enter the OTP code.

### Programmatic Permission Checks
```php
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;

$securityHelper = $this->container->get(SecurityHelperAccess::class);
if ($securityHelper->hasAccess('CMS_RIGHT_EDIT_ARTICLE')) {
    // ...
}
```

### Impersonation
Access `/cms?_switch_user={userId}` if you have `CMS_RIGHT_CMS_AUTO_SWITCH_TO_ANY_USER`.

Extensibility
-------------
- **Custom 2FA Condition**: implement `TwoFactorConditionInterface` and tag with `chameleon_system_security.condition.*`.
- **Redirect Listeners**: add listeners for `LoginSuccessEvent` or `LogoutEvent` by tagging services.
- **Voters**: register new voters by tagging with `security.voter` in `services.xml`.

Template Overrides
------------------
Copy templates from `src/SecurityBundle/templates/cms/2fa` to your application (`templates/bundles/ChameleonSystemSecurityBundle/cms/2fa/`) to customize 2FA forms.

License
-------
This bundle is released under the same license as the Chameleon System. See the LICENSE file in the project root.