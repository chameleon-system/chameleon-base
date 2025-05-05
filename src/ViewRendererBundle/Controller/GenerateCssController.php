<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\Controller;

use ChameleonSystem\ViewRendererBundle\objects\TPkgViewRendererLessCompiler;
use Symfony\Component\HttpFoundation\Response;

class GenerateCssController
{
    /**
     * @var TPkgViewRendererLessCompiler
     */
    private $lessCompiler;
    /**
     * @var bool
     */
    private $isCssCacheEnabled;

    /**
     * @param bool $cacheLessFiles
     */
    public function __construct(TPkgViewRendererLessCompiler $lessCompiler, $cacheLessFiles)
    {
        $this->lessCompiler = $lessCompiler;
        $this->isCssCacheEnabled = $cacheLessFiles;
    }

    /**
     * @param string $portalId
     *
     * @return Response
     *
     * @throws \ViewRenderException
     */
    public function __invoke($portalId)
    {
        $portal = \TdbCmsPortal::GetNewInstance();
        $css = '';
        if (true === $portal->LoadFromField('cmsident', $portalId)) {
            $css = $this->lessCompiler->getGeneratedCssForPortal($portal);
            if (true === $this->isCssCacheEnabled) {
                $this->lessCompiler->writeCssFileForPortal($css, $portal);
            }
        }

        return new Response($css, Response::HTTP_OK, [
            'Content-Type' => 'text/css',
        ]);
    }
}
