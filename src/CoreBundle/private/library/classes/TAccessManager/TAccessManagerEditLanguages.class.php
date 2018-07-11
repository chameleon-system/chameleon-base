<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\DBAL\Connection;

class TAccessManagerEditLanguages
{
    /**
     * @var array a list of edit languages to which a user has been assigned
     */
    public $list = array();
    /**
     * @var bool set to true if the user has no language field in the user table
     */
    public $hasNoEditLanguages = false;

    public function AddEditLanguage($id, $name)
    {
        // will overwrite the language if it exists
        $this->list[$id] = $name;
    }

    public function IsInLanguage($languageId)
    {
        // returns true if the that language id exists, else false
        if (array_key_exists($languageId, $this->list)) {
            return true;
        } else {
            return false;
        }
    }

    public function GetLanguageList()
    {
        // returns a comma separated list of language ids, or false if no language is assigned
        if (count($this->list) < 1) {
            return false;
        }

        $databaseConnection = $this->getDatabaseConnection();

        return implode(',', array_map(array($databaseConnection, 'quote'), array_keys($this->list)));
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
