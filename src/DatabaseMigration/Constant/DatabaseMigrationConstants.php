<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\Constant;

class DatabaseMigrationConstants
{
    public const MIGRATION_COUNTER_PREFIX = 'migration-counter-';

    public const RESPONSE_STATE_SUCCESS = 'SUCCESS';
    public const RESPONSE_STATE_ERROR = 'ERROR';

    public const UPDATE_STATE_ERROR = 'error';
    public const UPDATE_STATE_EXECUTED = 'executed';
    public const UPDATE_STATE_SKIPPED = 'skipped';
}
