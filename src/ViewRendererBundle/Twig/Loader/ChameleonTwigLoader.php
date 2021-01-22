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

use Symfony\Component\HttpKernel\KernelInterface;
use TPkgViewRendererSnippetDirectoryInterface;
use Twig\Error\LoaderError;

class ChameleonTwigLoader extends \Twig_Loader_Filesystem
{
    /**
     * @var TPkgViewRendererSnippetDirectoryInterface
     */
    private $snippetDirectory;

    private $initialized = false;
    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(TPkgViewRendererSnippetDirectoryInterface $snippetDirectory, KernelInterface $kernel)
    {
        $this->snippetDirectory = $snippetDirectory;
        $this->kernel = $kernel;
    }

    private function init()
    {
        if ($this->initialized) {
            return;
        }
        $this->setPaths(array_reverse($this->snippetDirectory->getBasePathsFromInstance()));
        $this->initialized = true;
    }

    /**
     * @param string $resource
     * @throws LoaderError
     */
    public function addBackendModuleResource(string $resource): void
    {
        $this->init(); // otherwise later the setPath() there would delete the paths added here

        // TODO (?) this not specific to a certain module (but could be if handled directly in the rendering code)

        $path = \rtrim($this->kernel->locateResource($resource), '/').'/Resources/views/snippets-cms';

        if (false === \is_dir($path)) {
            return;
        }

        // TODO there is no "double" check (CoreBundle is doubled then)

        $this->addPath($path);

        // TODO log error instead of throw?

        // TODO what about src/extensions/pkgGenericTableExport?

        // TODO the module type is only "Customer" for image crop; but it is present in the url (ImageCropEditorModule::getUrlParameters) - for the "BackendPageDefs"
        // NOTE DataAccessCmsMasterPagedefFile::PageDefinitionFile has a special case for this

        // TODO is the module type "CoreBundle" then still used (would simply be @ChameleonSystemCoreBundle now): Sidebar, NavigationTree
    }

    protected function findTemplate($name, $throwException = true)
    {
        $this->init();

        return parent::findTemplate($name, $throwException);
    }
}
