<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Maintenance;

/**
 * MigratorInterface is a common interface for migration helper classes. Migrators should only (!) be used to simplify
 * migration to newer Chameleon versions at an early stage of migrating, when the Symfony service container cannot be
 * built yet.
 */
interface MigratorInterface
{
    /**
     * Perform any migration steps.
     *
     * @return void
     */
    public function migrate();
}
