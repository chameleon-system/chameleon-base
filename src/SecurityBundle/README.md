# 2FA Setup with Google Authenticator in Chameleon

---

##  Register

Register SchebTwoFactorBundle to your `app/AppKernel.php` file:
```php
new \Scheb\TwoFactorBundle\SchebTwoFactorBundle(),
```

---

## Configuration

Edit your config file to implement 2fa with Google Authenticator. To activate or
deactivate the 2fa, set `enabled` to `true` or `false`.

### `app/config/config.yml`

```yaml
scheb_two_factor:
    security_tokens:
        - Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken
    google:
        enabled: true
        server_name: 'Chameleon Backend'
        issuer: 'ChameleonCMS'
        digits: 6
        template: '@ChameleonSystemSecurity/cms/2fa/form.html.twig'
```

To change the form of the 2fa authentication, adjust the "template".

## Usage

After enabling the 2fa, the user will be prompted with a setup page of the 
google authentication. You can either scan the QR code with your phone or
enter the secret manually.

Route for setup process: `/cms/2fa/setup`

After setting up the 2fa the user wil be redirected to enter the 2fa
code after a successful login was made.

Route for entering the 2fa code: `/cms/2fa`

---

## 📄 Custom 2FA Challenge Template

Override or extend this file:

```
@ChameleonSystemSecurity/cms/2fa/form.html.twig
```

---

## 🧪 Testing

Run PHPUnit tests for:

- `assignTwoFactorSecretAndUpdateSession()`
- `generateQrCodeDataUri()`
- `checkAuthorizationCode()`

---