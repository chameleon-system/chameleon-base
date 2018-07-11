<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_ShopCMSArticleImage($field, $row, $fieldName)
{
    $oArticle = TdbShopArticle::GetNewInstance();
    /** @var $oArticle TdbShopArticle */
    if ($oArticle->Load($field)) {
        return $oArticle->RenderPreviewThumbnail('cms', 'cms');
    } else {
        return 'no article';
    }
}
