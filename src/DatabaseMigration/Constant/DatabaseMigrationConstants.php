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
    const MIGRATION_COUNTER_PREFIX = 'migration-counter-';

    const RESPONSE_STATE_SUCCESS = 'SUCCESS';
    const RESPONSE_STATE_ERROR = 'ERROR';

    const UPDATE_STATE_ERROR = 'error';
    const UPDATE_STATE_EXECUTED = 'executed';
    const UPDATE_STATE_SKIPPED = 'skipped';
}
