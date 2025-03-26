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

/**
 * Class AddGlobalHeadIncludesListener returns the resources configured in the root directory of the snippets.
 * Those will be included no matter which modules are being loaded.
 */
readonly class AddGlobalHeadIncludesListener
{
    public function __construct(private \TPkgViewRendererSnippetDirectoryInterface $viewRendererSnippetDirectory)
    {
    }

    public function onGlobalHtmlHeaderInclude(HtmlIncludeEventInterface $event): void
    {
        $event->addData($this->viewRendererSnippetDirectory->getResourcesForSnippetPackage(''));

        $event->addData([
            '<script src="'.\TGlobal::GetStaticURL('/chameleon/blackbox/javascript/jquery/jquery-3.7.1.min.js').'" type="text/javascript"></script>',
            '<script src="'.\TGlobal::GetStaticURLToWebLib('/wysiwyg/functions.js').'" type="text/javascript"></script>',
            '<link href="'.\TGlobal::GetStaticURLToWebLib('/css/cms_user_style/main.css').'" rel="stylesheet" type="text/css" />',
            '<link href="'.\TGlobal::GetStaticURLToWebLib('/iconFonts/fileIconVectors/file-icon-square-o.css').'" rel="stylesheet" type="text/css" />',
        ]);
    }
}
