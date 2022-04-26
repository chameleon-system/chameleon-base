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

/**
 * Class testBicValidator.
 *
 * @Annotation
 */
class Bic extends \Symfony\Component\Validator\Constraint
{
    /**
     * @var string
     */
    public $message = '%value% is not a valid BIC.';

    /**
     * @return string
     */
    public function getRawMessage()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('VALIDATOR_CONSTRAINT_FINANCE_BIC');
    }
}
