<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * represents an abstract tag cloud.
 * /**/
class TCMSTagCloud extends TIterator
{
    public const QUERY_ITEM_KEY_NAME = 'cmsCloudItemKey';
    public const QUERY_ITEM_COUNT_NAME = 'cmsCloudItemCount';
    public const QUERY_ITEM_SIZE_NAME = 'cmsCloudItemSize';

    /**
     * return a cloud for the query. make sure the query includes the following:
     * _cloud_hits and any data needed for the data object used for the items.
     *
     * @param string $sQuery - must return at least a key for the cloud and a count as well as any other data
     *                       to use for the sCmsDbClassName object
     * @param string $sClassName - class name (must be a decendent of TCMSRecord) for each item
     * @param array $aCustomItems - (name=>relative weight) allows you to add custom tags to the list
     *
     * @return TCMSTagCloud
     */
    public static function GetCloud($sQuery, $sClassName, $aCustomItems = [], $iMinSize = 100, $iMaxSize = 250)
    {
        $oCloud = new self();

        $aData = [];
        $tRes = MySqlLegacySupport::getInstance()->query($sQuery);
        $tags = [];
        while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($tRes)) {
            if (!empty($aRow[self::QUERY_ITEM_KEY_NAME])) {
                $tags[$aRow[self::QUERY_ITEM_KEY_NAME]] = $aRow[self::QUERY_ITEM_COUNT_NAME];
                $aData[$aRow[self::QUERY_ITEM_KEY_NAME]] = $aRow;
            }
        }
        $iMinCount = 0;
        $iMaxCount = 0;
        if (count($tags) > 0) {
            $iMinCount = min(array_values($tags));
            $iMaxCount = max(array_values($tags));
        }

        $spread = $iMaxCount - $iMinCount;
        if (0 == $spread) {
            $spread = 1;
        }

        $step = ($iMaxSize - $iMinSize) / $spread;

        foreach ($tags as $key => $value) {
            $aData[$key][self::QUERY_ITEM_SIZE_NAME] = $iMinSize + (($value - $iMinCount) * $step);
        }

        // normalize weight for custom items
        reset($aCustomItems);
        foreach (array_keys($aCustomItems) as $sCustomWord) {
            $aCustomItems[$sCustomWord] = $iMaxSize * ($aCustomItems[$sCustomWord] / 100);
            if (!array_key_exists($sCustomWord, $aData)) {
                $aData[$sCustomWord] = [self::QUERY_ITEM_KEY_NAME => $sCustomWord];
            }
            $aData[$sCustomWord][self::QUERY_ITEM_SIZE_NAME] = $aCustomItems[$sCustomWord];
        }

        $aKeyList = array_keys($aData);
        shuffle($aKeyList);

        foreach ($aKeyList as $key) {
            /**
             * @var TCMSRecord $oItem
             */
            $oItem = new $sClassName();
            $oItem->LoadFromRow($aData[$key]);
            $oCloud->AddItem($oItem);
        }

        return $oCloud;
    }
}
