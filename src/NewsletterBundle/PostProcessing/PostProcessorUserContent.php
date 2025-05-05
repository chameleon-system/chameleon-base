<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\NewsletterBundle\PostProcessing;

use ChameleonSystem\NewsletterBundle\PostProcessing\Bridge\NewsletterUserDataModel;

class PostProcessorUserContent implements PostProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($text, NewsletterUserDataModel $userData)
    {
        $result = str_replace(['[{salutation}]', '[{firstname}]', '[{lastname}]', '[{email}]', '[{unsubscribelink}]', '[{htmllink}]'],
            [$userData->getSalutation(), $userData->getFirstName(), $userData->getLastName(), $userData->getEMail(), $userData->getUnsubscribeLink(), $userData->getHtmlLink()],
            $text);

        return $result;
    }
}
