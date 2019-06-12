<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\ViewRendererBundle\Compiler\Stylesheet\CompilerInterface;
use ChameleonSystem\ViewRendererBundle\objects\TPkgViewRendererLessCompiler;

class TPkgViewRendererConfigToLessMapper extends AbstractViewMapper
{
    /**
     * @var TPkgViewRendererLessCompiler
     */
    private $compiler;
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;

    /**
     * @param CompilerInterface|null $compiler
     * @param PortalDomainServiceInterface|null $portalDomainService
     */
    public function __construct(CompilerInterface $compiler = null, PortalDomainServiceInterface $portalDomainService = null)
    {
        if (null === $compiler) {
            $this->compiler = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_view_renderer.compiler');
        } else {
            $this->compiler = $compiler;
        }
        if (null === $portalDomainService) {
            $this->portalDomainService = \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
        } else {
            $this->portalDomainService = $portalDomainService;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $requirements)
    {
        $requirements->NeedsSourceObject('inTemplateEngineMode', 'bool', false);
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $visitor, $cachingEnabled, IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        $compiledCssSource = $this->compiler->getCompiledCssUrl($this->portalDomainService->getActivePortal());
        if (true === $visitor->GetSourceObject('inTemplateEngineMode')) {
            if (false === strpos($compiledCssSource, '?')) {
                $separator = '?';
            } else {
                $separator = '&';
            }
            $compiledCssSource .= $separator.'__modulechooser=true';
        }
        $visitor->SetMappedValue('sCompliedCSSSource', $compiledCssSource);
    }
}
