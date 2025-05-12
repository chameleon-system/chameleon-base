Chameleon System CoreValidatorConstraintsBundle
===============================================

## Overview

The CoreValidatorConstraintsBundle provides additional Symfony Validator constraints for common use cases in Chameleon System, such as financial identifiers.

## Installation

This bundle is distributed as part of the `chameleon-system/chameleon-base` package. Ensure you have it installed via Composer:

## Available Constraints

### finance/Bic

Validates Business Identifier Codes (BIC/SWIFT codes) according to ISO 9362.

Constraint Class: `esono\pkgCoreValidatorConstraints\finance\Bic`
Validator Class:  `esono\pkgCoreValidatorConstraints\finance\BicValidator`

Options:
- `message` (string): Override the error message. Defaults to translation key `VALIDATOR_CONSTRAINT_FINANCE_BIC`.

Usage Examples
--------------
Annotation in a PHP class:
```php
namespace App\Entity;

use esono\pkgCoreValidatorConstraints\finance\Bic;
use Symfony\Component\Validator\Constraints as Assert;

class BankAccount
{
    /**
     * @var string
     *
     * @Bic(
     *     message = "{{ value }} is not a valid BIC code."
     * )
     */
    private $bic;
}
```

Programmatic (Form) Validation:
```php
$builder->add('bic', TextType::class, [
    'constraints' => [
        new Bic(['message' => '%value% is not a valid BIC.']),
    ],
]);
```

Translation Override
--------------------
Default error message is resolved via the key `VALIDATOR_CONSTRAINT_FINANCE_BIC`. You can override it by adding translation files in your project:

```yaml
# app/Resources/translations/messages.en.yaml
VALIDATOR_CONSTRAINT_FINANCE_BIC: '%value% is not a valid SWIFT/BIC code.'
```

Extending Constraints
---------------------
To add new constraints, create your own Constraint and ConstraintValidator classes following Symfony's guidelines, and register them as services if needed.

License
-------
This bundle follows the same license as the Chameleon System.
