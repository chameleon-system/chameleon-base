Chameleon System CmsCaptchaBundle
=================================

Overview
--------
The CmsCaptchaBundle provides CAPTCHA generation and validation functionality to protect your forms
against automated submissions. It supports text-based and simple math CAPTCHAs with configurable
fonts, code length, and image dimensions.

Features
--------
- Generate CAPTCHA images on the fly (GD or ImageMagick)
- Validate user-submitted codes stored in session
- Configurable code length, image size, and font file
- Automatic code expiration and session isolation
- "Reload" support via JavaScript
- Extendable: create custom CAPTCHA types by subclassing

Installation
------------
This bundle is included by default in Chameleon System. To manually register:
```php
// in AppKernel::registerBundles() or bundles.php
new ChameleonSystem\\CmsCaptchaBundle\\ChameleonSystemCmsCaptchaBundle(),
```

Font Configuration
------------------
Place TTF font files in the `font/` directory of the bundle. By default, `monofont.ttf` is used.
To use a custom font, subclass `TPkgCmsCaptcha` and override `GetFontPath()`:
```php
class MyCaptcha extends TPkgCmsCaptcha
{
    protected function GetFontPath(): string
    {
        return __DIR__.'/../../font/myfont.ttf';
    }
}
```

Usage
-----
### Displaying a CAPTCHA
```php
<img src="<?= TdbPkgCmsCaptcha::GetInstanceFromName('standard')
        ->GetRequestURL('formCaptcha') ?>"
     alt="CAPTCHA" id="formCaptchaImage" />
<input type="text" name="captchaCode" />
```

### Validating the CAPTCHA
```php
$input = TGlobal::instance()->GetUserData('captchaCode');
if (TdbPkgCmsCaptcha::GetInstanceFromName('standard')
        ->CodeIsValid('formCaptcha', $input)) {
    // Valid
} else {
    // Invalid
}
```

### Reload Link
```html
<a href="#" onclick="
    document.getElementById('formCaptchaImage').src=
        '<?= TdbPkgCmsCaptcha::GetInstanceFromName('standard')
            ->GetRequestURL('formCaptcha') ?>'
        + '&' + Math.random();
    return false;">Reload CAPTCHA</a>
```

Customization
-------------
Extend `TPkgCmsCaptcha` to alter rendering or code logic:
```php
class MyMathCaptcha extends TPkgCmsCaptcha_Math
{
    protected function GenerateCode(string $identifier, int $length): string
    {
        return parent::GenerateCode($identifier, $length);
    }
}
```

Advanced Configuration
----------------------
- Change code length, width, and height via URL parameters `l`, `w`, and `h`.

API Reference
-------------
```php
TPkgCmsCaptcha::GetInstanceFromName(string $name): TPkgCmsCaptcha
GetRequestURL(string $identifier, array $params = []): string
CodeIsValid(string $identifier, string $input): bool
```

License
-------
Licensed under the MIT License. See the `LICENSE` file at the project root for details.