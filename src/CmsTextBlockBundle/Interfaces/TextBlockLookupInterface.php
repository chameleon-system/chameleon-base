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
     * @param string $systemName
     * @param $textContainerWidth
     *
     * @return string
     */
    public function getText($systemName, $textContainerWidth);

    /**
     * @param string $systemName
     *
     * @return string
     */
    public function getHeadline($systemName);

    /**
     * @param string $systemName
     *
     * @return TdbPkgCmsTextBlock
     */
    public function getTextBlock($systemName);

    /**
     * @param TdbPkgCmsTextBlock $textBlock
     * @param $textContainerWidth
     *
     * @return string
     */
    public function getTextFromTextBlock($textBlock, $textContainerWidth);

    /**
     * @param TdbPkgCmsTextBlock $textBlock
     *
     * @return string
     */
    public function getHeadlineFormTextBlock($textBlock);
}
