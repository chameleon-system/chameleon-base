<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\JavaScriptMinification\Service;

use ChameleonSystem\JavaScriptMinification\Interfaces\MinifyJsIntegrationInterface;
use ChameleonSystem\JavaScriptMinification\Interfaces\MinifyJsServiceInterface;

class MinifyJsService implements MinifyJsServiceInterface
{
    /**
     * @var MinifyJsIntegrationInterface
     */
    private $minifyJsIntegration;

    /**
     * {@inheritdoc}
     */
    public function minifyJsContent($jsContent)
    {
        if (null === $this->minifyJsIntegration) {
            return $jsContent;
        }
        $jsContent = $this->minifyJsIntegration->minifyJsContent($jsContent);

        return $jsContent;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinifierJsIntegration(MinifyJsIntegrationInterface $minifyJsIntegration)
    {
        $this->minifyJsIntegration = $minifyJsIntegration;
    }
}
