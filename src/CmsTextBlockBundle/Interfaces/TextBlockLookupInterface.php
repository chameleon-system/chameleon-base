<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsTextBlockBundle\Interfaces;

use TdbPkgCmsTextBlock;

interface TextBlockLookupInterface
{
    /**
     * @deprecated since version 7.0.13 use getRenderedText instead
     *
     * @param string $systemName
     * @param int $textContainerWidth
     *
     * @return string
     */
    public function getText($systemName, $textContainerWidth);

    public function getRenderedText(string $systemName, int $textContainerWidth = 1200, array $placeholders = []): string;

    /**
     * @param string $systemName
     *
     * @return string
     */
    public function getHeadline($systemName);

    /**
     * @param string $systemName
     *
     * @return  null|TdbPkgCmsTextBlock
     */
    public function getTextBlock($systemName);

    /**
     * @deprecated since version 7.0.13 use getRenderedTextFromTextBlock instead
     *
     * @param TdbPkgCmsTextBlock $textBlock
     * @param int $textContainerWidth
     *
     * @return string
     */
    public function getTextFromTextBlock($textBlock, $textContainerWidth);

    public function getRenderedTextFromTextBlock(?TdbPkgCmsTextBlock $textBlock, int $textContainerWidth = 1200, array $placeHolders = []): string;

    /**
     * @deprecated since version 7.0.13 use getHeadlineFromTextBlock instead
     *
     * @param TdbPkgCmsTextBlock $textBlock
     *
     * @return string
     */
    public function getHeadlineFormTextBlock($textBlock);

    public function getHeadlineFromTextBlock(TdbPkgCmsTextBlock $textBlock): string;
}
