<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRenderer\SnippetChain;

use ChameleonSystem\ViewRenderer\Exception\DataAccessException;

class SnippetChainModifier
{
    /**
     * @var SnippetChainModifierDataAccessInterface
     */
    private $snippetChainModifierDataAccess;

    public function __construct(SnippetChainModifierDataAccessInterface $snippetChainModifierDataAccess)
    {
        $this->snippetChainModifierDataAccess = $snippetChainModifierDataAccess;
    }

    /**
     * Adds a path to the snippet chain of some or all themes.
     *
     * @param string $pathToAdd
     * @param string $afterThisPath The new path is added after this path (provide the complete line as shown in the
     *                              theme view in the backend without \n).
     *                              Use ^ to add the new $pathToAdd as first element.
     *                              If the path is null or not found in the chain the $pathToAdd will be appended at the end.
     * @param string[] $toTheseThemes an array of theme IDs for which to add the element. Add to all themes if empty.
     *
     * @return void
     *
     * @throws DataAccessException
     */
    public function addToSnippetChain($pathToAdd, $afterThisPath = null, array $toTheseThemes = [])
    {
        if ('' === trim($pathToAdd)) {
            return;
        }
        $themes = $this->snippetChainModifierDataAccess->getThemeData($toTheseThemes);

        foreach ($themes as $themeId => $snippetChain) {
            $newSnippetChain = $this->getSnippetChainWithElementAdded($snippetChain, $pathToAdd, $afterThisPath);
            $this->snippetChainModifierDataAccess->updateSnippetChain($themeId, $newSnippetChain);
        }
    }

    /**
     * @param string $snippetChain
     * @param string $pathToAdd
     * @param string|null $afterThisPath
     *
     * @return string
     */
    private function getSnippetChainWithElementAdded($snippetChain, $pathToAdd, $afterThisPath)
    {
        if ('' === $snippetChain) {
            return $pathToAdd;
        }

        if ('^' === $afterThisPath) {
            $snippetChain = $pathToAdd."\n".$snippetChain;
        } else {
            $quotedAfterThisPath = preg_quote($afterThisPath, '#');
            $pattern = '#(\s+|^)'.$quotedAfterThisPath.'(\s+|$)#';
            if (null === $afterThisPath || 1 !== preg_match($pattern, $snippetChain)) {
                $snippetChain .= "\n$pathToAdd";
            } else {
                $snippetChain = preg_replace($pattern, "\$1$afterThisPath\n$pathToAdd\$2", $snippetChain);
            }
        }

        return $this->optimizeSnippetChainString($snippetChain);
    }

    /**
     * @param string $snippetChain
     *
     * @return string
     */
    private function optimizeSnippetChainString($snippetChain)
    {
        $snippetChain = preg_replace('#\s+#', "\n", $snippetChain);
        $snippetChain = trim($snippetChain);

        return $snippetChain;
    }

    /**
     * Removes a path from the snippet chain of some or all themes.
     *
     * @param string $pathToRemove
     * @param string[] $fromTheseThemes an array of theme IDs from which to remove the element. Remove from all themes if empty.
     *
     * @return void
     *
     * @throws DataAccessException
     */
    public function removeFromSnippetChain($pathToRemove, array $fromTheseThemes = [])
    {
        if ('' === trim($pathToRemove)) {
            return;
        }
        $themes = $this->snippetChainModifierDataAccess->getThemeData($fromTheseThemes);
        foreach ($themes as $themeId => $snippetChain) {
            $newSnippetChain = $this->getSnippetChainWithElementRemoved($snippetChain, $pathToRemove);
            if ($newSnippetChain !== $snippetChain) {
                $this->snippetChainModifierDataAccess->updateSnippetChain($themeId, $newSnippetChain);
            }
        }
    }

    /**
     * @param string $snippetChain
     * @param string $pathToRemove
     *
     * @return string
     */
    private function getSnippetChainWithElementRemoved($snippetChain, $pathToRemove)
    {
        $quotedAfterThisPath = preg_quote($pathToRemove, '#');
        $pattern = '#(\s+|^)'.$quotedAfterThisPath.'(\s+|$)#';
        $snippetChain = preg_replace($pattern, "\n", $snippetChain);

        return $this->optimizeSnippetChainString($snippetChain);
    }
}
