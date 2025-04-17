# 🔐 2FA Setup with Google Authenticator in ChameleonCMS (Symfony)

This guide explains how to set up Two-Factor Authentication (2FA) using **Google Authenticator** in your ChameleonCMS (Symfony-based) project.

---

### Attention! Known Bug
During the setup process of the 2FA secret, the user is
valid to use /cms, i have no idea on how to battle this issue

## 📦 Requirements

- Symfony >= 6.x
- `scheb/2fa-bundle`
- `scheb/2fa-google-authenticator`
- `endroid/qr-code`
- A user model that implements `Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface`

---

## 📥 Installation

```bash
composer require scheb/2fa-bundle scheb/2fa-google-authenticator endroid/qr-code
```

---

## ⚙️ Configuration

### `app/config/config.yml`

Two change the form, adjust the "template".

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

---

## 🧩 User Setup

Your backend user model must implement:

```php
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;

class CmsUserModel implements TwoFactorInterface
{
    public function isGoogleAuthenticatorEnabled(): bool;
    public function getGoogleAuthenticatorSecret(): string;
    public function getGoogleAuthenticatorUsername(): string;
}
```

---

## 🔑 Custom 2FA Setup Page

Add a controller route to allow users to set up their 2FA secret.

```php
#[Route('/cms/2fa/setup', name: '2fa_setup')]
public function setup(Request $request): Response
```

This controller should:

- Generate a secret using `$googleAuthenticator->generateSecret()`
- Save the secret via `CmsUserDataAccess`
- Regenerate the security token with the updated user
- Render a QR code using `endroid/qr-code`

---

## 🧠 Session Handling & Security Token Refresh

Use a service like this:

```php
$token = new UsernamePasswordToken(
    $updatedUser,
    'backend',
    $updatedUser->getRoles()
);
$tokenStorage->setToken($token);
```

---

## 🖼️ QR Code Display

Use `endroid/qr-code`:

```php
$qrContent = $googleAuthenticator->getQRContent($user);
$result = (new PngWriter())->write(new QrCode($qrContent));
$dataUri = $result->getDataUri();
```

Render with:

```twig
<img src="{{ qrCode }}" alt="QR Code">
```

---

## 📄 Custom 2FA Challenge Template

Override this file:

```
templates/bundles/SchebTwoFactorBundle/Form/form.html.twig
```

Use your own layout and point the form to:

```twig
<form method="post" action="{{ path('2fa_login_check') }}">
```

---

## 🧪 Testing

Run PHPUnit tests for:

- `assignTwoFactorSecretAndUpdateSession()`
- `generateQrCodeDataUri()`
- `checkAuthorizationCode()`

---

## ✅ Done!

Your ChameleonCMS installation now has full support for Google Authenticator 2FA, including:

- Secure QR setup flow
- Custom login design
- Symfony-native configuration

---

_Last updated: 2025-04-17_
