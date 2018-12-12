<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSListManagerCmsTableConf extends TCMSListManagerFullGroupTable
{
    public function _AddFunctionColumn()
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function usesManagedTables(): bool
    {
        return false;
    }
}
