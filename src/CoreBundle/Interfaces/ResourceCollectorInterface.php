<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Interfaces;

interface ResourceCollectorInterface
{
    /**
     * Checks if system is allowed to use resource collection.
     * Was used to disable resource collection in template engine.
     *
     * @return bool
     */
    public function IsAllowed();

    /**
     * Combine multiple resource files into one file.
     *
     * @param string $pageContent
     *
     * @return string - the processed page content
     */
    public function CollectExternalResources($pageContent);
}
