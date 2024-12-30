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

interface TextBlockLookupInterface
{
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
     * @return \TdbPkgCmsTextBlock|null
     */
    public function getTextBlock($systemName);

    public function getRenderedTextFromTextBlock(?\TdbPkgCmsTextBlock $textBlock, int $textContainerWidth = 1200, array $placeHolders = []): string;

    public function getHeadlineFromTextBlock(\TdbPkgCmsTextBlock $textBlock): string;
}
