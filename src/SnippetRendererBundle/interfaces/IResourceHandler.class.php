<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IResourceHandler
{
    /**
     * Adds a CSS resource.
     *
     * @abstract
     *
     * @param string $sResource
     *
     * @return void
     */
    public function addCSSResource($sResource);

    /**
     * Adds a JavaScript resource.
     *
     * @abstract
     *
     * @param string $sResource
     *
     * @return void
     */
    public function addJSResource($sResource);

    /**
     * Handle the given resources.
     *
     * @abstract
     *
     * @return array $aResult - collected resources as array
     */
    public function handleResources();

    /**
     * Handle cached resource strings.
     *
     * @abstract
     *
     * @param string $aPlainResources
     *
     * @return string
     */
    public function handlePlainResources($aPlainResources);
}
