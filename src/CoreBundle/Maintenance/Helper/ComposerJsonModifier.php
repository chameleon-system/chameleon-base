<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Maintenance\Helper;

use ChameleonSystem\CoreBundle\Maintenance\DataModel\ComposerData;

/**
 * ComposerJsonModifier A simple tool for modifying composer.json files.
 * Note that this file is for internal use only. No backwards compatibility promises are made for this class.
 */
class ComposerJsonModifier
{
    /**
     * @param string $path
     *
     * @return ComposerData
     */
    public function getComposerData($path)
    {
        return new ComposerData($path, json_decode(file_get_contents($path), true));
    }

    /**
     * @param ComposerData $composerData
     *
     * @return void
     */
    public function saveComposerFile(ComposerData $composerData)
    {
        file_put_contents($composerData->getFilePath(), json_encode($composerData->getData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param ComposerData $composerData
     * @param array        $newElements
     *
     * @return void
     */
    public function addAutoloadClassmap(ComposerData $composerData, array $newElements)
    {
        if (isset($composerData->getData()['autoload'])) {
            $autoload = $composerData->getData()['autoload'];
        } else {
            $autoload = [];
        }
        $classmap = isset($autoload['classmap']) ? $autoload['classmap'] : [];
        $classmap = array_merge($classmap, $newElements);
        $classmap = array_unique($classmap);
        sort($classmap);

        $autoload['classmap'] = $classmap;

        $this->addToSection($composerData, 'autoload', $autoload, true);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $newElements
     *
     * @return void
     */
    public function addRequire(ComposerData $composerData, array $newElements)
    {
        $this->addToSection($composerData, 'require', $newElements);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $newElements
     *
     * @return void
     */
    public function addRequireDev(ComposerData $composerData, array $newElements)
    {
        $this->addToSection($composerData, 'require-dev', $newElements);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $newElements
     *
     * @return void
     */
    public function addSuggest(ComposerData $composerData, array $newElements)
    {
        $this->addToSection($composerData, 'suggest', $newElements);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $newElements
     *
     * @return void
     */
    public function addScripts(ComposerData $composerData, array $newElements)
    {
        $this->addToSection($composerData, 'scripts', $newElements);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $newElements
     *
     * @return void
     */
    public function addConfig(ComposerData $composerData, array $newElements)
    {
        $this->addToSection($composerData, 'config', $newElements);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $newElements
     *
     * @return void
     */
    public function addExtra(ComposerData $composerData, array $newElements)
    {
        $this->addToSection($composerData, 'extra', $newElements);
    }

    /**
     * @param ComposerData $composerData
     * @param string       $section
     * @param array        $newElements
     * @param bool         $forceUpdate
     *
     * @return void
     */
    private function addToSection(ComposerData $composerData, $section, array $newElements, $forceUpdate = false)
    {
        $data = $composerData->getData();
        if (false === isset($data[$section])) {
            $data[$section] = [];
        }
        foreach ($newElements as $key => $value) {
            if (true === $forceUpdate || false === isset($data[$section][$key])) {
                $data[$section][$key] = $value;
            }
        }
        $composerData->setData($data);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $elementsToRemove
     *
     * @return void
     */
    public function removeRequire(ComposerData $composerData, array $elementsToRemove)
    {
        $this->removeFromSection($composerData, 'require', $elementsToRemove);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $elementsToRemove
     *
     * @return void
     */
    public function removeRequireDev(ComposerData $composerData, array $elementsToRemove)
    {
        $this->removeFromSection($composerData, 'require-dev', $elementsToRemove);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $elementsToRemove
     *
     * @return void
     */
    public function removeSuggest(ComposerData $composerData, array $elementsToRemove)
    {
        $this->removeFromSection($composerData, 'suggest', $elementsToRemove);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $elementsToRemove
     *
     * @return void
     */
    public function removeScripts(ComposerData $composerData, array $elementsToRemove)
    {
        $this->removeFromSection($composerData, 'scripts', $elementsToRemove);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $elementsToRemove
     *
     * @return void
     */
    public function removeConfig(ComposerData $composerData, array $elementsToRemove)
    {
        $this->removeFromSection($composerData, 'config', $elementsToRemove);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $elementsToRemove
     *
     * @return void
     */
    public function removeExtra(ComposerData $composerData, array $elementsToRemove)
    {
        $this->removeFromSection($composerData, 'extra', $elementsToRemove);
    }

    /**
     * @param ComposerData $composerData
     * @param string       $section
     * @param array        $elementsToRemove
     *
     * @return void
     */
    private function removeFromSection(ComposerData $composerData, $section, array $elementsToRemove)
    {
        $data = $composerData->getData();
        if (false === isset($data[$section])) {
            return;
        }
        $sectionData = $data[$section];
        foreach ($elementsToRemove as $elementToRemove) {
            unset($sectionData[$elementToRemove]);
        }
        if (0 === count($sectionData)) {
            unset($data[$section]);
        } else {
            $data[$section] = $sectionData;
        }
        $composerData->setData($data);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $elementsToAdd
     *
     * @return void
     */
    public function addPostInstallCommands(ComposerData $composerData, array $elementsToAdd)
    {
        $this->addScriptCommands($composerData, 'post-install-cmd', $elementsToAdd);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $elementsToAdd
     *
     * @return void
     */
    public function addPostUpdateCommands(ComposerData $composerData, array $elementsToAdd)
    {
        $this->addScriptCommands($composerData, 'post-update-cmd', $elementsToAdd);
    }

    /**
     * @param ComposerData $composerData
     * @param string       $commandType
     * @param array        $elementsToAdd
     *
     * @return void
     */
    private function addScriptCommands(ComposerData $composerData, $commandType, array $elementsToAdd)
    {
        $this->addToSection($composerData, 'scripts', [
            $commandType => [],
        ]);
        $data = $composerData->getData();
        foreach ($elementsToAdd as $elementToAdd) {
            if (true === in_array($elementToAdd, $data['scripts'][$commandType], true)) {
                continue;
            }
            $data['scripts'][$commandType][] = $elementToAdd;
        }
        $composerData->setData($data);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $elementsToRemove
     *
     * @return void
     */
    public function removePostInstallCommands(ComposerData $composerData, array $elementsToRemove)
    {
        $this->removeScriptCommands($composerData, 'post-install-cmd', $elementsToRemove);
    }

    /**
     * @param ComposerData $composerData
     * @param array        $elementsToRemove
     *
     * @return void
     */
    public function removePostUpdateCommands(ComposerData $composerData, array $elementsToRemove)
    {
        $this->removeScriptCommands($composerData, 'post-update-cmd', $elementsToRemove);
    }

    /**
     * @param ComposerData $composerData
     * @param string       $commandType
     * @param array        $elementsToRemove
     *
     * @return void
     */
    private function removeScriptCommands(ComposerData $composerData, $commandType, array $elementsToRemove)
    {
        $data = $composerData->getData();
        $data['scripts'][$commandType] = array_diff($data['scripts'][$commandType], $elementsToRemove);
        $composerData->setData($data);
    }

    /**
     * @param ComposerData $composerData
     * @param string       $key
     * @param string       $value
     * @param bool         $forceOverwrite
     *
     * @return void
     */
    public function addKey(ComposerData $composerData, $key, $value, $forceOverwrite = false)
    {
        $data = $composerData->getData();
        if (false === $forceOverwrite && isset($data[$key])) {
            return;
        }
        $data[$key] = $value;
        $composerData->setData($data);
    }

    /**
     * @param ComposerData $composerData
     * @param string       $key
     *
     * @return void
     */
    public function removeKey(ComposerData $composerData, $key)
    {
        $data = $composerData->getData();
        unset($data[$key]);
        $composerData->setData($data);
    }

    /**
     * @param ComposerData $composerData
     * @param string       $url
     *
     * @return void
     */
    public function removeRepository(ComposerData $composerData, $url)
    {
        $data = $composerData->getData();
        $repositories = $data['repositories'];
        foreach ($repositories as $index => $repository) {
            if (isset($repository['url']) && $repository['url'] === $url) {
                unset($repositories[$index]);
                $repositories = array_values($repositories);
                break;
            }
        }
        $data['repositories'] = $repositories;
        $composerData->setData($data);
    }
}
