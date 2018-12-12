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

class TCMSListManagerPortalDomains extends TCMSListManagerFullGroupTable
{
    /**
     * Allows deletion only if the domain is not the primary domain.
     *
     * @param string $id
     * @param array  $row
     *
     * @return string
     */
    public function CallBackFunctionBlockDeleteButton($id, $row)
    {
        if (false === $row['is_master_domain'] || '1' !== $row['is_master_domain']) {
            return parent::CallBackFunctionBlockDeleteButton($id, $row);
        }
        $translator = $this->getTranslator();

        return sprintf('<span title="%s" class="glyphicon glyphicon-remove" style="color: #d9534f; opacity: .5;"></span>',
            TGlobal::OutJS($translator->trans('chameleon_system_core.list.primary_domain_delete_not_allowed'))
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function usesManagedTables(): bool
    {
        return false;
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
