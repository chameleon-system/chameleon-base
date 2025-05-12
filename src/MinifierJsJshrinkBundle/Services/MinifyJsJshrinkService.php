<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MinifierJsJshrinkBundle\Services;

use ChameleonSystem\JavaScriptMinification\Exceptions\MinifyJsIntegrationException;
use ChameleonSystem\JavaScriptMinification\Interfaces\MinifyJsIntegrationInterface;
use JShrink\Minifier;

class MinifyJsJshrinkService implements MinifyJsIntegrationInterface
{
    /**
     * {@inheritdoc}
     */
    public function minifyJsContent($jsContent)
    {
        try {
            /** @var string|false $newJsContent */
            $newJsContent = Minifier::minify($jsContent);
            if (false === $newJsContent) {
                $newJsContent = '';
            }
        } catch (\Exception $e) {
            throw new MinifyJsIntegrationException($e->getMessage());
        }

        return $newJsContent;
    }
}
