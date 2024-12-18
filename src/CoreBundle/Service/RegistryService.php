<?php

namespace ChameleonSystem\CoreBundle\Service;

use Symfony\Component\HttpFoundation\ParameterBag;

class RegistryService
{
    private ParameterBag $registry;

    public function __construct()
    {
        $this->registry = new ParameterBag();
    }

    public function set(string $name, mixed $value): void
    {
        $this->registry->set($name, $value);
    }

    public function get(string $name): mixed
    {
        return $this->registry->get($name);
    }

    public function getVarNames(): array
    {
        return $this->registry->keys();
    }
}
