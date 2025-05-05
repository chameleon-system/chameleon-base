<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Contracts\Translation\TranslatorInterface;

class TCMSListManagerPortalDomains extends TCMSListManagerFullGroupTable
{
    /**
     * Allows deletion only if the domain is not the primary domain.
     *
     * @param string $id
     * @param array $row
     *
     * @return string
     */
    public function CallBackFunctionBlockDeleteButton($id, $row)
    {
        if (false === $row['is_master_domain'] || '1' !== $row['is_master_domain']) {
            return parent::CallBackFunctionBlockDeleteButton($id, $row);
        }
        $translator = $this->getTranslator();

        return sprintf('<span title="%s" class="fas fa-trash-alt text-danger"></span>',
            TGlobal::OutJS($translator->trans('chameleon_system_core.list.primary_domain_delete_not_allowed'))
        );
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
