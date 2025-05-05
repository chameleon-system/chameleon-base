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
 * MinifyJsServiceInterface defines a service which uses a before set JavaScript minification implementation to minify JavaScript.
 */
interface MinifyJsServiceInterface
{
    /**
     * Minify JavaScript content with a previously set minify integration for example (Jshrink).
     *
     * @param string $jsContent
     *
     * @return string
     *
     * @throws MinifyJsIntegrationException
     */
    public function minifyJsContent($jsContent);

    /**
     * Set the JavaScript minify integration.
     *
     * @return void
     */
    public function setMinifierJsIntegration(MinifyJsIntegrationInterface $minifyJsIntegration);
}
