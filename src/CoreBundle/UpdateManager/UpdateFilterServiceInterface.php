<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\UpdateManager;

interface UpdateFilterServiceInterface
{
    /**
     * @param string $type
     * @param string $folderName - path relative to the update type root folder (for a package that would have the form /packagename/packagename-updates)
     *
     * @return bool
     */
    public function allowUpdate($type, $folderName);
}
