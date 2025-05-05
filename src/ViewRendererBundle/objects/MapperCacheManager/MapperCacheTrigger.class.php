<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MapperCacheTrigger implements IMapperCacheTrigger
{
    /**
     * @var array<string, array{table: string, id: string}>
     */
    private $aTrigger = [];

    /**
     * {@inheritdoc}
     */
    public function getTrigger()
    {
        if (0 === count($this->aTrigger)) {
            return null;
        }

        return array_values($this->aTrigger);
    }

    /**
     * {@inheritdoc}
     */
    public function addTrigger($sTable, $sId = null)
    {
        $sIDKey = $sId;
        if (null === $sId) {
            $sIDKey = '[NULL]';
        } elseif (is_array($sId)) {
            $sIDKey = implode(',', $sId);
        }
        $sKey = 'x'.md5('table:'.$sTable.'-id:'.$sIDKey);
        $this->aTrigger[$sKey] = ['table' => $sTable, 'id' => $sId];
    }
}
