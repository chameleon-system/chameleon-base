<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

/**
 * @deprecated since 6.2.0 - use \ChameleonSystem\ViewRenderer\SnippetChain\SnippetChainModifier instead.
 */
class SnippetChainUtil
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    public function __construct(Connection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * Adds a path to the snippet chain of some or all themes.
     *
     * @param string   $pathToAdd
     * @param string   $afterThisPath the new path is added after this path (provide the complete line as shown in the theme view in the backend without \n).
     *                                If the path is null or not found in the chain, the $pathToAdd will be appended at the end
     * @param string[] $toTheseThemes an array of theme IDs. Add to all themes if empty.
     *
     * @throws DBALException
     */
    public function addToSnippetChain($pathToAdd, $afterThisPath = null, array $toTheseThemes = array())
    {
        $themes = $this->getThemeData($toTheseThemes);
        if (!empty($themes)) {
            $this->updateSnippetChain($themes, $pathToAdd, $afterThisPath);
        }
    }

    /**
     * @param string[] $toTheseThemes
     *
     * @return array
     *
     * @throws DBALException
     */
    private function getThemeData(array $toTheseThemes)
    {
        $query = 'SELECT `id`, `snippet_chain` FROM `pkg_cms_theme`';

        if (!empty($toTheseThemes)) {
            $toTheseThemes = array_map(array($this->databaseConnection, 'quote'), $toTheseThemes);
            $query .= ' WHERE `id` in ('.implode(',', $toTheseThemes).')';
        }
        $statement = $this->databaseConnection->executeQuery($query);
        $statement->execute();
        $themes = $statement->fetchAll();
        $statement->closeCursor();

        return $themes;
    }

    /**
     * @param array       $themes
     * @param string      $pathToAdd
     * @param string|null $afterThisPath
     */
    private function updateSnippetChain(array $themes, $pathToAdd, $afterThisPath)
    {
        $updateQuery = 'UPDATE `pkg_cms_theme` SET `snippet_chain` = :snippetChain WHERE `id` = :themeId';
        $updateStatement = $this->databaseConnection->prepare($updateQuery);

        foreach ($themes as $theme) {
            $themeId = $theme['id'];
            $snippetChain = $theme['snippet_chain'];
            $snippetChain = $this->getChangedSnippetChain($snippetChain, $pathToAdd, $afterThisPath);

            $updateStatement->execute(array(
                'themeId' => $themeId,
                'snippetChain' => $snippetChain,
            ));
        }
        $updateStatement->closeCursor();
    }

    /**
     * @param string      $snippetChain
     * @param string      $pathToAdd
     * @param string|null $afterThisPath
     *
     * @return string
     */
    private function getChangedSnippetChain($snippetChain, $pathToAdd, $afterThisPath)
    {
        if (null === $afterThisPath || false === strpos($snippetChain, $afterThisPath)) {
            $snippetChain .= "\n".$pathToAdd;
        } else {
            $snippetChain = str_replace($afterThisPath, $afterThisPath."\n".$pathToAdd, $snippetChain);
        }

        return $snippetChain;
    }
}
