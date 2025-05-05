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

use ChameleonSystem\CoreBundle\Maintenance\Migrator62\Migrator62;

class Migrator
{
    /**
     * @param string $version
     *
     * @return void
     */
    public function migrate($version)
    {
        switch ($version) {
            case '6.2':
                $migrator = new Migrator62();
                break;
            default:
                throw new \InvalidArgumentException('Unsupported version:'.$version);
        }
        $migrator->migrate();
    }
}
