<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace esono\pkgCoreValidatorConstraints\finance;

class BicValidator extends \Symfony\Component\Validator\ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param \Symfony\Component\Validator\Constraint $constraint The constraint for the validation
     *
     * @see http://www.iban.de/bic.html
     *
     * @api
     *
     * @return void
     */
    public function validate($value, \Symfony\Component\Validator\Constraint $constraint)
    {
        // 4-stelliger Bankcode
        // 2-stelliger Ländercode
        // 2-stellige Codierung des Ortes
        // 3-stellige Kennzeichnung der Filiale (optional)

        /** @var Bic $constraint */
        // must be either 11 or 8 char long
        $length = mb_strlen($value);
        if (8 !== $length && 11 !== $length) {
            $this->context->addViolation($constraint->getRawMessage(), ['[{value}]' => $value]);

            return;
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $value, $matches)) {
            $this->context->addViolation($constraint->getRawMessage(), ['[{value}]' => $value]);

            return;
        }

        $countryCode = substr($value, 4, 2);
        if (true === is_numeric($countryCode)) {
            $this->context->addViolation($constraint->getRawMessage(), ['[{value}]' => $value]);

            return;
        }
    }
}
