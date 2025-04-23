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
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Error\LoaderError;

/**
 * TPkgSnippetRenderer - a simple yet effective renderer for HTML snippets.
 *
 * It can handle snippet code including named blocks in the form of:
 * <code>
 *
 *   <html>
 *     <head>
 *       <title>[{ block title }]Template Title[{ endblock }]</title>
 *     </head>
 *     <body>
 *       <h1>[{ block header }]Template Header[{ endblock }]</h1>
 *       [{ block body }]
 *         Template Body
 *       [{ endblock }]
 *     </body>
 *    </html>
 *
 * </code>
 *
 *
 * Simple example usage:
 * <code>
 *
 *   $renderer = TPkgSnippetRenderer::GetNewInstance("FOO [{ block baz }]BAR[{ endblock }] BAZ");
 *   $renderer->setVar("baz", "FOOBAR");
 *   $result = $renderer->render();
 *
 * </code>
 *
 * More advanced example usage:
 * <code>
 *
 *  $renderer = TPkgSnippetRenderer::GetNewInstance("FOO [{ block baz }]BAR[{ endblock }] [{ block baz2 }]BAR[{ endblock }] BAZ");
 *  $renderer->setCapturedVarStart("baz");
 *  echo "BARBAZ";
 *  $renderer->setCapturedVarStop();
 *  $renderer->setCapturedVarStart("baz2");
 *  echo "BARBAZBAZZ";
 *  $renderer->setCapturedVarStop();
 *  $result = $renderer->render();
 *
 * </code>
 *
 * You can also use files as snippet source
 * <code>
 *
 *  $renderer = TPkgSnippetRenderer::GetNewInstance(dirname(__FILE__) . "/path/to/snippet.html", true);
 *  $renderer->setVar("body", "Body");
 *  $result = $renderer->render();
 *
 * </code>
 */
class TPkgSnippetRenderer extends PkgAbstractSnippetRenderer
{
    private Environment $twigEnvironment;
    private Environment $twigStringEnvironment;
    private LoggerInterface $logger;

    public function __construct(Environment $twigEnvironment, Environment $twigStringEnvironment, LoggerInterface $logger)
    {
        $this->twigEnvironment = $twigEnvironment;
        $this->twigStringEnvironment = $twigStringEnvironment;
        $this->logger = $logger;
    }

    public function getTwigEnvironment(): Environment
    {
        return $this->twigEnvironment;
    }

    /**
     * Returns a new instance. The instance uses the given string as snippet source.
     * It is possible to optionally provide it with a path to a file containing the snippet code.
     *
     * @static
     *
     * @param int $iSourceType
     * @param string $sSource - the snippet source (or path to a file containing it)
     *
     * @return TPkgSnippetRenderer
     */
    public static function GetNewInstance($sSource, $iSourceType = IPkgSnippetRenderer::SOURCE_TYPE_STRING)
    {
        $oNewInstance = ServiceLocator::get(
            'chameleon_system_snippet_renderer.snippet_renderer'
        );
        $oNewInstance->InitializeSource($sSource, $iSourceType);

        return $oNewInstance;
    }

    /**
     * Set the snippet source.
     * It is possible to override the initially given source this way afterwards.
     *
     * @param string $sSource - the snippet source
     *
     * @return void
     */
    public function setSource($sSource)
    {
        parent::setSource($sSource);
    }

    /**
     * Set the path to the snippet code.
     * It is possible to override the initially given source this way afterwards.
     *
     * @param string $sPath - the path to the snippet code
     *
     * @return void
     */
    public function setFilename($sPath)
    {
        parent::setSource($sPath);
        parent::setFilename($sPath);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if (null !== $this->getSourceModule()) {
            $this->setFilename($this->getSourceModule()->viewTemplate);
        }

        try {
            if (IPkgSnippetRenderer::SOURCE_TYPE_STRING !== $this->getSourceType()) {
                // use the normal file-only Twig environment

                $content = $this->twigEnvironment->render($this->getSource(), $this->getVars());
            } else {
                $content = $this->twigStringEnvironment->render($this->getSource(), $this->getVars());
            }
        } catch (LoaderError $e) {
            $message = sprintf('Error while rendering view %s: %s', $this->getSource(), $e->getMessage());
            $this->logger->error($message, ['error' => $e]);

            return $message;
        } catch (Error $e) {
            $previousException = $e->getPrevious();
            if (null !== $previousException) {
                $e = $previousException;
            }
            throw new TPkgSnippetRenderer_SnippetRenderingException(
                sprintf("%s\nin file %s at line %s\n", $e->getMessage(), $e->getFile(), $e->getLine()),
                $e->getCode(),
                $e
            );
        }

        return $content;
    }
}
