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

class NewsletterUserDataFactory implements NewsletterUserDataFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createNewsletterUserData(\TdbPkgNewsletterCampaign $campaign, \TdbPkgNewsletterUser $user, $newsletterGroupId = null, $newsletterGroups = null, $addAllUnsubscribeLinks = false)
    {
        $salutationObject = $user->GetFieldDataExtranetSalutation();
        $salutation = $salutationObject ? $salutationObject->GetName() : '';

        $firstname = $user->fieldFirstname;
        $lastname = $user->fieldLastname;
        $email = $user->fieldEmail;

        $unsubscribelink = '';

        // we can only render an unsubscribe link, if we have a group. We might be able to determine the group from our new event listener that substitutes the placeholders on html pages, but currently the code is trapped in a protected method inside the campaign class, so it might not be available.
        if (null !== $newsletterGroupId) {
            if (defined('CHAMELEON_PKG_NEWSLETTER_NEW_MODULE') && CHAMELEON_PKG_NEWSLETTER_NEW_MODULE === true) {
                if (null !== $newsletterGroups) {
                    $unsubscribelink = $user->GetLinkUnsubscribeWithCode($newsletterGroups, $addAllUnsubscribeLinks);
                } else {
                    $unsubscribelink = $user->GetLinkUnsubscribeWithCode($newsletterGroupId, $addAllUnsubscribeLinks);
                }
            } else {
                if (null !== $newsletterGroups) {
                    $unsubscribelink = $user->GetLinkUnsubscribe($newsletterGroups);
                } else {
                    $unsubscribelink = $user->GetLinkUnsubscribe($newsletterGroupId);
                }
            }
        }

        $htmllink = $campaign->GetLinkToHTMLNewsletter($user);

        $newsletterUserDataModel = new NewsletterUserDataModel($salutation, $firstname, $lastname, $email, $unsubscribelink, $htmllink);

        if ('' !== $user->fieldDataExtranetUserId) {
            $newsletterUserDataModel->setExtranetUserId($user->fieldDataExtranetUserId);
        }

        return $newsletterUserDataModel;
    }
}
