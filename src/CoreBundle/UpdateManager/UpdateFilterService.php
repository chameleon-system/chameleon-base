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

class UpdateFilterService implements UpdateFilterServiceInterface
{
    /**
     * @var array<string, string[]>
     */
    private $excludeFolders = [
        'module' => ['dbbuilds'],
    ];

    /**
     * {@inheritdoc}
     */
    public function allowUpdate($type, $folderName)
    {
        if (isset($this->excludeFolders[$type]) && in_array($folderName, $this->excludeFolders[$type])) {
            return false;
        }

        return true;
    }
}
