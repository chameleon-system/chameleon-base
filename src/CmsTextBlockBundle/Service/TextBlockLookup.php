<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsTextBlockBundle\Service;

use ChameleonSystem\CmsTextBlockBundle\Interfaces\TextBlockLookupInterface;

class TextBlockLookup implements TextBlockLookupInterface
{
    public function getRenderedText(string $systemName, int $textContainerWidth = 1200, array $placeholders = []): string
    {
        $textBlock = $this->getTextBlock($systemName);

        if (null === $textBlock) {
            return '';
        }

        return $this->getRenderedTextFromTextBlock($textBlock, $textContainerWidth, $placeholders);
    }

    /**
     * @param string $systemName
     *
     * @return string
     */
    public function getHeadline($systemName)
    {
        $textBlock = $this->getTextBlock($systemName);

        return $this->getHeadlineFromTextBlock($textBlock);
    }

    /**
     * @param string $systemName
     *
     * @return \TdbPkgCmsTextBlock|null
     */
    public function getTextBlock($systemName)
    {
        return \TdbPkgCmsTextBlock::GetInstanceFromSystemName($systemName);
    }

    public function getRenderedTextFromTextBlock(?\TdbPkgCmsTextBlock $textBlock, int $textContainerWidth = 1200, array $placeHolders = []): string
    {
        if (null === $textBlock) {
            return '';
        }

        return $textBlock->GetTextField('content', $textContainerWidth, false, $placeHolders);
    }

    public function getHeadlineFromTextBlock(?\TdbPkgCmsTextBlock $textBlock): string
    {
        if (null === $textBlock) {
            return '';
        }

        return $textBlock->fieldName;
    }
}
