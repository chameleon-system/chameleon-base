Chameleon System ExtranetBundle
===============================

Services
--------

chameleon_system_extranet.extranet_user_provider: provides access to the active user
chameleon_system_extranet.extranet_config: provides config details for the extranet module

Login Success Redirects
-----------------------

If a user tries to open a page which is restricted, the login form will be shown (if configured properly in extranet
config). This happens without a redirect, so the URL does not change.

If a login is successful, the user will be redirected to either the loginSuccess, myAccount or home page as fallback.
If the current page is NOT the login page the user will be redirected back to the current url.