<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Maintenance\MaintenanceMode;

use ChameleonSystem\CoreBundle\Exception\MaintenanceModeErrorException;

interface MaintenanceModeServiceInterface
{
    public function isActive(): bool;

    /**
     * @throws MaintenanceModeErrorException
     */
    public function activate(): void;

    /**
     * @throws MaintenanceModeErrorException
     */
    public function deactivate(): void;
}
