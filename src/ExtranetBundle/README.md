# Chameleon System ExtranetBundle

## Setup

* On every page that has a login form, a static module needs to be placed with the following settings:
   * Name: extranetHandler
   * Model: MTExtranet
   * Template: inc/system
   * Static: yes
* Create the following pages and add the module MTExtranet with the corresponding view
   * Login
   * Login success
   * Forgot password
   * Registration (if users are not added manually via CMS backend)
   * Registration success
   * Access denied (if you want to show a different text as the login page)
* Change the email template text and sender with systemname "registration"

## Services

- `chameleon_system_extranet.extranet_user_provider`: provides access to the active user
- `chameleon_system_extranet.extranet_config`: provides config details for the extranet module

## Login Success Redirects

If a user tries to open a page which is restricted, the login form will be shown (if configured properly in extranet config). This happens without a redirect, so the URL does not change.

If a login is successful, the user will be redirected to either the `loginSuccess`, `myAccount` or `home` page as fallback. If the current page is NOT the login page the user will be redirected back to the current url.