<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgTrackViews_ShopArticle extends TPkgTrackViews_ShopArticleAutoParent
{
    /**
     * increase the product view counter by one.
     *
     * @return void
     */
    public function UpdateProductViewCount()
    {
        parent::UpdateProductViewCount();
        if (CMS_SHOP_TRACK_ARTICLE_DETAIL_VIEWS && !is_null($this->id)) {
            TPkgTrackObjectViews::GetInstance()->TrackObject($this, false, false);
        }
    }
}
