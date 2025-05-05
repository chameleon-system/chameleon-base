<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace esono\pkgCmsRouting;

use Symfony\Component\Routing\RouteCollection;

interface CollectionGeneratorInterface
{
    /**
     * @param array $config
     *
     * @return RouteCollection
     */
    public function getCollection($config, \TdbCmsPortal $portal, \TdbCmsLanguage $language);
}
