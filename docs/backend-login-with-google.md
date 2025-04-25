# Backend Login with Google Workspace

You can enable backend login with google. If google login is enabled, you can define a user to use as a base when new users register from
a specific domain. 

For Example:

```yaml
chameleon_system_security:
  google_login:
    enabled: true
    domain_to_base_user_mapping:
      - domain: 'yourdomain.de'
        clone_user_permissions_from: admin
```

The user `admin` will be used as a base for new users that register from the domain `yourdomain.de`.

This bundle uses the https://github.com/knpuniversity/oauth2-client-bundle bundle - which you must configure in order to be able to use google login.

## Google Cloud Console Configuration

* Configure an OAuth Client in the [Google Cloud Console](https://developers.google.com/identity/openid-connect/openid-connect?hl=de#registeringyourapp)
* Add the client credentials to your configuration 

You need to add https://www.yourdomain.de as `Authorized JavaScript origins` 
and https://www.yourdomain.de/cms/google-check as Authorised redirect URIs for all environments.

Example Configuration:

```yaml
knpu_oauth2_client:
  clients:
    google_main:
      type: google
      client_id: '%env(GOOGLE_CLIENT_ID)%'
      client_secret: '%env(GOOGLE_CLIENT_SECRET)%'
      # route is defined in \ChameleonSystem\SecurityBundle\Controller\GoogleLoginController::connectAction
      redirect_route: connect_google_start
      redirect_params: {}
      hosted_domain: 'esono.de'
```