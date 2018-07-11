<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\DataAccess;

/**
 * AbstractQueryDataAccessInterface defines a service that provides data access for the AbstractQuery class.
 */
interface AbstractQueryDataAccessInterface
{
    /**
     * Returns the ISO639-1 two-letter language identifier of the system's base language.
     *
     * @return string
     */
    public function getBaseLanguageIso();

    /**
     * Returns a flat list of translated fields for the passed table (base field names without language suffixes).
     *
     * @param string $tableName
     *
     * @return array
     */
    public function getTranslatedFieldsForTable($tableName);
}
