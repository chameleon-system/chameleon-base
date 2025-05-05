<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\ClassManager;

class AutoclassesMapGenerator implements AutoclassesMapGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generateAutoclassesMap($autoclassesDir)
    {
        return $this->getClassesFromDirectory($autoclassesDir);
    }

    /**
     * @param string $rootPath
     * @param string $relativePath
     *
     * @return array
     */
    private function getClassesFromDirectory($rootPath, $relativePath = '')
    {
        $classes = [];
        $dirPath = $rootPath;
        if (!empty($relativePath)) {
            $dirPath .= '/'.$relativePath;
        }
        if (is_dir($dirPath)) {
            $d = dir($dirPath);
            while (false !== ($entry = $d->read())) {
                if ('.' === $entry || '..' === $entry || '.' === substr($entry, 0, 1)) {
                    continue;
                }
                if (is_dir($dirPath.'/'.$entry)) {
                    $newRelativePath = $relativePath;
                    if (!empty($newRelativePath)) {
                        $newRelativePath .= '/';
                    }
                    $newRelativePath .= $entry;
                    // parse sub-dir
                    $subClasses = $this->getClassesFromDirectory($rootPath, $newRelativePath);
                    if (count($subClasses) > 0) {
                        // add array
                        $classes = array_merge($classes, $subClasses);
                    }
                } elseif ('.class.php' === substr($entry, -10)) {
                    $className = substr($entry, 0, -10);
                    $classes[$className] = $relativePath;
                }
            }
            $d->close();
        }

        return $classes;
    }
}
