<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\ViewRendererBundle\Bridge\Twig\Loader\TwigStringLoader;
use PHPUnit\Framework\TestCase;

class SnippetRendererTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!defined('TESTSUITE')) {
            define('TESTSUITE', true);
        }
        if (!defined('_DEVELOPMENT_MODE')) {
            define('_DEVELOPMENT_MODE', false);
        }
    }

    public function setUp(): void
    {
        // $twigEnv = $this->getMockBuilder('Twig_Environment')->getMock();
        $snippetRenderer = new TPkgSnippetRenderer(new Twig\Environment(new Twig\Loader\FilesystemLoader()), new Twig\Environment(new TwigStringLoader()), new Psr\Log\NullLogger());
        $container = new Symfony\Component\DependencyInjection\ContainerBuilder();
        $container->set('chameleon_system_snippet_renderer.snippet_renderer', $snippetRenderer);
        ServiceLocator::setContainer($container);
    }

    private function helper_injectCustomPaths($oRenderer)
    {
        $oReflectionClass = new ReflectionClass('TPkgSnippetRenderer');
        $oReflectionMethod = $oReflectionClass->getMethod('setBasePaths');
        $oReflectionMethod->setAccessible(true);
        $oReflectionMethod->invoke($oRenderer, [__DIR__.'/_files']);
    }

    public function testSetCapturedVar()
    {
        $renderer = TPkgSnippetRenderer::GetNewInstance('', false);
        $renderer->setCapturedVarStart('bla');
        echo 'BARBAZ';
        $renderer->setCapturedVarStop();
        $renderer->setCapturedVarStart('blubb');
        echo 'BARBAZBAZZ';
        $renderer->setCapturedVarStop();

        $oRefObj = new ReflectionObject($renderer);
        $oAtr = $oRefObj->getMethod('getVars');
        $oAtr->setAccessible(true);
        $aSubstitutes = $oAtr->invoke($renderer);

        $aExpected = ['bla' => 'BARBAZ', 'blubb' => 'BARBAZBAZZ'];

        $this->assertEquals($aExpected, $aSubstitutes);
    }

    public function testWrongEndingOfCapture()
    {
        $this->expectException(BadMethodCallException::class);

        $renderer = TPkgSnippetRenderer::GetNewInstance('', false);
        $renderer->setCapturedVarStop();
    }

    public function testWrongStartingOfCapture()
    {
        $this->expectException(BadMethodCallException::class);

        $renderer = TPkgSnippetRenderer::GetNewInstance('', false);
        $renderer->setCapturedVarStart('foo');
        try {
            $renderer->setCapturedVarStart('bar');
        } catch (BadMethodCallException $e) {
            ob_end_clean();
            throw $e;
        }
    }

    public function testRenderFile()
    {
        $sSource = file_get_contents(__DIR__.'/_files/fixture1.html.twig');
        $renderer = TPkgSnippetRenderer::GetNewInstance($sSource, IPkgSnippetRenderer::SOURCE_TYPE_STRING);
        $expected = file_get_contents(__DIR__.'/_files/fixture1_rendered.html');
        $renderer->setVar('title', 'Title');
        $renderer->setVar('header', 'Header');
        $renderer->setVar('body', 'Body');
        $result = $renderer->render();
        $this->assertEquals($expected, $result);
    }
}
