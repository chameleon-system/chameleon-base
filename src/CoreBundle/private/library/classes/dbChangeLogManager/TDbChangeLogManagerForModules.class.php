<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TDbChangeLogManagerForModules
{
    /**
     * @var string|null
     */
    private $className;
    /**
     * @var TdbCmsTplModule
     */
    private $config;

    /**
     * @param string $moduleClassName
     *
     * @throws ErrorException
     */
    public function __construct($moduleClassName)
    {
        $this->className = $moduleClassName;
        $this->getConfig();
    }

    /**
     * @return int|string|null
     *
     * @throws ErrorException
     */
    public function getId()
    {
        return $this->getConfig()->id;
    }

    /**
     * @return ViewMapperConfig
     *
     * @throws ErrorException
     */
    public function getMapperConfig()
    {
        return $this->getConfig()->getViewMapperConfig();
    }

    public function updateMapperConfig(ViewMapperConfig $mapperConfig)
    {
        $data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
            ->setFields([
                'view_mapper_config' => $mapperConfig->getAsString(),
            ])
            ->setWhereEquals([
                'id' => $this->getConfig()->id,
            ]);
        TCMSLogChange::update(__LINE__, $data);
        $this->config = null;
    }

    /**
     * @return TdbCmsTplModule
     *
     * @throws ErrorException
     */
    private function getConfig()
    {
        if (null !== $this->config) {
            return $this->config;
        }
        $this->config = TdbCmsTplModule::GetNewInstance();
        if (false === $this->config->LoadFromField('classname', $this->className)) {
            throw new ErrorException("unable to load module with classname = [{$this->className}]", 0, E_USER_ERROR, __FILE__, __LINE__);
        }

        return $this->config;
    }

    /**
     * @param string $mapperChainName
     * @param string $newMapper
     * @param string|null $positionAfter
     *
     * @throws ErrorException
     */
    public function addMapperToMapperChain($mapperChainName, $newMapper, $positionAfter = null)
    {
        $mapperChainConfig = $this->getConfig()->getMapperChainConfig();
        $mapperChainConfig->addMapperToChain($mapperChainName, $newMapper, $positionAfter);
        $newMapperChain = $mapperChainConfig->getAsString();
        $this->commitMapperChainConfigurationToDatabase($newMapperChain);
    }

    /**
     * @param string $mapperChainName
     * @param string $mapperName
     *
     * @throws ErrorException
     */
    public function removeMapperFromMapperChain($mapperChainName, $mapperName)
    {
        $mapperChainConfig = $this->getConfig()->getMapperChainConfig();
        $mapperChainConfig->removeMapperFromMapperChain($mapperChainName, $mapperName);
        $newMapperChain = $mapperChainConfig->getAsString();
        $this->commitMapperChainConfigurationToDatabase($newMapperChain);
    }

    /**
     * @param string $oldMapperName
     * @param string $newMapperName
     * @param string|null $mapperChainName
     *
     * @throws ErrorException
     */
    public function replaceMapperInMapperChain($oldMapperName, $newMapperName, $mapperChainName = null)
    {
        $mapperChainConfig = $this->getConfig()->getMapperChainConfig();
        $hasChanges = $mapperChainConfig->replaceMapper($oldMapperName, $newMapperName, $mapperChainName);
        if (true === $hasChanges) {
            $newMapperChain = $mapperChainConfig->getAsString();
            $this->commitMapperChainConfigurationToDatabase($newMapperChain);
        }
    }

    /**
     * @param string $mapperChainName
     *
     * @throws ErrorException
     */
    public function addMapperChain($mapperChainName, array $mapperList)
    {
        $mapperChainConfig = $this->getConfig()->getMapperChainConfig();
        $mapperChainConfig->addMapperChain($mapperChainName, $mapperList);
        $newMapperChain = $mapperChainConfig->getAsString();
        $this->commitMapperChainConfigurationToDatabase($newMapperChain);
    }

    /**
     * @param string $mapperChainName
     *
     * @throws ErrorException
     */
    public function removeMapperChain($mapperChainName)
    {
        $mapperChainConfig = $this->getConfig()->getMapperChainConfig();
        $mapperChainConfig->removeMapperChain($mapperChainName);
        $newMapperChain = $mapperChainConfig->getAsString();
        $this->commitMapperChainConfigurationToDatabase($newMapperChain);
    }

    /**
     * @param string $newMapperChain
     */
    protected function commitMapperChainConfigurationToDatabase($newMapperChain)
    {
        $query = 'UPDATE `cms_tpl_module` SET `mapper_chain` = :newMapperChain WHERE `id` = :id';
        $parameter = ['newMapperChain' => $newMapperChain, 'id' => $this->getConfig()->id];
        TCMSLogChange::RunQuery(
            __LINE__,
            $query,
            $parameter
        );
        $this->config = null;
    }

    public function delete()
    {
        $tableEditorManager = TTools::GetTableEditorManager('cms_tpl_module', $this->getId());
        $tableEditorManager->AllowDeleteByAll(true);
        $tableEditorManager->Delete($this->getId());
    }
}
