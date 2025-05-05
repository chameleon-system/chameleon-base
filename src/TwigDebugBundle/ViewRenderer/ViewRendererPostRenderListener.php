<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\TwigDebugBundle\ViewRenderer;

use ChameleonSystem\ViewRendererBundle\objects\ViewRendererEvent;

class ViewRendererPostRenderListener
{
    /**
     * @return void
     */
    public function handlePostRender(ViewRendererEvent $evt)
    {
        $content = $this->addHTMLHintsToResponse($evt->getMappers(), $evt->getViewName(), $evt->getContent());
        $evt->setContent($content);
    }

    /**
     * @param string $viewName
     * @param string $content
     *
     * @return string
     */
    private function addHTMLHintsToResponse(array $mappersUsed, $viewName, $content)
    {
        return "\n\n<!-- START SNIPPET\n - snippet: {$viewName}\n - mappers: ".implode(', ', $mappersUsed)."\n-->\n\n"
        .$content.
        "\n\n<!-- END SNIPPET {$viewName} -->\n\n";
    }
}
