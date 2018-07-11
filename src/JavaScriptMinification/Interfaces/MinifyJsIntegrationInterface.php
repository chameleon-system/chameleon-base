<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\JavaScriptMinification\Interfaces;

use ChameleonSystem\JavaScriptMinification\Exceptions\MinifyJsIntegrationException;

/**
 * MinifyJsIntegrationInterface defines a service integration to minify JavaScript.
 */
interface MinifyJsIntegrationInterface
{
    /**
     * Minify JavaScript content.
     *
     * @param string $jsContent
     *
     * @return string
     *
     * @throws MinifyJsIntegrationException
     */
    public function minifyJsContent($jsContent);
}
