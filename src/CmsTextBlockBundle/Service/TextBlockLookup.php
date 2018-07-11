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
     * @param string $systemName
     * @param $textContainerWidth
     *
     * @return string
     */
    public function getText($systemName, $textContainerWidth)
    {
        $textBlock = $this->getTextBlock($systemName);
        $text = $this->getTextFromTextBlock($textBlock, $textContainerWidth);

        return $text;
    }

    /**
     * @param string $systemName
     *
     * @return string
     */
    public function getHeadline($systemName)
    {
        $textBlock = $this->getTextBlock($systemName);
        $headline = $this->getHeadlineFormTextBlock($textBlock);

        return $headline;
    }

    /**
     * @param string $systemName
     *
     * @return TdbPkgCmsTextBlock
     */
    public function getTextBlock($systemName)
    {
        $textBlock = TdbPkgCmsTextBlock::GetInstanceFromSystemName($systemName);

        return $textBlock;
    }

    /**
     * @param TdbPkgCmsTextBlock $textBlock
     * @param $textContainerWidth
     *
     * @return string
     */
    public function getTextFromTextBlock($textBlock, $textContainerWidth)
    {
        $text = '';
        if (null !== $textBlock) {
            $text = $textBlock->GetTextField('content', $textContainerWidth);
        }

        return $text;
    }

    /**
     * @param TdbPkgCmsTextBlock $textBlock
     *
     * @return string
     */
    public function getHeadlineFormTextBlock($textBlock)
    {
        if (null !== $textBlock) {
            return $textBlock->fieldName;
        }

        return '';
    }
}
