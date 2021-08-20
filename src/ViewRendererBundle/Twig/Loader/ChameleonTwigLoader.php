<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\Twig\Loader;

use TPkgViewRendererSnippetDirectoryInterface;

class ChameleonTwigLoader extends \Twig_Loader_Filesystem
{
    /**
     * @var TPkgViewRendererSnippetDirectoryInterface
     */
    private $snippetDirectory;

    private $initialized = false;

    public function __construct(TPkgViewRendererSnippetDirectoryInterface $snippetDirectory)
    {
        $this->snippetDirectory = $snippetDirectory;
    }

    private function init()
    {
        if ($this->initialized) {
            return;
        }
        $this->setPaths(array_reverse($this->snippetDirectory->getBasePathsFromInstance()));
        $this->initialized = true;
    }

    protected function findTemplate($name, $throwException = true)
    {
        $this->init();

        return parent::findTemplate($name, $throwException);
    }
}
