<?php

namespace ChameleonSystem\ExtranetBundle\EventListener;

class DelayFailedLoginAttemptListener
{
    /**
     * @var int
     */
    private $delayInMicroSeconds;

    public function __construct(int $delayInMicroSeconds = 2500000)
    {
        $this->delayInMicroSeconds = $delayInMicroSeconds;
    }

    public function addDelay(): void
    {
        \usleep($this->delayInMicroSeconds);
    }
}
