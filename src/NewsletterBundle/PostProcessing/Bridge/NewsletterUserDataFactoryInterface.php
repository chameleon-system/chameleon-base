<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\NewsletterBundle\PostProcessing\Bridge;

use TdbPkgNewsletterUser;

interface NewsletterUserDataFactoryInterface
{
    /**
     * Creates a data model based on an existing TdbPkgNewsletterUser instance.
     *
     * @param string $newsletterGroupId
     * @param array|string $newsletterGroups
     * @param bool $addAllUnsubscribeLinks
     *
     * @return NewsletterUserDataModel
     */
    public function createNewsletterUserData(\TdbPkgNewsletterCampaign $campaign, \TdbPkgNewsletterUser $newsletterUser, $newsletterGroupId = null, $newsletterGroups = null, $addAllUnsubscribeLinks = false);
}
