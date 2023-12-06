Backend-Login with google
==========================

You can enable backend login with google. If google login is enabled, you can define a user to use as a base when new users register from
a specific domain. Example:

```yaml
chameleon_system_security:
  googleLogin:
    enabled: true
    domainToBaseUserMapping:
      - domain: 'esono.de'
        value: admin
```

The user `admin` will be used as a base for new users that register from the domain `esono.de`.

The bundle uses the https://github.com/knpuniversity/oauth2-client-bundle bundle - which you must configure in order to be able to use google login.

Example:
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