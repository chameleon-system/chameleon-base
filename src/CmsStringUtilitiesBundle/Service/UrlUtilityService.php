<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsStringUtilitiesBundle\Service;

use ChameleonSystem\CmsStringUtilitiesBundle\Interfaces\UrlUtilityServiceInterface;

class UrlUtilityService implements UrlUtilityServiceInterface
{
    /**
     * {@inheritDoc}
     */
    public function addParameterToUrl($url, array $parameter)
    {
        $urlParts = parse_url($url);
        $queryParameterString = isset($urlParts['query']) ? $urlParts['query'] : '';
        $queryParameter = [];
        parse_str($queryParameterString, $queryParameter);
        $queryParameter = array_merge_recursive($queryParameter, $parameter);
        $urlParts['query'] = http_build_query($queryParameter);

        return $this->httpBuildUr($urlParts);
    }

    /**
     * @return string
     */
    private function httpBuildUr(array $urlParts)
    {
        $scheme = isset($urlParts['scheme']) ? $urlParts['scheme'].'://' : '';
        $host = isset($urlParts['host']) ? $urlParts['host'] : '';
        $port = isset($urlParts['port']) ? ':'.$urlParts['port'] : '';

        $user = isset($urlParts['user']) ? $urlParts['user'] : '';
        $pass = isset($urlParts['pass']) ? ':'.$urlParts['pass'] : '';

        if ('' === $scheme && ($host || $port || $user || $pass)) {
            $scheme = '//';
        }

        $access = ('' !== $user || '' !== $pass) ? $access = '@' : '';

        $path = isset($urlParts['path']) ? $urlParts['path'] : '';
        $query = isset($urlParts['query']) ? '?'.$urlParts['query'] : '';
        $fragment = isset($urlParts['fragment']) ? '#'.$urlParts['fragment'] : '';

        return "{$scheme}{$user}{$pass}{$access}{$host}{$port}{$path}{$query}{$fragment}";
    }
}
