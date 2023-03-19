<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\DataAccess;

use TCMSConfig;

class AutoclassesRequestCacheDataAccess implements AutoclassesDataAccessInterface
{

    private AutoclassesDataAccessInterface $decorated;
    private ?array $tableExtensionData = null;
    private ?array $fieldData = null;
    private ?TCMSConfig $config = null;
    private ?array $tableOrderByData = null;
    private ?array $tableConfigData = null;


    public function __construct(AutoclassesDataAccessInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function clearCache(): void
    {
        $this->tableExtensionData = null;
        $this->fieldData = null;
        $this->config = null;
        $this->tableOrderByData = null;
        $this->tableConfigData = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableExtensionData(): array
    {
        if (null === $this->tableExtensionData) {
            $this->tableExtensionData = $this->decorated->getTableExtensionData();
        }

        return $this->tableExtensionData;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldData(): array
    {
        if (null === $this->fieldData) {
            $this->fieldData = $this->decorated->getFieldData();
        }

        return $this->fieldData;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): TCMSConfig
    {
        if (null === $this->config) {
            $this->config = $this->decorated->getConfig();
        }

        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableOrderByData(): array
    {
        if (null === $this->tableOrderByData) {
            $this->tableOrderByData = $this->decorated->getTableOrderByData();
        }

        return $this->tableOrderByData;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableConfigData(): array
    {
        if (null === $this->tableConfigData) {
            $this->tableConfigData = $this->decorated->getTableConfigData();
        }

        return $this->tableConfigData;
    }
}
