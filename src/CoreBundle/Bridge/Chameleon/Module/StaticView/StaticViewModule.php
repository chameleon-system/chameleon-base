<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\StaticView;

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;

/**
 * Can be used to display static information in the backend. The module requires the additional module config parameter
 * "targetView" containing the path to the view to be displayed (relative to snippets-cms). This path name may contain a
 * "[{language}]" placeholder. If present, this placeholder will be replaced with the current display language.
 */
class StaticViewModule extends \MTPkgViewRendererAbstractModuleMapper
{
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    public function __construct(LanguageServiceInterface $languageService)
    {
        parent::__construct();
        $this->languageService = $languageService;
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(\IMapperVisitorRestricted $visitor, $cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        $targetView = $this->aModuleConfig['targetView'];
        $targetLanguage = $this->languageService->getActiveLanguage()->fieldIso6391;
        $targetView = \str_replace('[{language}]', $targetLanguage, $targetView);

        $visitor->SetMappedValue('targetView', $targetView);
    }
}
