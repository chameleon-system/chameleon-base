<?php

namespace ChameleonSystem\CoreBundle\CronJob;

use Symfony\Component\DependencyInjection\ContainerInterface;
use TCMSCronJob;

class CronJobFactory implements CronJobFactoryInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var array
     */
    private $idList = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string[] $idList
     */
    public function setCronJobs(array $idList)
    {
        $this->idList = [];
        foreach ($idList as $id) {
            $this->idList[$id] = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function constructCronJob($identifier, array $data)
    {
        if (isset($this->idList[$identifier])) {
            if (false === $this->container->has($identifier)) {
                throw new \InvalidArgumentException(sprintf('Service with ID %s could not be loaded.', $identifier));
            }
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
         * @var TCMSCronJob $cronJob
         */
        $cronJob->LoadFromRow($data);

        return $cronJob;
    }
}
