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

class AutoclassesRequestCacheDataAccess implements AutoclassesDataAccessInterface
{
    /**
     * @var AutoclassesDataAccessInterface
     */
    private $decorated;
    /**
     * @var array
     */
    private $tableExtensionData;
    /**
     * @var array
     */
    private $fieldData;
    /**
     * @var \TCMSConfig
     */
    private $config;
    /**
     * @var array
     */
    private $tableOrderByData;
    /**
     * @var array
     */
    private $tableConfigData;

    public function __construct(AutoclassesDataAccessInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * @return void
     */
    public function clearCache()
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
    public function getTableExtensionData()
    {
        if (null === $this->tableExtensionData) {
            $this->tableExtensionData = $this->decorated->getTableExtensionData();
        }

        return $this->tableExtensionData;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldData()
    {
        if (null === $this->fieldData) {
            $this->fieldData = $this->decorated->getFieldData();
        }

        return $this->fieldData;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        if (null === $this->config) {
            $this->config = $this->decorated->getConfig();
        }

        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableOrderByData()
    {
        if (null === $this->tableOrderByData) {
            $this->tableOrderByData = $this->decorated->getTableOrderByData();
        }

        return $this->tableOrderByData;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableConfigData()
    {
        if (null === $this->tableConfigData) {
            $this->tableConfigData = $this->decorated->getTableConfigData();
        }

        return $this->tableConfigData;
    }
}
