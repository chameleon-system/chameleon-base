<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\NewsletterBundle\Listener;

use ChameleonSystem\NewsletterBundle\PostProcessing\Bridge\NewsletterUserDataFactory;
use ChameleonSystem\NewsletterBundle\PostProcessing\PostProcessorCollectionService;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class NewsletterPostProcessorListener
{
    /**
     * @var PostProcessorCollectionService
     */
    private $postProcessorCollectionService;

    public function __construct(PostProcessorCollectionService $postProcessorCollectionService)
    {
        $this->postProcessorCollectionService = $postProcessorCollectionService;
    }

    /**
     * @return void
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        $newsletterUserId = $event->getRequest()->query->get('TPkgNewsletterUserId', null);
        if (null === $newsletterUserId) {
            return;
        }
        $campaignId = $event->getRequest()->query->get('TPkgNewsletterCampaign', null);
        if (null === $campaignId) {
            return;
        }

        $newsletterUser = \TPkgNewsletterUser::GetNewInstance();
        $loaded = $newsletterUser->Load($newsletterUserId);

        if (false === $loaded) {
            return;
        }

        $campaign = \TdbPkgNewsletterCampaign::GetNewInstance();
        $campaignLoaded = $campaign->Load($campaignId);

        if (false === $campaignLoaded) {
            return;
        }

        $dataFactory = new NewsletterUserDataFactory();
        $userData = $dataFactory->createNewsletterUserData($campaign, $newsletterUser);
        $content = $this->postProcessorCollectionService->process($event->getResponse()->getContent(), $userData);

        $event->getResponse()->setContent($content);
    }
}
