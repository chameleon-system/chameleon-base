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

class StripVirtualFieldsFromQuery
{
    /**
     * @var VirtualFieldManagerInterface
     */
    private $virtualFieldManager;

    public function __construct(VirtualFieldManagerInterface $virtualFieldManager)
    {
        $this->virtualFieldManager = $virtualFieldManager;
    }

    /**
     * @param string $query
     *
     * @return string
     */
    public function stripNonExistingFields($query)
    {
        $tableNamePattern = '/^(INSERT INTO|UPDATE) `(\\w+)`/iu';
        $matches = [];
        if (preg_match($tableNamePattern, $query, $matches) > 0) {
            $table = $matches[2];
            $virtualFields = $this->virtualFieldManager->getVirtualFieldsForTable($table);
            foreach ($virtualFields as $matchingField) {
                $replacePattern = "/(,\s|,\n?\s*)+`{$matchingField}` = '[^']*'/iu";
                $query = preg_replace($replacePattern, '', $query);
                $replacePattern = "/`{$matchingField}` = '[^']*'(,\s|\n?)+/iu";
                $query = preg_replace($replacePattern, '', $query);
            }
        }

        return $query;
    }
}
