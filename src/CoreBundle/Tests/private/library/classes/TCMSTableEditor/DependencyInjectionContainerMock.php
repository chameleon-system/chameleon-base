<?php

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DependencyInjectionContainerMock implements ContainerInterface
{

    public $services = [];

    public function get($id) {
        if (!$this->has($id)) {
            throw new class('Requested a non-existent service ' . $id) extends \Exception implements NotFoundExceptionInterface {};
        }

        return $this->services[$id];
    }

    public function has($id) {
        return array_key_exists($id, $this->services);
    }
}
