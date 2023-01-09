<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface ICMSSeoPatternItem
{
    /**
     * Get SEO pattern of actual Item. $sPatternIn will be modified (replaced) by
     * configured item pattern.
     *
     * Object of type TCMSRenderSeoPattern will take the $sPatternIn and replace
     * them with the placeholder values from return array.
     *
     * Format of $sPatternIn placeholder values is [{PLACEHOLDER_NAME}].
     *
     * Try: [{SHOW}] to display all avalible replacement values.
     *
     * @param string $sPatternIn SEO Pattern
     *
     * @return array Replacement values array
     *
     * @see TCMSRenderSeoPattern
     */
    public function GetSeoPattern($sPatternIn);
}
