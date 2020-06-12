<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\EventListener;

use ChameleonSystem\CoreBundle\Event\HtmlIncludeEventInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use TGlobal;

/**
 * Class AddJqueryIncludeListener returns the resources configured in the root directory of the snippets.
 * Those will be included no matter which modules are being loaded.
 */
class AddGlobalHeadIncludesListener
{
    /**
     * @var \TPkgViewRendererSnippetDirectoryInterface
     */
    private $viewRendererSnippetDirectory;

    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    public function __construct(\TPkgViewRendererSnippetDirectoryInterface $viewRendererSnippetDirectory, RequestInfoServiceInterface $requestInfoService)
    {
        $this->viewRendererSnippetDirectory = $viewRendererSnippetDirectory;
        $this->requestInfoService = $requestInfoService;
    }

    /**
     * @param HtmlIncludeEventInterface $event
     */
    public function onGlobalHtmlHeaderInclude(HtmlIncludeEventInterface $event)
    {
        if (true === $this->requestInfoService->isBackendMode()) {
            $event->addData(
                [
                    '<script src="'.TGlobal::GetStaticURLToWebLib( '/javascript/cms.js').'" type="text/javascript"></script>',
                ]
            );
        }

        $event->addData($this->viewRendererSnippetDirectory->getResourcesForSnippetPackage(''));

        $event->addData([
            '<script src="'.TGlobal::GetStaticURLToWebLib('/wysiwyg/functions.js').'" type="text/javascript"></script>',
            '<link href="'.TGlobal::GetStaticURLToWebLib('/css/cms_user_style/main.css').'" rel="stylesheet" type="text/css" />',
            '<link href="'.TGlobal::GetStaticURLToWebLib('/iconFonts/fileIconVectors/file-icon-square-o.css').'" rel="stylesheet" type="text/css" />',
        ]);
    }
}
