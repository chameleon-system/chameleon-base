<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgExternalTracker_TShopArticle extends TPkgExternalTracker_TShopArticleAutoParent
{
    /**
     * increase the product view counter by one.
     *
     * @return void
     */
    public function UpdateProductViewCount()
    {
        parent::UpdateProductViewCount();
        if (!is_null($this->id)) {
            TdbPkgExternalTrackerList::GetActiveInstance()->AddStateData('oShopArticle', $this);
        }
    }
}
