<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\UpdateCounterMigrationBundle\Exception\InvalidMigrationCounterException;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;

class MTLoginEndPoint extends TCMSModelBase
{
    public function Execute()
    {
        $this->data = parent::Execute();

        $this->data['username'] = $this->global->GetUserData('username');

        $this->data['redirectParams'] = '';
        if ($this->global->UserDataExists('redirectParams')) {
            $this->data['redirectParams'] = $this->global->GetUserData('redirectParams');
        } 

        if ($this->global->CMSUserDefined()) {
            $this->getRedirectService()->redirectToActivePage([]);
        }

        return $this->data;
    }

    private function getDatabaseConnection(): Connection
    {
        return ServiceLocator::get('database_connection');
    }

    private function getRedirectService(): ICmsCoreRedirect
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }
}
