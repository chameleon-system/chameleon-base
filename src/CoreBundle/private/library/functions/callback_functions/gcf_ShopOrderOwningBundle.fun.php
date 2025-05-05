<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_ShopOrderOwningBundle($iShopOrderItemId, $row, $fieldName)
{
    $sOwner = '&nbsp;';
    $oOrdeItem = TdbShopOrderItem::GetNewInstance();
    /* @var $oOrdeItem TdbShopOrderItem */
    $oOrdeItem->Load($iShopOrderItemId);
    $oOwner = $oOrdeItem->GetOwningBundleOrderItem();
    if ($oOwner) {
        $sOwner = TGlobal::OutHTML($oOwner->fieldName);
    }

    return $sOwner;
}
