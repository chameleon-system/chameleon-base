<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\Bridge\Chameleon\SnippetChain;

use ChameleonSystem\ViewRenderer\Exception\DataAccessException;
use ChameleonSystem\ViewRenderer\SnippetChain\SnippetChainModifierDataAccessInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class SnippetChainModifierDataAccess implements SnippetChainModifierDataAccessInterface
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
     * {@inheritdoc}
     */
    public function getThemeData(array $themes = [])
    {
        $query = 'SELECT `id`, `snippet_chain` FROM `pkg_cms_theme`';
        $parameters = [];
        $parameterTypes = [];

        if (count($themes) > 0) {
            $query .= ' WHERE `id` in (:themeIdList)';
            $parameters['themeIdList'] = $themes;
            $parameterTypes['themeIdList'] = Connection::PARAM_STR_ARRAY;
        }

        try {
            $rawData = $this->databaseConnection->fetchAllAssociative($query, $parameters, $parameterTypes);
        } catch (DBALException $e) {
            throw new DataAccessException('Error while accessing database: '.$e->getMessage(), 0, $e);
        }
        $themeData = [];
        foreach ($rawData as $row) {
            $themeData[$row['id']] = $row['snippet_chain'];
        }

        return $themeData;
    }

    /**
     * {@inheritdoc}
     */
    public function updateSnippetChain($themeId, $snippetChain)
    {
        $updateQuery = 'UPDATE `pkg_cms_theme` SET `snippet_chain` = :snippetChain WHERE `id` = :themeId';
        try {
            $this->databaseConnection->executeQuery($updateQuery, [
                'snippetChain' => $snippetChain,
                'themeId' => $themeId,
            ]);
        } catch (DBALException $e) {
            throw new DataAccessException('Error while accessing database: '.$e->getMessage(), 0, $e);
        }
    }
}
