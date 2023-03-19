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
    public function generateAutoclassesMap(string $autoclassesDir): array
    {
        return $this->getClassesFromDirectory($autoclassesDir);
    }

    private function getClassesFromDirectory(string $rootPath, string $relativePath = ''): array
    {
        $classes = [];

        $dirPath = rtrim($rootPath . '/' . $relativePath, '/');

        if (false === is_dir($dirPath)) {
            return $classes;
        }

        foreach (scandir($dirPath) as $entry) {
            if (true === in_array($entry, ['.', '..',]) || '.' === substr($entry, 0, 1))
            {
                continue;
            }

            $entryPath = $dirPath . '/' . $entry;
            if (true === is_dir($entryPath)) {
                $subClasses = $this->getClassesFromDirectory($rootPath, $relativePath . '/' . $entry);

                if (false === empty($subClasses)) {
                    $classes = array_merge($classes, $subClasses);
                }
            } elseif ('.class.php' === substr($entry, -10)) {
                $className = substr($entry, 0, -10);
                $classes[$className] = $relativePath;
            }
        }

        return $classes;
    }
}
