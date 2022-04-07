<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmstextBlockBundle\Service;

use ChameleonSystem\CmsTextBlockBundle\Interfaces\TextBlockLookupInterface;
use TdbPkgCmsTextBlock;

class TextBlockLookup implements TextBlockLookupInterface
{
    /**
     * {@inheritdoc}
     */
    public function getText($systemName, $textContainerWidth)
    {
        return $this->getRenderedText($systemName, $textContainerWidth);
    }

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
     * @return null|TdbPkgCmsTextBlock
     */
    public function getTextBlock($systemName)
    {
        return TdbPkgCmsTextBlock::GetInstanceFromSystemName($systemName);
    }

    /**
     * @deprecated since version 7.0.13 use getRenderedTextFromTextBlock instead
     *
     * @param TdbPkgCmsTextBlock $textBlock
     * @param int $textContainerWidth
     *
     * @return string
     */
    public function getTextFromTextBlock($textBlock, $textContainerWidth)
    {
        return $this->getRenderedTextFromTextBlock($textBlock, $textContainerWidth);
    }

    public function getRenderedTextFromTextBlock(?TdbPkgCmsTextBlock $textBlock, int $textContainerWidth = 1200, array $placeHolders = []): string
    {
        if (null === $textBlock) {
            return '';
        }

        return $textBlock->GetTextField('content', $textContainerWidth, false, $placeHolders);
    }

    /**
     * @deprecated since version 7.0.13 use getHeadlineFromTextBlock instead
     *
     * @param TdbPkgCmsTextBlock $textBlock
     *
     * @return string
     */
    public function getHeadlineFormTextBlock($textBlock)
    {
        return $this->getHeadlineFromTextBlock($textBlock);
    }

    public function getHeadlineFromTextBlock(?TdbPkgCmsTextBlock $textBlock): string
    {
        if (null === $textBlock) {
            return '';
        }

        return $textBlock->fieldName;
    }
}
