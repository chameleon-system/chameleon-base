<?php

namespace ChameleonSystem\CoreBundle\Interfaces;

interface ResourceCollectorInterface
{
    /**
     * Checks if system is allowed to use resource collection.
     * Was used to disable resource collection in template engine.
     *
     * @return bool
     */
    public function IsAllowed();

    /**
     * Combine multiple resource files into one file.
     *
     * @param string $pageContent
     *
     * @return string - the processed page content
     */
    public function CollectExternalResources($pageContent);
}
