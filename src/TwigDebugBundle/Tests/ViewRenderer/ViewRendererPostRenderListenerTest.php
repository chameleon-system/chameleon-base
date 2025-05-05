<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\TwigDebugBundle\Tests\ViewRenderer;

use ChameleonSystem\TwigDebugBundle\ViewRenderer\ViewRendererPostRenderListener;
use ChameleonSystem\ViewRendererBundle\objects\ViewRendererEvent;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class ViewRendererPostRenderListenerTest extends TestCase
{
    /**
     * @test
     */
    public function itModifiesTheContent()
    {
        $prophet = new Prophet();
        $includes = ['foo', 'bar'];

        $listener = new ViewRendererPostRenderListener();

        $evt = new ViewRendererEvent('content', ['foo', 'bar'], 'fooView');

        $expected = <<<EOF


<!-- START SNIPPET
 - snippet: fooView
 - mappers: foo, bar
-->

content

<!-- END SNIPPET fooView -->


EOF;

        $listener->handlePostRender($evt);

        $this->assertEquals($expected, $evt->getContent());
    }
}
