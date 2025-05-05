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

interface PostProcessorInterface
{
    /**
     * returns a modified version of $text based on the data in $userData.
     *
     * @param string $text
     *
     * @return string
     */
    public function process($text, NewsletterUserDataModel $userData);
}
