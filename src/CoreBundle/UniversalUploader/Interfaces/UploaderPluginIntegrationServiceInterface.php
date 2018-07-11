<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\UniversalUploader\Interfaces;

/**
 * Used to integrate different components for file upload into UniversalUploader.
 */
interface UploaderPluginIntegrationServiceInterface
{
    /**
     * @return array
     */
    public function getHtmlHeadIncludes();
}
