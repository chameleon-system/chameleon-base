<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\SanityCheckChameleonBundle\Bridge\Chameleon\Module\CMSSanityCheck;

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\SanityCheck\Handler\CheckHandlerInterface;
use ChameleonSystem\SanityCheck\Output\AbstractTranslatingCheckOutput;
use ChameleonSystem\SanityCheck\Resolver\OutputResolverInterface;
use ChameleonSystem\SanityCheckBundle\Resolver\CheckDataHolderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use TModelBase;

/**
 * CMSSanityCheck is a module that integrates the SanityCheckBundle into the Chameleon backend.
 * If you don't use Chameleon, just ignore this file and any missing class errors your IDE might throw.
 */
class CMSSanityCheck extends TModelBase
{
    /** @var RequestStack $requestStack */
    private $requestStack;
    /** @var CheckDataHolderInterface $checkDataHolder */
    private $checkDataHolder;
    /** @var CheckHandlerInterface $checkHandler */
    private $checkHandler;
    /** @var OutputResolverInterface $outputResolver */
    private $outputResolver;
    /** @var LanguageServiceInterface $languageService */
    private $languageService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param RequestStack             $requestStack
     * @param CheckDataHolderInterface $checkDataHolder
     * @param CheckHandlerInterface    $checkHandler
     * @param OutputResolverInterface  $outputResolver
     * @param LanguageServiceInterface $languageService
     * @param TranslatorInterface      $translator
     */
    public function __construct(
        RequestStack $requestStack,
        CheckDataHolderInterface $checkDataHolder,
        CheckHandlerInterface $checkHandler,
        OutputResolverInterface $outputResolver,
        LanguageServiceInterface $languageService,
        TranslatorInterface $translator
    ) {
        parent::__construct();
        $this->requestStack = $requestStack;
        $this->checkDataHolder = $checkDataHolder;
        $this->checkHandler = $checkHandler;
        $this->outputResolver = $outputResolver;
        $this->languageService = $languageService;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function &Execute()
    {
        parent::Execute();

        $this->handleCheckExecution();
        $this->gatherTemplateData();
        $this->data['translator'] = $this->translator;

        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = '<link href="/bundles/chameleonsystemsanitycheckchameleon/css/sanitycheck.css" rel="stylesheet" type="text/css" />';

        return $aIncludes;
    }

    /**
     * @return void
     */
    private function handleCheckExecution()
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $doChecks = false;
        $thingsToCheck = null;
        if (null !== $request->get('singleCheck')) {
            $thingsToCheck = $request->get('singleCheck');
            $doChecks = true;
        } elseif (null !== $request->get('bundleCheck')) {
            $thingsToCheck = $request->get('bundleCheck');
            $doChecks = true;
        }

        if ($doChecks) {
            $outcomeList = $this->checkHandler->checkSome($thingsToCheck);
            /** @var AbstractTranslatingCheckOutput $output */
            $output = $this->outputResolver->get('default');
            $output->setLocale($this->languageService->getActiveLanguage()->fieldIso6391);

            foreach ($outcomeList as $outcome) {
                $output->gather($outcome);
            }
            $output->commit();
        }
    }

    /**
     * @return void
     */
    private function gatherTemplateData()
    {
        $checks = $this->checkDataHolder->getAllChecks();
        sort($checks);
        $this->data['checks'] = $checks;

        $bundlesWithChecks = array_keys($this->checkDataHolder->getBundleCheckData());
        sort($bundlesWithChecks);
        $this->data['bundlesWithChecks'] = $bundlesWithChecks;
    }
}
