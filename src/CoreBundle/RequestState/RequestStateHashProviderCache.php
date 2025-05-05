<?php

namespace ChameleonSystem\CoreBundle\RequestState;

use ChameleonSystem\CoreBundle\RequestState\Interfaces\RequestStateHashProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\Event;

class RequestStateHashProviderCache implements RequestStateHashProviderInterface
{
    /**
     * @var string|null
     */
    private $cache;
    /**
     * @var RequestStateHashProviderInterface
     */
    private $subject;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStateHashProviderInterface $subject, RequestStack $requestStack)
    {
        $this->subject = $subject;
        $this->requestStack = $requestStack;
    }

    /**
     * The state hash will only be cached once the session has been started since we only know the final state
     * after session start.
     * {@inheritdoc}
     */
    public function getHash(?Request $request = null)
    {
        if (null !== $this->cache) {
            return $this->cache;
        }

        if (null === $request) {
            $request = $this->requestStack->getCurrentRequest();
        }

        if (null === $request) {
            return null;
        }

        $hash = $this->subject->getHash($request);
        if (null === $hash) {
            return null;
        }

        $allowCaching = $request->hasSession();
        $allowCaching = $allowCaching && $request->getSession()->isStarted();

        if ($allowCaching) {
            $this->cache = $hash;
        }

        return $hash;
    }

    /**
     * @return void
     */
    public function onStateDataChanged(Event $event)
    {
        $this->cache = null;
    }
}
