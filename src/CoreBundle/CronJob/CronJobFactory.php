<?php

namespace ChameleonSystem\CoreBundle\CronJob;

use Psr\Container\ContainerInterface;

class CronJobFactory implements CronJobFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function constructCronJob($identifier, array $data)
    {
        if (true === $this->container->has($identifier)) {
            $cronJob = $this->container->get($identifier);
        } else {
            if (false === \class_exists($identifier)) {
                throw new \InvalidArgumentException(sprintf('Given identifier %s is neither a valid service ID nor a valid class name.', $identifier));
            }
            $cronJob = new $identifier();
        }
        if (false === $cronJob instanceof \TCMSCronJob) {
            throw new \InvalidArgumentException(sprintf('Class for given identifier %s does not extend TCMSCronJob', $identifier));
        }
        /**
         * @var \TCMSCronJob $cronJob
         */
        $cronJob->LoadFromRow($data);

        return $cronJob;
    }
}
