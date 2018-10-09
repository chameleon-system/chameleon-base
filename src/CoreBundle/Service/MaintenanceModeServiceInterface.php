<?php

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\Exception\MaintenanceModeErrorException;

interface MaintenanceModeServiceInterface
{
    /**
     * Returns if the maintenance mode is activated (by file).
     * Method should not query the database.
     *
     * @return bool
     */
    public function isActivated(): bool;

    /**
     * Returns if the maintenance mode is activated (in database).
     *
     * @return bool
     *
     * @throws MaintenanceModeErrorException
     */
    public function isActivatedInDb(): bool;

    /**
     * @throws MaintenanceModeErrorException
     */
    public function activate(): void;

    /**
     * @throws MaintenanceModeErrorException
     */
    public function deactivate(): void;
}
