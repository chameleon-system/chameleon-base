<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddCronJobsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $cronJobFactoryDefinition = $container->getDefinition('chameleon_system_core.cronjob.cronjob_factory');
        $cronJobServiceIds = array_keys($container->findTaggedServiceIds('chameleon_system.cronjob'));

        foreach ($cronJobServiceIds as $cronjobServiceId) {
            $cronJobDefinition = $container->getDefinition($cronjobServiceId);
            if ($cronJobDefinition->isShared()) {
                throw new \LogicException('Chameleon cron jobs must not be shared service instances. This cron job is shared: '.$cronjobServiceId);
            }
        }

        $cronJobFactoryDefinition->addMethodCall('setCronJobs', array($cronJobServiceIds));
    }
}
