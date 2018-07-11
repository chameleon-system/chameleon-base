<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\ModuleService;

use Symfony\Component\DependencyInjection\ContainerInterface;
use TModelBase;

class ModuleResolver
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var array
     */
    private $idlist = array();

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $id
     */
    public function addModule($id)
    {
        $this->idlist[] = $id;
    }

    /**
     * @param string $name
     *
     * @return TModelBase|null
     */
    public function getModule($name)
    {
        if (!$this->hasModule($name)) {
            return null;
        }

        return $this->container->get($name);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasModule($name)
    {
        return in_array($name, $this->idlist);
    }

    /**
     * @param string[] $ids
     */
    public function addModules(array $ids)
    {
        $this->idlist = array_merge($this->idlist, $ids);
    }

    /**
     * @return string[]
     */
    public function getModules()
    {
        return $this->idlist;
    }
}
