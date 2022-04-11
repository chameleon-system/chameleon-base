<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ExtranetBundle\DependencyInjection\Compiler;

use ChameleonSystem\ExtranetBundle\ExtranetEvents;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddSessionMigrationEventsPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (false === SECURITY_REGENERATE_SESSION_ON_USER_CHANGE) {
            return;
        }
        if (false === $container->hasDefinition('chameleon_system_core.event_listener.migrate_session_listener')) {
            return;
        }

        $definition = $container->getDefinition('chameleon_system_core.event_listener.migrate_session_listener');
        $definition->addTag('kernel.event_listener', [
            'event' => ExtranetEvents::USER_LOGIN_SUCCESS,
            'method' => 'migrateSession',
        ]);
        $definition->addTag('kernel.event_listener', [
            'event' => ExtranetEvents::USER_LOGOUT_SUCCESS,
            'method' => 'migrateSession',
        ]);
    }
}
