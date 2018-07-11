<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Translation\TranslatorInterface;

/**
 * male or female.
/**/
class TCMSFieldGenderSelector extends TCMSFieldOption
{
    /**
     * {@inheritdoc}
     */
    public function GetOptions()
    {
        parent::GetOptions();

        $translator = $this->getTranslationService();
        $this->options['m'] = $translator->trans('chameleon_system_core.field_gender.male', array(), 'admin');
        $this->options['f'] = $translator->trans('chameleon_system_core.field_gender.female', array(), 'admin');
    }

    /**
     * {@inheritdoc}
     */
    public function DataIsValid()
    {
        $dataIsValid = $this->CheckMandatoryField();
        if (false === $dataIsValid) {
            return $dataIsValid;
        }

        if ('m' === $this->data || 'f' === $this->data) {
            return true;
        }

        return false;
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslationService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
