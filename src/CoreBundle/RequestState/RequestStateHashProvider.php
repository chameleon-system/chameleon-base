<?php

namespace ChameleonSystem\CoreBundle\RequestState;

use ChameleonSystem\CoreBundle\RequestState\Interfaces\HashCalculationLockInterface;
use ChameleonSystem\CoreBundle\RequestState\Interfaces\RequestStateElementProviderInterface;
use ChameleonSystem\CoreBundle\RequestState\Interfaces\RequestStateHashProviderInterface;
use ChameleonSystem\CoreBundle\Util\HashInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestStateHashProvider implements RequestStateHashProviderInterface
{
    /**
     * @var HashInterface
     */
    private $hashArray;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RequestStateElementProviderInterface[]
     */
    private $elementProviderList;
    /**
     * @var HashCalculationLockInterface
     */
    private $hashCalculationLock;

    /**
     * @param RequestStateElementProviderInterface[] $elementProviderList
     */
    public function __construct(
        HashInterface $hashArray,
        HashCalculationLockInterface $hashCalculationLock,
        RequestStack $requestStack,
        array $elementProviderList
    ) {
        $this->hashArray = $hashArray;
        $this->requestStack = $requestStack;
        $this->elementProviderList = $elementProviderList;
        $this->hashCalculationLock = $hashCalculationLock;
    }

    /**
     * {@inheritdoc}
     */
    public function getHash(?Request $request = null)
    {
        if (null === $request) {
            $request = $this->requestStack->getCurrentRequest();
        }

        if (null === $request) {
            return null;
        }

        if (false === $this->hashCalculationLock->lock()) {
            return null;
        }
        $elements = array_map(
            function (RequestStateElementProviderInterface $provider) use ($request) {
                return $provider->getStateElements($request);
            },
            $this->elementProviderList
        );
        $this->hashCalculationLock->release();

        $mergedElements = array_reduce(
            $elements,
            function (array $carry, array $element) {
                return array_merge($carry, $element);
            },
            []
        );

        return $this->hashArray->hash32($mergedElements);
    }
}
