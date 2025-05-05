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

class PostProcessorCollectionService implements PostProcessorInterface
{
    /**
     * @var PostProcessorInterface[]
     */
    private $processors = [];

    /**
     * @return void
     */
    public function addPostProcessor(PostProcessorInterface $postProcessor)
    {
        $this->processors[] = $postProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function process($text, NewsletterUserDataModel $userData)
    {
        foreach ($this->processors as $processor) {
            $text = $processor->process($text, $userData);
        }

        return $text;
    }
}
