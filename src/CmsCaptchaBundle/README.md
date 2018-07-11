Chameleon System CmsCaptchaBundle
=================================

Usage
-----

1. to show a captcha:

```php
    <img src="<?=TdbPkgCmsCaptcha::GetInstanceFromName('standard')->GetRequestURL('test')?>" alt="" id="testCaptcha" />
    <input type="text" name="mycapture" value="" />
```

2. to check the captcha:

```php
<?php
    if (TdbPkgCmsCaptcha::GetInstanceFromName('standard')->CodeIsValid('test', TGlobal::instance()->GetUserData('mycapture'))) {
     // all good
    } else {
      // fail
    }
```

3. if you want to include a "generate new" button:

```php
    <a href="#" onclick="document.getElementById('testCaptcha').src='<?=TdbPkgCmsCaptcha::GetInstanceFromName('standard')->GetRequestURL('test')?>&'+ Math.random(); return false;" >reload</a>
```