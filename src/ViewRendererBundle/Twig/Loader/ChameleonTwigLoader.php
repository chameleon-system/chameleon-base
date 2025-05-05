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

use Twig\Loader\FilesystemLoader;

class ChameleonTwigLoader extends FilesystemLoader
{
    /**
     * @var \TPkgViewRendererSnippetDirectoryInterface
     */
    private $snippetDirectory;

    /**
     * @var bool
     */
    private $initialized = false;

    public function __construct(\TPkgViewRendererSnippetDirectoryInterface $snippetDirectory)
    {
        $this->snippetDirectory = $snippetDirectory;
        parent::__construct();
    }

    /**
     * @return void
     */
    private function init()
    {
        if ($this->initialized) {
            return;
        }
        $this->setPaths(array_reverse($this->snippetDirectory->getBasePathsFromInstance()));
        $this->initialized = true;
    }

    /**
     * @param string $name
     * @param bool $throwException
     *
     * @return string|false|null
     */
    protected function findTemplate($name, $throwException = true)
    {
        $this->init();

        return parent::findTemplate($name, $throwException);
    }
}
