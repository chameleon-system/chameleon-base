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
use Twig\Environment;
use Twig\Error\Error;

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
    /**
     * @var Environment
     */
    private $twigEnvironment = null;

    /**
     * @var Environment
     */
    private $twigStringEnvironment;

    public function __construct(Environment $twigEnvironment, Environment $twigStringEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
        $this->twigStringEnvironment = $twigStringEnvironment;
    }

    /**
     * Returns a new instance. The instance uses the given string as snippet source.
     * It is possible to optionally provide it with a path to a file containing the snippet code.
     *
     * @static
     *
     * @param $sSource - the snippet source (or path to a file containing it)
     * @param int $iSourceType
     *
     * @return \TPkgSnippetRenderer
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
     * @param $sSource - the snippet source
     */
    public function setSource($sSource)
    {
        parent::setSource($sSource);
    }

    /**
     * @return Environment
     *
     * @throws ViewRenderException
     *
     * @deprecated since 6.3.6 - use the normal dependency injection
     */
    protected function &getTwigHandler()
    {
        if (null === $this->twigEnvironment) {
            throw new ViewRenderException('No twig environment set!');
        }

        return $this->twigEnvironment;
    }

    /**
     * Set the path to the snippet code.
     * It is possible to override the initially given source this way afterwards.
     *
     * @param $sPath - the path to the snippet code
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

        // TODO why didn't the old handling swallow up any "missing template" error?
        //  -> that is maybe the reason why missing twig templates always showed up simply as the file path?

        try {
            if ($this->getSourceType() !== IPkgSnippetRenderer::SOURCE_TYPE_STRING) {
                // use the normal file-only Twig environment

                $content = $this->twigEnvironment->render($this->getSource(), $this->getVars());
            } else {
                $content = $this->twigStringEnvironment->render($this->getSource(), $this->getVars());
            }
        } catch (Error $e) {
            throw new TPkgSnippetRenderer_SnippetRenderingException(
                sprintf("%s\nin file %s at line %s\n", $e->getMessage(), $e->getFile(), $e->getLine()),
                $e->getCode(),
                $e
            );
        }

        return $content;
    }
}
