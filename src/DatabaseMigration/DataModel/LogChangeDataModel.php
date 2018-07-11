<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\DataModel;

use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;

class LogChangeDataModel
{
    const TYPE_CUSTOM_QUERY = 'customQuery';
    const TYPE_INSERT = 'insert';
    const TYPE_UPDATE = 'update';
    const TYPE_DELETE = 'delete';

    /**
     * @var string|MigrationQueryData
     */
    private $data;
    /**
     * @var string
     */
    private $type;

    /**
     * @param string|MigrationQueryData $data An array of string and/or MigrationQueryData objects
     * @param string                    $type
     */
    public function __construct($data, $type = self::TYPE_CUSTOM_QUERY)
    {
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * @return string|MigrationQueryData
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
