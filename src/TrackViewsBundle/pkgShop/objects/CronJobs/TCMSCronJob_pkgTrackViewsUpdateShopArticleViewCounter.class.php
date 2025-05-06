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
 * updates the total view count - we do this to easily allow ordering by the value.
 **/
class TCMSCronJob_pkgTrackViewsUpdateShopArticleViewCounter extends TdbCmsCronjobs
{
    /**
     * @return void
     */
    protected function _ExecuteCron()
    {
        // make sure every article has an entry
        $query = 'INSERT INTO shop_article_stats (`id`, `shop_article_id`)
                  SELECT UUID(), `shop_article`.`id`
                    FROM `shop_article`
                    LEFT JOIN `shop_article_stats` ON `shop_article`.`id` = `shop_article_stats`.`shop_article_id`
                    WHERE `shop_article_stats`.`id` IS NULL
                    ';
        $this->getDatabaseConnection()->executeQuery($query);

        $query = "update shop_article_stats, pkg_track_object
                   SET shop_article_stats.stats_detail_views = pkg_track_object.count
                 WHERE shop_article_stats.shop_article_id = pkg_track_object.owner_id
                   AND pkg_track_object.table_name = 'shop_article'
                   AND pkg_track_object.time_block = 'xxxxxxxxxx'";
        $this->getDatabaseConnection()->executeQuery($query);
    }
}
