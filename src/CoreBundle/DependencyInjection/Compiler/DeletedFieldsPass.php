<?php

namespace ChameleonSystem\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DeletedFieldsPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('chameleon_system_core.service.deleted_fields');
        $filename = $container->getParameter('deleted_fields');
        $filePath = __DIR__.'/../../Resources/config/'.$filename;

        if (false === file_exists($filePath)) {
            throw new \RuntimeException(sprintf('File "%s" does not exist.', $filePath));
        }

        $deletedFields = json_decode(file_get_contents($filePath), true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException('Error decoding JSON: '.json_last_error_msg());
        }

        $definition->replaceArgument('$deletedFields', $deletedFields);
    }
}
