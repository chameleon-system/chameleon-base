<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\Sidebar;

abstract class AbstractMenuItemProvider implements MenuItemProviderInterface
{
    protected function addHistoryParameters(string $url): string
    {
        $queryParams = [
            '_rmhist' => 'true',
            '_histid' => '0',
        ];

        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'] ?? '', $existingParams);
        $mergedParams = array_merge($existingParams, $queryParams);
        $newQuery = http_build_query($mergedParams);

        $base = $parsedUrl['scheme'] ?? '';
        if ('' !== $base) {
            $base .= '://'.($parsedUrl['host'] ?? '');
        }
        $base .= $parsedUrl['path'] ?? '';

        return $base.'?'.$newQuery;
    }
}
